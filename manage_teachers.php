<?php
require_once 'config/db.php';
checkRole(['admin']);

$teachers = $conn->query("SELECT t.*, u.email FROM teachers t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Manage Teachers</h1>
            <button class="btn btn-primary" onclick="openModal('addTeacherModal')">
                + Add New Teacher
            </button>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="teachersTable">
                            <?php while ($teacher = $teachers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $teacher['name']; ?></td>
                                <td><?php echo $teacher['email']; ?></td>
                                <td><?php echo $teacher['subject']; ?></td>
                                <td><?php echo $teacher['phone'] ?? 'N/A'; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editTeacher(<?php echo $teacher['id']; ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteTeacher(<?php echo $teacher['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Teacher Modal -->
    <div id="addTeacherModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Teacher</h2>
                <span class="close" onclick="closeModal('addTeacherModal')">&times;</span>
            </div>
            <form id="addTeacherForm" onsubmit="submitTeacher(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Subject *</label>
                        <input type="text" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone">
                    </div>
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addTeacherModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Teacher</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Teacher Modal -->
    <div id="editTeacherModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Teacher</h2>
                <span class="close" onclick="closeModal('editTeacherModal')">&times;</span>
            </div>
            <form id="editTeacherForm" onsubmit="updateTeacher(event)">
                <input type="hidden" name="teacher_id" id="edit_teacher_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" id="edit_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" id="edit_email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Subject *</label>
                        <input type="text" name="subject" id="edit_subject" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" id="edit_phone">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editTeacherModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Teacher</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/main.js"></script>
    <script src="js/teachers.js"></script>
</body>
</html>