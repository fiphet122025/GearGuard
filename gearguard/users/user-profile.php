<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

if (!in_array($_SESSION['user']['role'], [ROLE_ADMIN, ROLE_MANAGER])) {
    die("Access denied");
}

$id = (int)$_GET['id'];

// User info
$user = $conn->query("
    SELECT u.*, d.department_name
    FROM users u
    LEFT JOIN departments d ON u.department_id = d.id
    WHERE u.id = $id
")->fetch_assoc();

// Teams (only meaningful for technicians)
$teams = $conn->query("
    SELECT t.team_name
    FROM maintenance_teams t
    JOIN maintenance_team_members tm ON tm.team_id = t.id
    WHERE tm.user_id = $id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .odoo-sheet { background: white; border: 1px solid #dcdcdc; border-radius: 4px; margin: 20px auto; max-width: 800px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
        .sheet-header { padding: 30px 40px; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 20px; }
        
        /* Big Avatar */
        .profile-avatar { width: 80px; height: 80px; background: #E0E0E0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; color: #fff; background: linear-gradient(135deg, #017E84, #004D40); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        /* Profile Info */
        .profile-name { margin: 0; font-size: 24px; color: #212529; font-weight: 700; }
        .profile-role { font-size: 14px; color: #666; margin-top: 5px; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }

        .field-row { margin-bottom: 15px; border-bottom: 1px solid #f9f9f9; padding-bottom: 10px; }
        .field-label { font-size: 12px; font-weight: 700; color: #666; text-transform: uppercase; margin-bottom: 4px; display: block; }
        .field-val { font-size: 15px; color: #333; font-weight: 500; }

        /* Teams Tag */
        .team-tag { background: #F3E5F5; color: #7B1FA2; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block; margin-right: 5px; margin-bottom: 5px; border: 1px solid #E1BEE7; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 20px;">

    <div style="max-width: 800px; margin: 0 auto 10px auto; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 13px;">
            <a href="users-list.php" style="text-decoration:none; color: #017E84; font-weight: 600;">Users</a> / 
            <span style="color: #666;"><?php echo htmlspecialchars($user['name']); ?></span>
        </div>
        <a href="users-list.php" class="btn-outline" style="text-decoration:none; padding: 6px 12px; border-radius: 4px; color: #666; font-size: 13px; font-weight: 600; border: 1px solid #ccc; background: white; display: inline-flex; align-items: center; gap: 5px;">
            <i class="ph-bold ph-arrow-left"></i> Back
        </a>
    </div>

    <div class="odoo-sheet">
        
        <div class="sheet-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
            <div>
                <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
                <div class="profile-role"><?php echo ucfirst($user['role']); ?></div>
            </div>
        </div>

        <div style="padding: 30px 40px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px;">
                
                <div>
                    <div class="field-row">
                        <span class="field-label">Email Address</span>
                        <span class="field-val">
                            <i class="ph-fill ph-envelope-simple" style="margin-right: 5px; color: #999;"></i>
                            <?php echo $user['email']; ?>
                        </span>
                    </div>
                    
                    <div class="field-row">
                        <span class="field-label">Department</span>
                        <span class="field-val">
                            <?php echo $user['department_name'] ?? '<span style="color:#999; font-style:italic;">Not Assigned</span>'; ?>
                        </span>
                    </div>
                </div>

                <div>
                    <?php if ($user['role'] === ROLE_TECHNICIAN): ?>
                        <div class="field-row" style="border: none;">
                            <span class="field-label" style="color: #017E84;">Maintenance Teams</span>
                            <div style="margin-top: 8px;">
                                <?php 
                                $hasTeams = false;
                                while ($t = $teams->fetch_assoc()): 
                                    $hasTeams = true;
                                ?>
                                    <span class="team-tag">
                                        <i class="ph-bold ph-users-three"></i> <?php echo $t['team_name']; ?>
                                    </span>
                                <?php endwhile; ?>
                                
                                <?php if (!$hasTeams): ?>
                                    <span style="color: #999; font-size: 13px;">No teams assigned yet.</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

</body>
</html>