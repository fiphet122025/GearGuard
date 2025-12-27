<?php
include_once "../config/constants.php";
$role = $_SESSION['user']['role'];
?>

<div class="odoo-sidebar">
    <div class="sidebar-header">
        <i class="ph-fill ph-gear-six" style="margin-right: 10px; color: #00E0C6;"></i> GearGuard
    </div>

    <div class="sidebar-menu">
        <div class="menu-label">Main Menu</div>
        
        <a href="../dashboard/dashboard.php">
            <i class="ph-bold ph-squares-four"></i> Dashboard
        </a>

        <?php if ($role === ROLE_EMPLOYEE): ?>
            <div class="menu-label" style="margin-top: 15px;">Employee Actions</div>
            <a href="../requests/request-create.php">
                <i class="ph-bold ph-plus-circle"></i> Create Request
            </a>
            <a href="../requests/request-list.php">
                <i class="ph-bold ph-list-dashes"></i> My Requests
            </a>

        <?php elseif ($role === ROLE_MANAGER): ?>
            <div class="menu-label" style="margin-top: 15px;">Management</div>
            <a href="../requests/request-list.php">
                <i class="ph-bold ph-clipboard-text"></i> All Requests
            </a>
            <a href="../dashboard/calendar.php">
                <i class="ph-bold ph-calendar"></i> Calendar
            </a>
            <a href="../equipment/equipment-list.php">
                <i class="ph-bold ph-desktop-tower"></i> Equipment
            </a>
            <a href="../teams/teams-list.php">
                <i class="ph-bold ph-users"></i> Teams
            </a>
            <a href="../reports/report-dashboard.php">
                <i class="ph-bold ph-chart-line-up"></i> Reports
            </a>

        <?php elseif ($role === ROLE_ADMIN): ?>
            <div class="menu-label" style="margin-top: 15px;">Administration</div>
            <a href="../users/users-list.php">
                <i class="ph-bold ph-user-gear"></i> Users
            </a>
            <a href="../teams/teams-list.php">
                <i class="ph-bold ph-users-three"></i> Teams
            </a>
            <a href="../equipment/equipment-list.php">
                <i class="ph-bold ph-desktop-tower"></i> Equipment
            </a>
            <a href="../reports/report-dashboard.php">
                <i class="ph-bold ph-chart-bar"></i> Analytics
            </a>

        <?php elseif ($role === ROLE_TECHNICIAN): ?>
            <div class="menu-label" style="margin-top: 15px;">Work Space</div>
            <a href="../dashboard/kanban.php">
                <i class="ph-bold ph-kanban"></i> Kanban Board
            </a>
        <?php endif; ?>
    </div>
</div>