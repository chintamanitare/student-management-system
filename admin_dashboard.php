<?php
require_once 'config/db.php';
checkRole(['admin']);

// Get statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_teachers = $conn->query("SELECT COUNT(*) as count FROM teachers")->fetch_assoc()['count'];

// Calculate attendance percentage
$attendance_stats = $conn->query("
    SELECT 
        COUNT(*) as total_records,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count
    FROM attendance
    WHERE MONTH(date) = MONTH(CURRENT_DATE()) 
    AND YEAR(date) = YEAR(CURRENT_DATE())
")->fetch_assoc();

$attendance_percentage = 0;
if ($attendance_stats['total_records'] > 0) {
    $attendance_percentage = round(($attendance_stats['present_count'] / $attendance_stats['total_records']) * 100, 2);
}

// Recent attendance records
$recent_attendance = $conn->query("
    SELECT a.*, s.name as student_name, s.roll_no, t.name as teacher_name, t.subject
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    JOIN teachers t ON a.teacher_id = t.id
    ORDER BY a.date DESC, a.created_at DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo $_SESSION['name']; ?>!</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-icon">👨‍🎓</div>
                <div class="stat-details">
                    <h3><?php echo $total_students; ?></h3>
                    <p>Total Students</p>
                </div>
            </div>
            
            <div class="stat-card stat-success">
                <div class="stat-icon">👨‍🏫</div>
                <div class="stat-details">
                    <h3><?php echo $total_teachers; ?></h3>
                    <p>Total Teachers</p>
                </div>
            </div>
            
            <div class="stat-card stat-info">
                <div class="stat-icon">📊</div>
                <div class="stat-details">
                    <h3><?php echo $attendance_percentage; ?>%</h3>
                    <p>Attendance (This Month)</p>
                </div>
            </div>
            
            <div class="stat-card stat-warning">
                <div class="stat-icon">📅</div>
                <div class="stat-details">
                    <h3><?php echo $attendance_stats['total_records']; ?></h3>
                    <p>Total Records</p>
                </div>
            </div>
        </div>
        
        <div class="chart-container">
            <h2>Attendance Trends</h2>
            <canvas id="attendanceChart"></canvas>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Recent Attendance Records</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Roll No</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $recent_attendance->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                                <td><?php echo $row['student_name']; ?></td>
                                <td><?php echo $row['roll_no']; ?></td>
                                <td><?php echo $row['subject']; ?></td>
                                <td><?php echo $row['teacher_name']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['status'] === 'present' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script src="js/charts.js"></script>
</body>
</html>