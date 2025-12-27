<?php
include "../config/auth.php";
include "../config/constants.php";

// Only Admin / Manager
if (!in_array($_SESSION['user']['role'], [ROLE_ADMIN, ROLE_MANAGER])) {
    die("Access denied");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports Dashboard | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Specific Styles for Report Cards */
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .report-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 6px; /* Odoo Style */
            padding: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
        }

        .report-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            border-color: #017E84; /* Teal border on hover */
        }

        /* Icon Circle */
        .report-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #F3EEF1; /* Light Purple Bg */
            color: #714B67; /* Odoo Purple */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 20px;
            transition: 0.2s;
        }

        .report-card:hover .report-icon {
            background: #017E84; /* Teal Bg on Hover */
            color: white;
        }

        .report-title {
            font-size: 16px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 8px;
        }

        .report-desc {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }

        .btn-arrow {
            margin-top: 20px;
            font-size: 12px;
            font-weight: 700;
            color: #017E84;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 30px;">

    <div style="max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="color: #714B67; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="ph-fill ph-chart-pie-slice"></i> Reporting
            </h2>
            <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">Select a metric to generate analytics.</p>
        </div>
        
        <a href="../dashboard/dashboard.php" class="btn-outline" style="text-decoration: none; padding: 8px 16px; background: white; border: 1px solid #ccc; border-radius: 4px; color: #444; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
            <i class="ph-bold ph-squares-four"></i> Dashboard
        </a>
    </div>

    <div class="report-grid" style="max-width: 1000px; margin: 0 auto;">
        
        <a href="report-requests.php?type=team" class="report-card">
            <div class="report-icon">
                <i class="ph-fill ph-users-three"></i>
            </div>
            <div class="report-title">By Maintenance Team</div>
            <div class="report-desc">Analyze workload distribution across different technical teams.</div>
            <div class="btn-arrow">View Analysis <i class="ph-bold ph-arrow-right"></i></div>
        </a>

        <a href="report-requests.php?type=equipment" class="report-card">
            <div class="report-icon">
                <i class="ph-fill ph-desktop-tower"></i>
            </div>
            <div class="report-title">By Equipment</div>
            <div class="report-desc">Identify which assets break down most frequently.</div>
            <div class="btn-arrow">View Analysis <i class="ph-bold ph-arrow-right"></i></div>
        </a>

        <a href="report-requests.php?type=status" class="report-card">
            <div class="report-icon">
                <i class="ph-fill ph-chart-bar"></i>
            </div>
            <div class="report-title">By Request Status</div>
            <div class="report-desc">Track progress: New vs. In-Progress vs. Completed requests.</div>
            <div class="btn-arrow">View Analysis <i class="ph-bold ph-arrow-right"></i></div>
        </a>

    </div>

</body>
</html>