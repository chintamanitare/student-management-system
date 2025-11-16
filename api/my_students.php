<?php
require_once '../config/db.php';
checkRole(['teacher']);

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_details':
        getStudentDetails();
        break;
    default:
        jsonResponse(false, 'Invalid action');
}

function getStudentDetails() {
    global $conn;
    
    $student_id = intval($_GET['student_id']);
    $teacher_id = intval($_GET['teacher_id']);
    
    // Get student info
    $student_query = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($student_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student_result = $stmt->get_result();
    
    if ($student_result->num_rows === 0) {
        jsonResponse(false, 'Student not found');
    }
    
    $student = $student_result->fetch_assoc();
    
    // Get attendance records for this student with this teacher
    $attendance_query = "
        SELECT a.*, t.subject
        FROM attendance a
        JOIN teachers t ON a.teacher_id = t.id
        WHERE a.student_id = ? AND a.teacher_id = ?
        ORDER BY a.date DESC
        LIMIT 50
    ";
    
    $stmt = $conn->prepare($attendance_query);
    $stmt->bind_param("ii", $student_id, $teacher_id);
    $stmt->execute();
    $attendance_result = $stmt->get_result();
    
    $attendance = [];
    while ($row = $attendance_result->fetch_assoc()) {
        $attendance[] = $row;
    }
    
    // Get statistics
    $stats_query = "
        SELECT 
            COUNT(*) as total_classes,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
            ROUND((SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*) * 100), 2) as attendance_percentage
        FROM attendance
        WHERE student_id = ? AND teacher_id = ?
    ";
    
    $stmt = $conn->prepare($stats_query);
    $stmt->bind_param("ii", $student_id, $teacher_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
    jsonResponse(true, 'Student details retrieved', [
        'student' => $student,
        'attendance' => $attendance,
        'stats' => $stats
    ]);
}
?>