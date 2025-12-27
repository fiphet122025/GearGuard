<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

if (!in_array($_SESSION['user']['role'], [ROLE_ADMIN, ROLE_MANAGER])) {
    die("Access denied");
}

if (isset($_POST['save'])) {
    $stmt = $conn->prepare("
        INSERT INTO maintenance_teams (team_name)
        VALUES (?)
    ");
    $stmt->bind_param("s", $_POST['team_name']);
    $stmt->execute();

    header("Location: teams-list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Team | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .odoo-form-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 30px;
            max-width: 600px;
            margin: 20px auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .form-control:focus { border-color: #714B67; outline: none; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 20px;">

    <div style="max-width: 600px; margin: 0 auto 10px auto; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="color: #714B67; margin: 0;">New Team</h2>
        <a href="teams-list.php" class="btn-outline" style="text-decoration:none; padding: 6px 12px; border-radius: 4px; color: #666; font-size: 13px; font-weight: 600; border: 1px solid #ccc; background: white;">
            <i class="ph-bold ph-arrow-left"></i> Discard
        </a>
    </div>

    <div class="odoo-form-card">
        <form method="POST">
            
            <div style="margin-bottom: 25px;">
                <label style="display:block; font-size: 12px; color: #017E84; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Team Name</label>
                <input name="team_name" class="form-control" placeholder="e.g. Electrical Maintenance" style="font-size: 18px; font-weight: 600;" required>
            </div>

            <div style="border-top: 1px solid #eee; padding-top: 20px;">
                <button type="submit" name="save" style="background: #017E84; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: 700; text-transform: uppercase; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="ph-bold ph-check"></i> Save Team
                </button>
            </div>

        </form>
    </div>

</body>
</html>