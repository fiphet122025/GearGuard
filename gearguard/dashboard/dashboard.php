<?php
/* =============================
   Core Includes (DO NOT add session_start here)
============================= */
include "../config/auth.php";
include "../config/constants.php";
include "../config/db.php";

/* =============================
   Authenticated User
============================= */
$user = $_SESSION['user'] ?? die("Unauthorized access");
$name = htmlspecialchars($user['name']);
$role = strtolower(trim($user['role']));

/* =============================
   Dashboard Metrics (Reports)
============================= */

// Open Requests (New + In Progress)
$openReq = $conn->query("
    SELECT COUNT(*) AS total
    FROM maintenance_requests
    WHERE status IN ('New','In Progress')
")->fetch_assoc()['total'];

// Repaired Requests
$repairedReq = $conn->query("
    SELECT COUNT(*) AS total
    FROM maintenance_requests
    WHERE status = 'Repaired'
")->fetch_assoc()['total'];

// Scrapped Equipment
$scrappedEq = $conn->query("
    SELECT COUNT(*) AS total
    FROM equipment
    WHERE is_scrapped = 1
")->fetch_assoc()['total'];

// Preventive Requests (This Month)
$preventiveMonth = $conn->query("
    SELECT COUNT(*) AS total
    FROM maintenance_requests
    WHERE request_type = 'Preventive'
      AND MONTH(scheduled_date) = MONTH(CURDATE())
      AND YEAR(scheduled_date) = YEAR(CURDATE())
")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | GearGuard</title>
    <link rel="stylesheet" href="style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<!-- =============================
     SIDEBAR
============================= -->
<div class="sidebar">
    <div class="brand">
        <i class="ph-fill ph-gear-six"></i> GearGuard
    </div>

    <div class="menu">
        <a href="dashboard.php" class="menu-item active">
            <i class="ph-bold ph-squares-four"></i> Dashboard
        </a>

        <?php if ($role === ROLE_EMPLOYEE): ?>
            <a href="../requests/request-create.php" class="menu-item">
                <i class="ph-bold ph-plus-circle"></i> Create Request
            </a>
            <a href="../requests/request-list.php" class="menu-item">
                <i class="ph-bold ph-list-dashes"></i> My Requests
            </a>

        <?php elseif ($role === ROLE_MANAGER): ?>
            <a href="kanban.php" class="menu-item">
                <i class="ph-bold ph-kanban"></i> Kanban Board
            </a>
            <a href="calendar.php" class="menu-item">
                <i class="ph-bold ph-calendar"></i> Calendar
            </a>
            <a href="../equipment/equipment-list.php" class="menu-item">
                <i class="ph-bold ph-desktop-tower"></i> Equipment
            </a>
            <a href="../teams/teams-list.php" class="menu-item">
                <i class="ph-bold ph-users"></i> Teams
            </a>

            <a href="../users/users-list.php" class="menu-item">
                <i class="ph-bold ph-user-gear"></i> Users
            </a>
            <a href="../reports/report-dashboard.php" class="menu-item">
                <i class="ph-bold ph-chart-line-up"></i> Reports
            </a>
        <?php endif; ?>
    </div>

    <div class="user-profile">
        <div style="font-weight:600;"><?php echo $name; ?></div>
        <div class="user-role"><?php echo ucfirst($role); ?></div>
        <a href="../auth/logout.php"
           style="color:#FF8787;font-size:12px;text-decoration:none;margin-top:8px;display:inline-block;">
            <i class="ph-bold ph-sign-out"></i> Logout
        </a>
    </div>
</div>

<!-- =============================
     MAIN CONTENT
============================= -->
<div class="main-content">

    <div class="top-header">
        <div class="page-title">Dashboard Overview</div>
        <div style="font-size:13px;color:#666;">
            <?php echo date("l, F j, Y"); ?>
        </div>
    </div>

    <div class="card" style="border-left:5px solid var(--odoo-purple);">
        <h3 style="display:flex;align-items:center;gap:10px;">
            <i class="ph-fill ph-hand-waving" style="color:var(--odoo-purple);"></i>
            Welcome back, <?php echo explode(' ', $name)[0]; ?>!
        </h3>
        <p style="color:#666;margin-bottom:0;">
            You are logged in as a <strong><?php echo ucfirst($role); ?></strong>.
            Use the sidebar to navigate your tools.
        </p>
    </div>

    <!-- =============================
         METRICS (LIVE REPORT DATA)
    ============================= -->
    <div class="metrics-grid">

        <div class="metric-card">
            <div class="metric-icon" style="background:#FFF3CD;color:#856404;">
                <i class="ph-fill ph-warning"></i>
            </div>
            <div class="metric-info">
                <span class="metric-val"><?php echo $openReq; ?></span>
                <span class="metric-label">Open Requests</span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background:#D1E7DD;color:#0F5132;">
                <i class="ph-fill ph-check-circle"></i>
            </div>
            <div class="metric-info">
                <span class="metric-val"><?php echo $repairedReq; ?></span>
                <span class="metric-label">Repaired</span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background:#F8D7DA;color:#842029;">
                <i class="ph-fill ph-trash"></i>
            </div>
            <div class="metric-info">
                <span class="metric-val"><?php echo $scrappedEq; ?></span>
                <span class="metric-label">Scrapped Assets</span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon" style="background:#E0F2FE;color:#0369A1;">
                <i class="ph-fill ph-calendar"></i>
            </div>
            <div class="metric-info">
                <span class="metric-val"><?php echo $preventiveMonth; ?></span>
                <span class="metric-label">Preventive (This Month)</span>
            </div>
        </div>

    </div>

</div>

</body>
</html>
