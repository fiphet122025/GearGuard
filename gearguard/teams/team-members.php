<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

if (!in_array($_SESSION['user']['role'], [ROLE_ADMIN, ROLE_MANAGER])) {
    die("Access denied");
}

$teamId = (int)$_GET['id'];

// Team info
$team = $conn->query("
    SELECT * FROM maintenance_teams WHERE id = $teamId
")->fetch_assoc();

// Technicians not yet in this team
$availableTechs = $conn->query("
    SELECT id, name 
    FROM users 
    WHERE role = '" . ROLE_TECHNICIAN . "'
    AND id NOT IN (
        SELECT user_id FROM maintenance_team_members WHERE team_id = $teamId
    )
");

// Current members
$members = $conn->query("
    SELECT u.name
    FROM users u
    JOIN maintenance_team_members tm ON tm.user_id = u.id
    WHERE tm.team_id = $teamId
");

if (isset($_POST['add_member'])) {
    $stmt = $conn->prepare("
        INSERT INTO maintenance_team_members (team_id, user_id)
        VALUES (?, ?)
    ");
    $stmt->bind_param("ii", $teamId, $_POST['user_id']);
    $stmt->execute();

    header("Location: team-members.php?id=$teamId");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Members | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .odoo-sheet { background: white; border: 1px solid #dcdcdc; border-radius: 4px; margin: 20px auto; max-width: 800px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
        .sheet-header { padding: 20px 40px; border-bottom: 1px solid #eee; background: #fafafa; }
        .section-title { font-size: 14px; font-weight: 700; color: #444; margin-bottom: 15px; border-bottom: 2px solid #017E84; display: inline-block; padding-bottom: 5px; }
        
        .member-list { list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .member-item { background: #fff; border: 1px solid #eee; padding: 10px; display: flex; align-items: center; gap: 10px; border-radius: 4px; }
        .member-avatar { width: 32px; height: 32px; background: #F3EEF1; color: #714B67; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; }

        .add-box { background: #E0F2F1; border: 1px solid #B2DFDB; padding: 20px; border-radius: 4px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 20px;">

    <div style="max-width: 800px; margin: 0 auto 10px auto;">
        <a href="teams-list.php" class="btn-outline" style="text-decoration:none; padding: 6px 12px; border-radius: 4px; color: #666; font-size: 13px; font-weight: 600; border: 1px solid #ccc; background: white; display: inline-flex; align-items: center; gap: 5px;">
            <i class="ph-bold ph-arrow-left"></i> Back to Teams
        </a>
    </div>

    <div class="odoo-sheet">
        <div class="sheet-header">
            <h2 style="margin: 0; color: #714B67; display: flex; align-items: center; gap: 10px;">
                <i class="ph-fill ph-users-three"></i> 
                <?php echo htmlspecialchars($team['team_name']); ?>
            </h2>
        </div>

        <div style="padding: 30px 40px;">
            
            <div class="add-box">
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #00695C; font-size: 14px;">Add New Technician</div>
                    <div style="font-size: 12px; color: #555;">Select a user to assign them to this team.</div>
                </div>
                <form method="POST" style="display: flex; gap: 10px;">
                    <select name="user_id" required style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-width: 200px;">
                        <option value="">Select Technician...</option>
                        <?php while ($t = $availableTechs->fetch_assoc()): ?>
                            <option value="<?php echo $t['id']; ?>">
                                <?php echo $t['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" name="add_member" style="background: #017E84; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 700; cursor: pointer; text-transform: uppercase; font-size: 12px;">
                        Add
                    </button>
                </form>
            </div>

            <div class="section-title">Current Team Members</div>
            
            <?php if ($members->num_rows > 0): ?>
                <ul class="member-list">
                    <?php while ($m = $members->fetch_assoc()): ?>
                        <li class="member-item">
                            <div class="member-avatar">
                                <?php echo strtoupper(substr($m['name'], 0, 1)); ?>
                            </div>
                            <span style="font-weight: 500; color: #333;"><?php echo $m['name']; ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <div style="color: #999; font-style: italic;">No members in this team yet.</div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>