<?php
require_once '../config/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        checkRole(['admin']);
        addAnnouncement();
        break;
    case 'delete':
        checkRole(['admin']);
        deleteAnnouncement();
        break;
    default:
        jsonResponse(false, 'Invalid action');
}

function addAnnouncement() {
    global $conn;
    
    $title = sanitize($_POST['title']);
    $message = sanitize($_POST['message']);
    $target_role = sanitize($_POST['target_role']);
    $created_by = $_SESSION['user_id'];
    
    if (empty($title) || empty($message)) {
        jsonResponse(false, 'Title and message are required');
    }
    
    if (!in_array($target_role, ['all', 'student', 'teacher'])) {
        jsonResponse(false, 'Invalid target role');
    }
    
    $stmt = $conn->prepare("INSERT INTO announcements (title, message, created_by, target_role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $title, $message, $created_by, $target_role);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Announcement posted successfully');
    } else {
        jsonResponse(false, 'Error posting announcement');
    }
}

function deleteAnnouncement() {
    global $conn;
    
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Announcement deleted successfully');
    } else {
        jsonResponse(false, 'Error deleting announcement');
    }
}
?>