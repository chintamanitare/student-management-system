<?php
require_once 'config/db.php';
checkRole(['teacher', 'admin']);

// Get teacher info
if ($_SESSION['role'] === 'teacher') {
    $teacher_result = $conn->query("SELECT * FROM teachers WHERE user_id = " . $_SESSION['user_id']);
    $teacher = $teacher_result->fetch_assoc();
} else {
    // Admin can select teacher
    $teachers = $conn->query("SELECT * FROM teachers ORDER BY name");
}

// Get all students
$students = $conn->query("SELECT * FROM students ORDER BY roll_no");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Mark Attendance</h1>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form id="attendanceForm" onsubmit="submitAttendance(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date *</label>
                            <input type="date" name="date" id="attendance_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <div class="form-group">
                            <label>Teacher *</label>
                            <select name="teacher_id" id="teacher_id" required onchange="updateSubject()">
                                <option value="">Select Teacher</option>
                                <?php while ($t = $teachers->fetch_assoc()): ?>
                                <option value="<?php echo $t['id']; ?>" data-subject="<?php echo $t['subject']; ?>">
                                    <?php echo $t['name']; ?> - <?php echo $t['subject']; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">
                        <div class="form-group">
                            <label>Teacher</label>
                            <input type="text" value="<?php echo $teacher['name']; ?>" readonly>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label>Subject *</label>
                            <input type="text" name="subject" id="subject" 
                                   value="<?php echo $_SESSION['role'] === 'teacher' ? $teacher['subject'] : ''; ?>" 
                                   <?php echo $_SESSION['role'] === 'teacher' ? 'readonly' : ''; ?> required>
                        </div>
                    </div>
                    
                    <div class="attendance-actions">
                        <button type="button" class="btn btn-success" onclick="markAllPresent()">Mark All Present</button>
                        <button type="button" class="btn btn-secondary" onclick="markAllAbsent()">Mark All Absent</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table attendance-table">
                            <thead>
                                <tr>
                                    <th>Roll No</th>
                                    <th>Student Name</th>
                                    <th>Course</th>
                                    <th>Batch</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $students->data_seek(0);
                                while ($student = $students->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><?php echo $student['roll_no']; ?></td>
                                    <td><?php echo $student['name']; ?></td>
                                    <td><?php echo $student['course']; ?></td>
                                    <td><?php echo $student['batch']; ?></td>
                                    <td>
                                        <label class="radio-inline">
                                            <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="present" checked>
                                            <span class="radio-label present">Present</span>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="absent">
                                            <span class="radio-label absent">Absent</span>
                                        </label>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">Submit Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script src="js/attendance.js"></script>
</body>
</html>