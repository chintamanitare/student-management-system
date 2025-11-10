<nav class="navbar">
    <div class="navbar-container">
        <a href="<?php echo $_SESSION['role']; ?>_dashboard.php" class="navbar-brand">
            🎓 Coaching Institute
        </a>
        
        <ul class="navbar-menu">
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_students.php">Students</a></li>
                <li><a href="manage_teachers.php">Teachers</a></li>
                <li><a href="mark_attendance.php">Mark Attendance</a></li>
                <li><a href="view_report.php">Reports</a></li>
                <li><a href="announcements.php">Announcements</a></li>
            <?php elseif ($_SESSION['role'] === 'teacher'): ?>
                <li><a href="teacher_dashboard.php">Dashboard</a></li>
                <li><a href="mark_attendance.php">Mark Attendance</a></li>
                <li><a href="view_report.php">View Reports</a></li>
                <li><a href="my_students.php">My Students</a></li>
            <?php else: ?>
                <li><a href="student_dashboard.php">Dashboard</a></li>
                <li><a href="view_report.php">My Attendance</a></li>
                <li><a href="announcements.php">Announcements</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="navbar-user">
            <div class="user-info">
                <div class="user-name"><?php echo $_SESSION['name']; ?></div>
                <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
            </div>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>