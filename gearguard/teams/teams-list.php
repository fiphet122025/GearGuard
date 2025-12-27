<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

// Only admin / manager
if (!in_array($_SESSION['user']['role'], [ROLE_ADMIN, ROLE_MANAGER])) {
    die("Access denied");
}

$teams = $conn->query("SELECT * FROM maintenance_teams ORDER BY team_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teams | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .odoo-table { width: 100%; border-collapse: collapse; background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .odoo-table th { background: #F9F9F9; color: #666; font-size: 12px; font-weight: 700; text-transform: uppercase; padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .odoo-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
        .odoo-table tr:hover { background: #fcfcfc; }
        
        .count-badge { background: #E3F2FD; color: #0D47A1; padding: 2px 8px; border-radius: 10px; font-weight: 700; font-size: 12px; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 30px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #714B67; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="ph-fill ph-users-three"></i> Maintenance Teams
        </h2>
        <div>
            <a href="team-add.php" style="background: #017E84; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 700; font-size: 13px; text-transform: uppercase;">
                <i class="ph-bold ph-plus"></i> New Team
            </a>
            <a href="../dashboard/dashboard.php" class="btn-outline" style="margin-left: 10px; background: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; color: #444; border: 1px solid #ccc; font-size: 13px; font-weight: 600;">
                Dashboard
            </a>
        </div>
    </div>

    <table class="odoo-table">
        <thead>
            <tr>
                <th>Team Name</th>
                <th>Members Count</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($team = $teams->fetch_assoc()): ?>
            <tr>
                <td style="font-weight: 600; color: #212529;">
                    <?php echo htmlspecialchars($team['team_name']); ?>
                </td>
                <td>
                    <?php
                    $count = $conn->query("
                        SELECT COUNT(*) AS total 
                        FROM maintenance_team_members 
                        WHERE team_id = {$team['id']}
                    ")->fetch_assoc()['total'];
                    ?>
                    <span class="count-badge"><?php echo $count; ?> Techs</span>
                </td>
                <td style="text-align: right;">
                    <a href="team-members.php?id=<?php echo $team['id']; ?>" style="color: #017E84; font-weight: 700; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                        MANAGE MEMBERS <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>