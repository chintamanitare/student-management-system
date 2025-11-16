<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box" style="text-align: center;">
            <div style="font-size: 72px; margin-bottom: 20px;">🚫</div>
            <h1 style="color: #ef4444; margin-bottom: 20px;">Unauthorized Access</h1>
            <p style="font-size: 16px; margin-bottom: 30px;">
                You don't have permission to access this page.
            </p>
            
            <?php if (isset($_SESSION['role'])): ?>
                <a href="<?php echo $_SESSION['role']; ?>_dashboard.php" class="btn btn-primary">
                    Go to Dashboard
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">
                    Login
                </a>
            <?php endif; ?>
            
            <div style="margin-top: 20px;">
                <a href="logout.php" style="color: #6b7280; text-decoration: none;">
                    Logout
                </a>
            </div>
        </div>
    </div>
</body>
</html>