<?php
require_once '../config/db.php';
checkRole(['admin']);

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        addTeacher();
        break;
    case 'get':
        getTeacher();
        break;
    case 'update':
        updateTeacher();
        break;
    case 'delete':
        deleteTeacher();
        break;
    default:
        jsonResponse(false, 'Invalid action');
}

function addTeacher() {
    global $conn;
    
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $phone = sanitize($_POST['phone'] ?? '');
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        jsonResponse(false, 'Email already exists');
    }
    
    $conn->begin_transaction();
    
    try {
        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (name, role, email, password) VALUES (?, 'teacher', ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        $stmt->execute();
        $user_id = $conn->insert_id;
        
        // Insert into teachers table
        $stmt = $conn->prepare("INSERT INTO teachers (user_id, name, subject, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $name, $subject, $email, $phone);
        $stmt->execute();
        
        $conn->commit();
        jsonResponse(true, 'Teacher added successfully');
    } catch (Exception $e) {
        $conn->rollback();
        jsonResponse(false, 'Error adding teacher: ' . $e->getMessage());
    }
}

function getTeacher() {
    global $conn;
    
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT t.*, u.email FROM teachers t LEFT JOIN users u ON t.user_id = u.id WHERE t.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        jsonResponse(true, 'Teacher found', $result->fetch_assoc());
    } else {
        jsonResponse(false, 'Teacher not found');
    }
}

function updateTeacher() {
    global $conn;
    
    $id = intval($_POST['teacher_id']);
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $phone = sanitize($_POST['phone'] ?? '');
    
    // Get user_id
    $result = $conn->query("SELECT user_id FROM teachers WHERE id = $id");
    if ($result->num_rows === 0) {
        jsonResponse(false, 'Teacher not found');
    }
    $user_id = $result->fetch_assoc()['user_id'];
    
    $conn->begin_transaction();
    
    try {
        // Update users table
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $stmt->execute();
        
        // Update teachers table
        $stmt = $conn->prepare("UPDATE teachers SET name = ?, subject = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $subject, $email, $phone, $id);
        $stmt->execute();
        
        $conn->commit();
        jsonResponse(true, 'Teacher updated successfully');
    } catch (Exception $e) {
        $conn->rollback();
        jsonResponse(false, 'Error updating teacher: ' . $e->getMessage());
    }
}

function deleteTeacher() {
    global $conn;
    
    $id = intval($_GET['id']);
    
    // Get user_id
    $result = $conn->query("SELECT user_id FROM teachers WHERE id = $id");
    if ($result->num_rows === 0) {
        jsonResponse(false, 'Teacher not found');
    }
    $user_id = $result->fetch_assoc()['user_id'];
    
    // Delete user (cascade will delete teacher record)
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Teacher deleted successfully');
    } else {
        jsonResponse(false, 'Error deleting teacher');
    }
}
?>