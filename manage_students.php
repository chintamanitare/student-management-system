<?php
require_once 'config/db.php';
checkRole(['admin']);

// Fetch all students
$students = $conn->query("SELECT s.*, u.email FROM students s LEFT JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Manage Students</h1>
            <button class="btn btn-primary" onclick="openModal('addStudentModal')">
                + Add New Student
            </button>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTable">
                            <?php while ($student = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $student['roll_no']; ?></td>
                                <td><?php echo $student['name']; ?></td>
                                <td><?php echo $student['email'] ?? 'N/A'; ?></td>
                                <td><?php echo $student['course']; ?></td>
                                <td><?php echo $student['batch']; ?></td>
                                <td><?php echo $student['phone'] ?? 'N/A'; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editStudent(<?php echo $student['id']; ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo $student['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Student Modal -->
    <div id="addStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Student</h2>
                <span class="close" onclick="closeModal('addStudentModal')">&times;</span>
            </div>
            <form id="addStudentForm" onsubmit="submitStudent(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Roll Number *</label>
                        <input type="text" name="roll_no" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Course *</label>
                        <input type="text" name="course" required>
                    </div>
                    <div class="form-group">
                        <label>Batch *</label>
                        <input type="text" name="batch" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addStudentModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Student Modal -->
    <div id="editStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Student</h2>
                <span class="close" onclick="closeModal('editStudentModal')">&times;</span>
            </div>
            <form id="editStudentForm" onsubmit="updateStudent(event)">
                <input type="hidden" name="student_id" id="edit_student_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" id="edit_name" required>
                    </div>
                    <div class="form-group">
                        <label>Roll Number *</label>
                        <input type="text" name="roll_no" id="edit_roll_no" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" id="edit_email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" id="edit_phone">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Course *</label>
                        <input type="text" name="course" id="edit_course" required>
                    </div>
                    <div class="form-group">
                        <label>Batch *</label>
                        <input type="text" name="batch" id="edit_batch" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editStudentModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script src="js/students.js"></script>
</body>
</html>