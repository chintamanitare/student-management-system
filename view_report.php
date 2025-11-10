<?php
require_once 'config/db.php';
checkRole(['admin', 'teacher', 'student']);

// Get filter options
$students = $conn->query("SELECT id, name, roll_no FROM students ORDER BY roll_no");
$subjects = $conn->query("SELECT DISTINCT subject FROM attendance ORDER BY subject");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Attendance Report</h1>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Filter Options</h3>
            </div>
            <div class="card-body">
                <form id="filterForm" onsubmit="loadReport(event)">
                    <div class="form-row">
                        <?php if ($_SESSION['role'] !== 'student'): ?>
                        <div class="form-group">
                            <label>Student</label>
                            <select name="student_id" id="student_id">
                                <option value="">All Students</option>
                                <?php while ($student = $students->fetch_assoc()): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo $student['roll_no']; ?> - <?php echo $student['name']; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="date_from" id="date_from">
                        </div>
                        
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="date_to" id="date_to">
                        </div>
                        
                        <div class="form-group">
                            <label>Subject</label>
                            <select name="subject" id="subject">
                                <option value="">All Subjects</option>
                                <?php while ($subj = $subjects->fetch_assoc()): ?>
                                <option value="<?php echo $subj['subject']; ?>"><?php echo $subj['subject']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                        <button type="button" class="btn btn-success" onclick="exportReport()">Export CSV</button>
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card" id="reportCard" style="display: none;">
            <div class="card-header">
                <h3>Report Results</h3>
                <div id="reportSummary" class="report-summary"></div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="reportTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script src="js/report.js"></script>
    <script>
        // For student role, automatically load their attendance
        <?php if ($_SESSION['role'] === 'student'): ?>
        window.addEventListener('DOMContentLoaded', function() {
            const studentResult = <?php 
                $student = $conn->query("SELECT id FROM students WHERE user_id = " . $_SESSION['user_id'])->fetch_assoc();
                echo json_encode($student);
            ?>;
            if (studentResult) {
                loadStudentReport(studentResult.id);
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>