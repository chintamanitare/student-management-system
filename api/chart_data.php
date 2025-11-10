<?php
require_once '../config/db.php';
checkRole(['admin', 'teacher']);

header('Content-Type: application/json');

// Get attendance data for the last 7 days
$query = "
    SELECT 
        DATE(date) as attendance_date,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count
    FROM attendance
    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(date)
    ORDER BY date ASC
";

$result = $conn->query($query);

$labels = [];
$present = [];
$absent = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = date('M d', strtotime($row['attendance_date']));
    $present[] = (int)$row['present_count'];
    $absent[] = (int)$row['absent_count'];
}

// Fill missing dates with zeros if needed
if (count($labels) < 7) {
    for ($i = 6; $i >= 0; $i--) {
        $date = date('M d', strtotime("-$i days"));
        if (!in_array($date, $labels)) {
            array_unshift($labels, $date);
            array_unshift($present, 0);
            array_unshift($absent, 0);
        }
    }
}

jsonResponse(true, 'Chart data loaded', [
    'labels' => $labels,
    'present' => $present,
    'absent' => $absent
]);
?>