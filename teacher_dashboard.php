<?php
require_once 'config/db.php';
checkRole(['teacher']);

// Get teacher info
$teacher = $conn->query("SELECT * FROM teachers WHERE user_id = " . $_SESSION['user_id'])->fetch_assoc();

// Get statistics
$total_students = $conn->query("SELECT COUNT(DISTINCT student_id) as count FROM attendance WHERE teacher_id = " . $teacher['id'])->fetch_assoc()['count'];

// Today's attendance
$today_attendance = $conn->query("
    SELECT COUNT(*) as total,
    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
    FROM attendance 
    WHERE teacher_id = " . $teacher['id'] . " 
    AND date = CURDATE()
")->fetch_assoc();

// This month's attendance percentage
$monthly_stats = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
    FROM attendance 
    WHERE teacher_id = " . $teacher['id'] . "
    AND MONTH(date) = MONTH(CURRENT_DATE()) 
    AND YEAR(date) = YEAR(CURRENT_DATE())
")->fetch_assoc();

$monthly_percentage = 0;
if ($monthly_stats['total'] > 0) {
    $monthly_percentage = round(($monthly_stats['present'] / $monthly_stats['total']) * 100, 2);
}

// Recent attendance
$recent_attendance = $conn->query("
    SELECT a.*, s.name as student_name, s.roll_no
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    WHERE a.teacher_id = " . $teacher['id'] . "
    ORDER BY a.date DESC, a.created_at DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Teacher Dashboard</h1>
            <p>Welcome back, <?php echo $_SESSION['name']; ?>! Subject: <?php echo $teacher['subject']; ?></p>
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
                <div class="stat-icon">✅</div>
                <div class="stat-details">
                    <h3><?php echo $today_attendance['present'] ?? 0; ?></h3>
                    <p>Present Today</p>
                </div>
            </div>
            
            <div class="stat-card stat-info">
                <div class="stat-icon">📊</div>
                <div class="stat-details">
                    <h3><?php echo $monthly_percentage; ?>%</h3>
                    <p>Monthly Attendance</p>
                </div>
            </div>
            
            <div class="stat-card stat-warning">
                <div class="stat-icon">📚</div>
                <div class="stat-details">
                    <h3><?php echo $teacher['subject']; ?></h3>
                    <p>Subject</p>
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
                <a href="mark_attendance.php" class="btn btn-primary">Mark Attendance</a>
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