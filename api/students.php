<?php
require_once '../config/db.php';
checkRole(['admin']);

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        addStudent();
        break;
    case 'get':
        getStudent();
        break;
    case 'update':
        updateStudent();
        break;
    case 'delete':
        deleteStudent();
        break;
    default:
        jsonResponse(false, 'Invalid action');
}

function addStudent() {
    global $conn;
    
    $name = sanitize($_POST['name']);
    $roll_no = sanitize($_POST['roll_no']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone'] ?? '');
    $course = sanitize($_POST['course']);
    $batch = sanitize($_POST['batch']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email or roll_no already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? UNION SELECT id FROM students WHERE roll_no = ?");
    $check->bind_param("ss", $email, $roll_no);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        jsonResponse(false, 'Email or Roll Number already exists');
    }
    
    $conn->begin_transaction();
    
    try {
        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (name, role, email, password) VALUES (?, 'student', ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        $stmt->execute();
        $user_id = $conn->insert_id;
        
        // Insert into students table
        $stmt = $conn->prepare("INSERT INTO students (user_id, name, roll_no, course, batch, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $name, $roll_no, $course, $batch, $phone);
        $stmt->execute();
        
        $conn->commit();
        jsonResponse(true, 'Student added successfully');
    } catch (Exception $e) {
        $conn->rollback();
        jsonResponse(false, 'Error adding student: ' . $e->getMessage());
    }
}

function getStudent() {
    global $conn;
    
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT s.*, u.email FROM students s LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        jsonResponse(true, 'Student found', $result->fetch_assoc());
    } else {
        jsonResponse(false, 'Student not found');
    }
}

function updateStudent() {
    global $conn;
    
    $id = intval($_POST['student_id']);
    $name = sanitize($_POST['name']);
    $roll_no = sanitize($_POST['roll_no']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone'] ?? '');
    $course = sanitize($_POST['course']);
    $batch = sanitize($_POST['batch']);
    
    // Get user_id
    $result = $conn->query("SELECT user_id FROM students WHERE id = $id");
    if ($result->num_rows === 0) {
        jsonResponse(false, 'Student not found');
    }
    $user_id = $result->fetch_assoc()['user_id'];
    
    $conn->begin_transaction();
    
    try {
        // Update users table
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $stmt->execute();
        
        // Update students table
        $stmt = $conn->prepare("UPDATE students SET name = ?, roll_no = ?, course = ?, batch = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $roll_no, $course, $batch, $phone, $id);
        $stmt->execute();
        
        $conn->commit();
        jsonResponse(true, 'Student updated successfully');
    } catch (Exception $e) {
        $conn->rollback();
        jsonResponse(false, 'Error updating student: ' . $e->getMessage());
    }
}

function deleteStudent() {
    global $conn;
    
    $id = intval($_GET['id']);
    
    // Get user_id
    $result = $conn->query("SELECT user_id FROM students WHERE id = $id");
    if ($result->num_rows === 0) {
        jsonResponse(false, 'Student not found');
    }
    $user_id = $result->fetch_assoc()['user_id'];
    
    // Delete user (cascade will delete student record)
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Student deleted successfully');
    } else {
        jsonResponse(false, 'Error deleting student');
    }
}
?>