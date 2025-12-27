<?php
// Header should be included AFTER auth.php
include_once "../config/constants.php";

$userName = $_SESSION['user']['name'] ?? '';
$userRole = $_SESSION['user']['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>GearGuard</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/style_layout.css"> <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="page-wrapper">

    <div class="odoo-topbar">
        <div class="topbar-title">
            <i class="ph-fill ph-circles-four" style="color: #017E84;"></i>
            Maintenance Tracker
        </div>
        
        <div class="user-dropdown">
            <div style="text-align: right;">
                <div style="font-weight: 700; color: #212529;">
                    <?php echo htmlspecialchars($userName); ?>
                </div>
                <div style="font-size: 11px; color: #888; text-transform: uppercase;">
                    <?php echo ucfirst($userRole); ?>
                </div>
            </div>
            
            <div style="width: 32px; height: 32px; background: #E0E0E0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="ph-fill ph-user" style="color: #666;"></i>
            </div>

            <a href="../auth/logout.php" class="logout-link">
                <i class="ph-bold ph-sign-out"></i> Logout
            </a>
        </div>
    </div>

    <div class="content-area">
