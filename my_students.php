<?php
require_once 'config/db.php';
checkRole(['teacher']);

// Get teacher info
$teacher = $conn->query("SELECT * FROM teachers WHERE user_id = " . $_SESSION['user_id'])->fetch_assoc();

// Get all students who have attendance records with this teacher
$students_query = "
    SELECT DISTINCT s.*, 
           COUNT(a.id) as total_classes,
           SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
           ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(a.id) * 100), 2) as attendance_percentage
    FROM students s
    LEFT JOIN attendance a ON s.id = a.student_id AND a.teacher_id = " . $teacher['id'] . "
    GROUP BY s.id
    HAVING total_classes > 0
    ORDER BY s.roll_no
";

$students = $conn->query($students_query);

// If no students found, show all students
if ($students->num_rows === 0) {
    $students = $conn->query("SELECT *, 0 as total_classes, 0 as present_count, 0 as attendance_percentage FROM students ORDER BY roll_no");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <div>
                <h1>My Students</h1>
                <p>Subject: <?php echo $teacher['subject']; ?></p>
            </div>
            <a href="mark_attendance.php" class="btn btn-primary">Mark Attendance</a>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Student List with Attendance Summary</h2>
            </div>
            <div class="card-body">
                <?php if ($students->num_rows === 0): ?>
                    <div style="text-align: center; padding: 40px;">
                        <p>No students found. Start marking attendance to see students here.</p>
                        <a href="mark_attendance.php" class="btn btn-primary" style="margin-top: 20px;">Mark Attendance</a>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Phone</th>
                                <th>Total Classes</th>
                                <th>Present</th>
                                <th>Attendance %</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $student['roll_no']; ?></td>
                                <td><?php echo $student['name']; ?></td>
                                <td><?php echo $student['course']; ?></td>
                                <td><?php echo $student['batch']; ?></td>
                                <td><?php echo $student['phone'] ?? 'N/A'; ?></td>
                                <td><?php echo $student['total_classes']; ?></td>
                                <td><?php echo $student['present_count']; ?></td>
                                <td>
                                    <?php 
                                    $percentage = $student['attendance_percentage'] ?? 0;
                                    $badge_class = $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge badge-<?php echo $badge_class; ?>">
                                        <?php echo number_format($percentage, 2); ?>%
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewDetails(<?php echo $student['id']; ?>)">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Student Details Modal -->
    <div id="studentDetailsModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2>Student Attendance Details</h2>
                <span class="close" onclick="closeModal('studentDetailsModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div id="studentDetailsContent">
                    <p style="text-align: center; padding: 20px;">Loading...</p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .attendance-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .summary-card h3 {
            font-size: 28px;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .summary-card p {
            font-size: 14px;
            color: #6b7280;
        }
        
        .attendance-history {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
    
    <script src="js/main.js"></script>
    <script>
        async function viewDetails(studentId) {
            openModal('studentDetailsModal');
            
            const teacherId = <?php echo $teacher['id']; ?>;
            const response = await fetch(`api/my_students.php?action=get_details&student_id=${studentId}&teacher_id=${teacherId}`);
            const result = await response.json();
            
            if (result.success) {
                displayStudentDetails(result.data);
            } else {
                document.getElementById('studentDetailsContent').innerHTML = 
                    `<p style="text-align: center; color: red;">${result.message}</p>`;
            }
        }
        
        function displayStudentDetails(data) {
            const student = data.student;
            const attendance = data.attendance;
            const stats = data.stats;
            
            let attendanceHTML = '';
            if (attendance.length === 0) {
                attendanceHTML = '<p style="text-align: center; padding: 20px;">No attendance records found.</p>';
            } else {
                attendanceHTML = `
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Subject</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                attendance.forEach(record => {
                    const badgeClass = record.status === 'present' ? 'success' : 'danger';
                    const statusText = record.status.charAt(0).toUpperCase() + record.status.slice(1);
                    attendanceHTML += `
                        <tr>
                            <td>${formatDate(record.date)}</td>
                            <td>${record.subject}</td>
                            <td><span class="badge badge-${badgeClass}">${statusText}</span></td>
                        </tr>
                    `;
                });
                
                attendanceHTML += '</tbody></table>';
            }
            
            const percentage = stats.attendance_percentage || 0;
            const badgeClass = percentage >= 75 ? 'success' : (percentage >= 50 ? 'warning' : 'danger');
            
            document.getElementById('studentDetailsContent').innerHTML = `
                <div>
                    <h3>${student.name}</h3>
                    <p><strong>Roll No:</strong> ${student.roll_no} | 
                       <strong>Course:</strong> ${student.course} | 
                       <strong>Batch:</strong> ${student.batch}</p>
                </div>
                
                <div class="attendance-summary">
                    <div class="summary-card">
                        <h3>${stats.total_classes}</h3>
                        <p>Total Classes</p>
                    </div>
                    <div class="summary-card">
                        <h3>${stats.present_count}</h3>
                        <p>Present</p>
                    </div>
                    <div class="summary-card">
                        <h3>${stats.absent_count}</h3>
                        <p>Absent</p>
                    </div>
                    <div class="summary-card">
                        <h3 class="badge badge-${badgeClass}" style="font-size: 24px; padding: 10px 15px;">
                            ${percentage.toFixed(2)}%
                        </h3>
                        <p>Attendance Rate</p>
                    </div>
                </div>
                
                <h4 style="margin-top: 20px;">Attendance History</h4>
                <div class="attendance-history">
                    ${attendanceHTML}
                </div>
            `;
        }
    </script>
</body>
</html>