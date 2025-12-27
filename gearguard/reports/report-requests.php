<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

// Only Admin / Manager
if (!in_array($_SESSION['user']['role'], [ROLE_ADMIN, ROLE_MANAGER])) {
    die("Access denied");
}

$type = $_GET['type'] ?? 'team';

switch ($type) {

    // 🔹 Requests grouped by Maintenance Team
    case 'team':
        $title = "Requests by Maintenance Team";
        $query = "
            SELECT t.team_name AS label, COUNT(r.id) AS total
            FROM maintenance_requests r
            JOIN maintenance_teams t ON r.maintenance_team_id = t.id
            GROUP BY t.id
        ";
        break;

    // 🔹 Requests grouped by Equipment
    case 'equipment':
        $title = "Requests by Equipment";
        $query = "
            SELECT e.equipment_name AS label, COUNT(r.id) AS total
            FROM maintenance_requests r
            JOIN equipment e ON r.equipment_id = e.id
            GROUP BY e.id
        ";
        break;

    // 🔹 Requests grouped by Status
    case 'status':
        $title = "Requests by Status";
        $query = "
            SELECT r.status AS label, COUNT(r.id) AS total
            FROM maintenance_requests r
            GROUP BY r.status
        ";
        break;

    default:
        die("Invalid report type");
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?> | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Report Specific Styles */
        .report-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden; /* For rounded corners on table */
            max-width: 900px;
            margin: 0 auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #F9F9F9;
            color: #666;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .data-table td {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #333;
        }

        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover { background-color: #fcfcfc; }

        /* Metric Badge for Total */
        .metric-badge {
            background: #E0F2F1;
            color: #00695C;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 13px;
            display: inline-block;
        }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 30px;">

    <div style="max-width: 900px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="color: #714B67; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="ph-fill ph-chart-bar"></i>
                <?php echo $title; ?>
            </h2>
            <div style="font-size: 13px; color: #666; margin-top: 5px;">
                Analytics Report Generated on <?php echo date("M d, Y"); ?>
            </div>
        </div>
        
        <a href="report-dashboard.php" class="btn-outline" style="text-decoration: none; padding: 8px 16px; background: white; border: 1px solid #ccc; border-radius: 4px; color: #444; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
            <i class="ph-bold ph-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="report-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th><?php echo ucfirst($type); ?> Name</th>
                    <th style="width: 150px; text-align: right;">Total Requests</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight: 600; color: #212529;">
                        <?php echo htmlspecialchars($row['label']); ?>
                    </td>
                    
                    <td style="text-align: right;">
                        <span class="metric-badge">
                            <?php echo $row['total']; ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                </tbody>
        </table>
    </div>

</body>
</html>