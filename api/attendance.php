<?php
require_once '../config/db.php';
checkRole(['teacher', 'admin']);

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'submit':
        submitAttendance();
        break;
    case 'get_report':
        getAttendanceReport();
        break;
    case 'export_csv':
        exportCSV();
        break;
    default:
        jsonResponse(false, 'Invalid action');
}

function submitAttendance() {
    global $conn;
    
    $date = sanitize($_POST['date']);
    $teacher_id = intval($_POST['teacher_id']);
    $subject = sanitize($_POST['subject']);
    $attendance = $_POST['attendance'];
    
    if (empty($attendance)) {
        jsonResponse(false, 'No attendance data provided');
    }
    
    $conn->begin_transaction();
    
    try {
        $success_count = 0;
        
        foreach ($attendance as $student_id => $status) {
            $student_id = intval($student_id);
            
            // Check if attendance already exists
            $check = $conn->prepare("SELECT id FROM attendance WHERE student_id = ? AND date = ? AND subject = ?");
            $check->bind_param("iss", $student_id, $date, $subject);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                // Update existing record
                $stmt = $conn->prepare("UPDATE attendance SET teacher_id = ?, status = ? WHERE student_id = ? AND date = ? AND subject = ?");
                $stmt->bind_param("isiss", $teacher_id, $status, $student_id, $date, $subject);
            } else {
                // Insert new record
                $stmt = $conn->prepare("INSERT INTO attendance (student_id, teacher_id, date, subject, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisss", $student_id, $teacher_id, $date, $subject, $status);
            }
            
            if ($stmt->execute()) {
                $success_count++;
            }
        }
        
        $conn->commit();
        jsonResponse(true, "Attendance marked successfully for $success_count students");
    } catch (Exception $e) {
        $conn->rollback();
        jsonResponse(false, 'Error submitting attendance: ' . $e->getMessage());
    }
}

function getAttendanceReport() {
    global $conn;
    
    $student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;
    $date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : null;
    $date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : null;
    $subject = isset($_GET['subject']) ? sanitize($_GET['subject']) : null;
    
    $query = "SELECT a.*, s.name as student_name, s.roll_no, s.course, s.batch, 
              t.name as teacher_name, t.subject as teacher_subject
              FROM attendance a
              JOIN students s ON a.student_id = s.id
              JOIN teachers t ON a.teacher_id = t.id
              WHERE 1=1";
    
    if ($student_id) {
        $query .= " AND a.student_id = $student_id";
    }
    if ($date_from) {
        $query .= " AND a.date >= '$date_from'";
    }
    if ($date_to) {
        $query .= " AND a.date <= '$date_to'";
    }
    if ($subject) {
        $query .= " AND a.subject LIKE '%$subject%'";
    }
    
    $query .= " ORDER BY a.date DESC, s.roll_no";
    
    $result = $conn->query($query);
    $records = [];
    
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    
    jsonResponse(true, 'Records fetched', $records);
}

function exportCSV() {
    global $conn;
    
    $student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;
    $date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : null;
    $date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : null;
    $subject = isset($_GET['subject']) ? sanitize($_GET['subject']) : null;
    
    $query = "SELECT a.date, s.roll_no, s.name as student_name, s.course, s.batch,
              a.subject, t.name as teacher_name, a.status
              FROM attendance a
              JOIN students s ON a.student_id = s.id
              JOIN teachers t ON a.teacher_id = t.id
              WHERE 1=1";
    
    if ($student_id) {
        $query .= " AND a.student_id = $student_id";
    }
    if ($date_from) {
        $query .= " AND a.date >= '$date_from'";
    }
    if ($date_to) {
        $query .= " AND a.date <= '$date_to'";
    }
    if ($subject) {
        $query .= " AND a.subject LIKE '%$subject%'";
    }
    
    $query .= " ORDER BY a.date DESC, s.roll_no";
    
    $result = $conn->query($query);
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date', 'Roll No', 'Student Name', 'Course', 'Batch', 'Subject', 'Teacher', 'Status']);
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}
?>