<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

// Only Admin or Manager
if (!in_array($_SESSION['user']['role'], [ROLE_ADMIN, ROLE_MANAGER])) {
    die("Access denied");
}

$result = $conn->query("
    SELECT u.id, u.name, u.email, u.role, d.department_name
    FROM users u
    LEFT JOIN departments d ON u.department_id = d.id
    ORDER BY u.name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .odoo-table { width: 100%; border-collapse: collapse; background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .odoo-table th { background: #F9F9F9; color: #666; font-size: 12px; font-weight: 700; text-transform: uppercase; padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .odoo-table td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; vertical-align: middle; }
        .odoo-table tr:hover { background: #fcfcfc; }
        
        /* Role Badges */
        .role-badge { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; display: inline-block; }
        .role-admin { background: #E1BEE7; color: #4A148C; }
        .role-manager { background: #BBDEFB; color: #0D47A1; }
        .role-employee { background: #C8E6C9; color: #1B5E20; }
        .role-technician { background: #B2DFDB; color: #00695C; }

        /* Avatar Circle */
        .avatar-small { width: 32px; height: 32px; background: #F3EEF1; color: #714B67; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; margin-right: 10px; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 30px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #714B67; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="ph-fill ph-users"></i> System Users
        </h2>
        
        <a href="../dashboard/dashboard.php" class="btn-outline" style="background: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; color: #444; border: 1px solid #ccc; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 5px;">
            <i class="ph-bold ph-squares-four"></i> Dashboard
        </a>
    </div>

    <table class="odoo-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = $result->fetch_assoc()): ?>
            <tr onclick="window.location='user-profile.php?id=<?php echo $u['id']; ?>'" style="cursor: pointer;">
                <td>
                    <div style="display: flex; align-items: center;">
                        <div class="avatar-small">
                            <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                        </div>
                        <span style="font-weight: 600; color: #212529;"><?php echo htmlspecialchars($u['name']); ?></span>
                    </div>
                </td>
                <td style="color: #555;"><?php echo $u['email']; ?></td>
                <td>
                    <span class="role-badge <?php echo 'role-' . strtolower($u['role']); ?>">
                        <?php echo ucfirst($u['role']); ?>
                    </span>
                </td>
                <td style="color: #555;"><?php echo $u['department_name'] ?? '<span style="color:#ccc;">-</span>'; ?></td>
                <td style="text-align: right;">
                    <a href="user-profile.php?id=<?php echo $u['id']; ?>" style="color: #017E84; font-weight: 700; font-size: 12px; text-decoration: none;">
                        VIEW PROFILE <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>