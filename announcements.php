<?php
require_once 'config/db.php';
checkRole(['admin', 'student']);

// Fetch announcements
if ($_SESSION['role'] === 'admin') {
    $announcements = $conn->query("
        SELECT a.*, u.name as created_by_name
        FROM announcements a
        JOIN users u ON a.created_by = u.id
        ORDER BY a.created_at DESC
    ");
} else {
    $announcements = $conn->query("
        SELECT a.*, u.name as created_by_name
        FROM announcements a
        JOIN users u ON a.created_by = u.id
        WHERE a.target_role IN ('all', 'student')
        ORDER BY a.created_at DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Announcements</h1>
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <button class="btn btn-primary" onclick="openModal('addAnnouncementModal')">
                + Add Announcement
            </button>
            <?php endif; ?>
        </div>
        
        <div class="announcements-list">
            <?php if ($announcements->num_rows === 0): ?>
                <div class="card">
                    <div class="card-body" style="text-align: center; padding: 40px;">
                        <p>No announcements yet.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php while ($announcement = $announcements->fetch_assoc()): ?>
                <div class="card announcement-card">
                    <div class="card-header">
                        <div>
                            <h3><?php echo $announcement['title']; ?></h3>
                            <small>
                                Posted by <?php echo $announcement['created_by_name']; ?> on 
                                <?php echo date('M d, Y \a\t h:i A', strtotime($announcement['created_at'])); ?>
                                • Target: <?php echo ucfirst($announcement['target_role']); ?>
                            </small>
                        </div>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <button class="btn btn-sm btn-danger" onclick="deleteAnnouncement(<?php echo $announcement['id']; ?>)">
                            Delete
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br($announcement['message']); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($_SESSION['role'] === 'admin'): ?>
    <!-- Add Announcement Modal -->
    <div id="addAnnouncementModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Announcement</h2>
                <span class="close" onclick="closeModal('addAnnouncementModal')">&times;</span>
            </div>
            <form id="addAnnouncementForm" onsubmit="submitAnnouncement(event)">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" required maxlength="200">
                </div>
                
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" rows="5" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Target Audience *</label>
                    <select name="target_role" required>
                        <option value="all">All Users</option>
                        <option value="student">Students Only</option>
                        <option value="teacher">Teachers Only</option>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addAnnouncementModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Post Announcement</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <style>
        .announcements-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .announcement-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .announcement-card h3 {
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .announcement-card small {
            color: #6b7280;
            font-size: 13px;
        }
        
        .announcement-card p {
            line-height: 1.8;
            color: var(--dark);
        }
    </style>
    
    <script src="js/main.js"></script>
    <script src="js/announcements.js"></script>
</body>
</html>