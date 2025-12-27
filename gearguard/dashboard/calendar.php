<?php
session_start();

include "../config/auth.php";
include "../config/constants.php";
include "../config/db.php";

/* =============================
   AUTH & ROLE CHECK
============================= */
if (!isset($_SESSION['user'])) {
    die("Unauthorized access");
}

$user = $_SESSION['user'];
$name = htmlspecialchars($user['name'] ?? 'User');
$role = trim($user['role']); // DO NOT lowercase

if (!in_array($role, [ROLE_MANAGER, ROLE_ADMIN], true)) {
    die("Access denied");
}

/* =============================
    CALENDAR DATE LOGIC
============================= */
// Accept month/year via GET for navigation, validate values
$year  = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');
$month = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('n');
if ($month < 1) { $month = 1; }
if ($month > 12) { $month = 12; }

$monthName   = date('F Y', strtotime("$year-$month-01"));
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Get first day of month (1 = Monday, 7 = Sunday)
$firstDayOfMonth = (int)date('N', strtotime("$year-$month-01"));

// Prev / Next month helpers
$prevMonth = $month - 1; $prevYear = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear -= 1; }
$nextMonth = $month + 1; $nextYear = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear += 1; }

// Today for highlight
$todayY = (int)date('Y'); $todayM = (int)date('n'); $todayD = (int)date('j');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calendar | GearGuard</title>
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
        <a href="dashboard.php" class="menu-item">
            <i class="ph-bold ph-squares-four"></i> Dashboard
        </a>
         <a href="kanban.php" class="menu-item">
            <i class="ph-bold ph-kanban"></i> Kanban
        </a>
        <a href="calendar.php" class="menu-item active">
            <i class="ph-bold ph-calendar"></i> Calendar
        </a>
       <a href="../equipment/equipment-list.php" class="menu-item">
            <i class="ph-bold ph-desktop-tower"></i> Equipment
        </a>
        <a href="../teams/teams-list.php" class="menu-item">
            <i class="ph-bold ph-users"></i> Teams
        </a>
    </div>

    <div class="user-profile">
        <div style="font-weight:600;"><?php echo $name; ?></div>
        <div class="user-role"><?php echo htmlspecialchars($role); ?></div>
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
        <div class="page-title">
            <i class="ph-fill ph-calendar-blank"></i> Maintenance Calendar
        </div>
        <a href="dashboard.php" class="btn btn-outline">
            <i class="ph-bold ph-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="padding:0; overflow:hidden;">

        <!-- Month Header -->
        <div style="padding:15px; background:#fff; border-bottom:1px solid #eee;
                    display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; gap:8px; align-items:center;">
                <a class="btn btn-outline" href="?m=<?php echo $prevMonth; ?>&y=<?php echo $prevYear; ?>">&larr;</a>
                <div style="font-weight:700; font-size:18px; color:var(--odoo-purple);">
                    <?php echo $monthName; ?>
                </div>
                <a class="btn btn-outline" href="?m=<?php echo $nextMonth; ?>&y=<?php echo $nextYear; ?>">&rarr;</a>
            </div>
            <div>
                <a href="?m=<?php echo (int)date('n'); ?>&y=<?php echo (int)date('Y'); ?>" class="btn">Today</a>
            </div>
        </div>

        <!-- Calendar Table -->
        <div class="calendar-grid">
            <table class="calendar-table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                        <th>Sun</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Build array of cells: leading empties, days, trailing empties
                $cells = [];
                for ($i = 1; $i < $firstDayOfMonth; $i++) { $cells[] = ''; }
                for ($day = 1; $day <= $daysInMonth; $day++) { $cells[] = $day; }
                $trailing = (7 - (count($cells) % 7)) % 7;
                for ($i = 0; $i < $trailing; $i++) { $cells[] = ''; }

                // Output rows of 7
                $weeks = array_chunk($cells, 7);
                foreach ($weeks as $week) {
                    echo "<tr>";
                    foreach ($week as $cell) {
                        $cellHtml = '&nbsp;';
                        $classes = '';
                        if ($cell !== '') {
                            $day = (int)$cell;
                            $cellHtml = $day;
                            if ($year === $todayY && $month === $todayM && $day === $todayD) {
                                $classes = 'today';
                            }
                        } else {
                            $classes = 'empty';
                        }
                        echo "<td class='cal-cell " . $classes . "' style='padding:10px; vertical-align:top; border:1px solid #f0f0f0;'>" . $cellHtml . "</td>";
                    }
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

</body>
</html>
