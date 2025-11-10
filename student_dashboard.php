<?php
require_once 'config/db.php';
checkRole(['student']);

// Get student info
$student = $conn->query("SELECT * FROM students WHERE user_id = " . $_SESSION['user_id'])->fetch_assoc();

// Get attendance statistics
$total_classes = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE student_id = " . $student['id'])->fetch_assoc()['count'];
$present_count = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE student_id = " . $student['id'] . " AND status = 'present'")->fetch_assoc()['count'];
$absent_count = $total_classes - $present_count;

$attendance_percentage = 0;
if ($total_classes > 0) {
    $attendance_percentage = round(($present_count / $total_classes) * 100, 2);
}

// Monthly attendance
$monthly_stats = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
    FROM attendance 
    WHERE student_id = " . $student['id'] . "
    AND MONTH(date) = MONTH(CURRENT_DATE()) 
    AND YEAR(date) = YEAR(CURRENT_DATE())
")->fetch_assoc();

// Recent attendance
$recent_attendance = $conn->query("
    SELECT a.*, t.name as teacher_name
    FROM attendance a
    JOIN teachers t ON a.teacher_id = t.id
    WHERE a.student_id = " . $student['id'] . "
    ORDER BY a.date DESC
    LIMIT 10
");

// Recent announcements
$announcements = $conn->query("
    SELECT a.*, u.name as created_by_name
    FROM announcements a
    JOIN users u ON a.created_by = u.id
    WHERE a.target_role IN ('all', 'student')
    ORDER BY a.created_at DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Student Dashboard</h1>
            <p>Welcome back, <?php echo $_SESSION['name']; ?>!</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Personal Information</h2>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Name:</strong> <?php echo $student['name']; ?>
                    </div>
                    <div class="info-item">
                        <strong>Roll Number:</strong> <?php echo $student['roll_no']; ?>
                    </div>
                    <div class="info-item">
                        <strong>Course:</strong> <?php echo $student['course']; ?>
                    </div>
                    <div class="info-item">
                        <strong>Batch:</strong> <?php echo $student['batch']; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-icon">📚</div>
                <div class="stat-details">
                    <h3><?php echo $total_classes; ?></h3>
                    <p>Total Classes</p>
                </div>
            </div>
            
            <div class="stat-card stat-success">
                <div class="stat-icon">✅</div>
                <div class="stat-details">
                    <h3><?php echo $present_count; ?></h3>
                    <p>Present</p>
                </div>
            </div>
            
            <div class="stat-card stat-danger">
                <div class="stat-icon">❌</div>
                <div class="stat-details">
                    <h3><?php echo $absent_count; ?></h3>
                    <p>Absent</p>
                </div>
            </div>
            
            <div class="stat-card stat-info">
                <div class="stat-icon">📊</div>
                <div class="stat-details">
                    <h3><?php echo $attendance_percentage; ?>%</h3>
                    <p>Attendance Rate</p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Recent Attendance</h2>
                <a href="view_report.php" class="btn btn-primary">View Full Report</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $recent_attendance->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
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
        
        <?php if ($announcements->num_rows > 0): ?>
        <div class="card">
            <div class="card-header">
                <h2>Recent Announcements</h2>
            </div>
            <div class="card-body">
                <?php while ($announcement = $announcements->fetch_assoc()): ?>
                <div class="announcement-item">
                    <h4><?php echo $announcement['title']; ?></h4>
                    <p><?php echo $announcement['message']; ?></p>
                    <small>Posted by <?php echo $announcement['created_by_name']; ?> on <?php echo date('M d, Y', strtotime($announcement['created_at'])); ?></small>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <style>
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .announcement-item {
            padding: 15px;
            border-bottom: 1px solid var(--border);
        }
        
        .announcement-item:last-child {
            border-bottom: none;
        }
        
        .announcement-item h4 {
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .announcement-item p {
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .announcement-item small {
            color: #6b7280;
        }
    </style>
    
    <script src="js/main.js"></script>
</body>
</html>