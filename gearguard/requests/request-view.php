<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

$id = (int)$_GET['id'];

$request = $conn->query("
    SELECT r.*, e.equipment_name 
    FROM maintenance_requests r
    JOIN equipment e ON r.equipment_id = e.id
    WHERE r.id = $id
")->fetch_assoc();

// Fetch technicians of the request’s team
$techs = $conn->query("
    SELECT u.id, u.name
    FROM users u
    JOIN maintenance_team_members tm ON tm.user_id = u.id
    WHERE tm.team_id = {$request['maintenance_team_id']}
");

if (isset($_POST['assign'])) {
    $stmt = $conn->prepare("
        UPDATE maintenance_requests
        SET assigned_technician_id = ?, status = ?
        WHERE id = ?
    ");

    $newStatus = STATUS_IN_PROGRESS;
    $stmt->bind_param("isi", $_POST['technician_id'], $newStatus, $id);
    $stmt->execute();

    header("Location: request-view.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Details | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .odoo-sheet { background: white; border: 1px solid #dcdcdc; border-radius: 4px; margin: 20px auto; max-width: 900px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
        .sheet-header { padding: 20px 40px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: flex-start; }
        
        .status-ribbon {
            background: #017E84; color: white; padding: 5px 15px; font-weight: 700; font-size: 12px; text-transform: uppercase; border-radius: 4px;
        }

        .field-row { margin-bottom: 15px; }
        .field-label { font-size: 12px; font-weight: 700; color: #666; text-transform: uppercase; margin-bottom: 4px; display: block; }
        .field-val { font-size: 15px; color: #212529; font-weight: 500; }

        .assign-box { background: #FFF8E1; border: 1px solid #FFECB3; padding: 15px; border-radius: 4px; margin-top: 20px; display: flex; gap: 10px; align-items: center; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 20px;">

    <div style="max-width: 900px; margin: 0 auto 10px auto; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 13px;">
            <a href="request-list.php" style="text-decoration:none; color: #017E84; font-weight: 600;">Requests</a> / 
            <span style="color: #666;"><?php echo htmlspecialchars($request['subject']); ?></span>
        </div>
        <a href="request-list.php" class="btn-outline" style="background:white; text-decoration:none; padding: 6px 12px; border-radius: 4px; color: #444; font-size: 12px; font-weight: 600; border: 1px solid #ccc;">
            <i class="ph-bold ph-arrow-left"></i> Back
        </a>
    </div>

    <div class="odoo-sheet">
        <div class="sheet-header">
            <div>
                <h1 style="margin: 0; color: #714B67; font-size: 24px;">
                    <?php echo htmlspecialchars($request['subject']); ?>
                </h1>
                <div style="margin-top: 5px; font-size: 13px; color: #888;">
                    Created on <?php echo date("F j, Y", strtotime($request['created_at'])); ?>
                </div>
            </div>
            <div class="status-ribbon">
                <?php echo $request['status']; ?>
            </div>
        </div>

        <div style="padding: 30px 40px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <div>
                    <div class="field-row">
                        <span class="field-label">Equipment</span>
                        <span class="field-val">
                            <i class="ph-fill ph-desktop-tower" style="color: #017E84;"></i> 
                            <?php echo $request['equipment_name']; ?>
                        </span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Request Type</span>
                        <span class="field-val"><?php echo ucfirst($request['request_type']); ?></span>
                    </div>
                </div>

                <div>
                    <div class="field-row">
                        <span class="field-label">Scheduled Date</span>
                        <span class="field-val">
                            <?php echo $request['scheduled_date'] ? $request['scheduled_date'] : '<span style="color:#999;">Not Scheduled</span>'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($_SESSION['user']['role'] === ROLE_MANAGER && !$request['assigned_technician_id']): ?>
            <div class="assign-box">
                <i class="ph-fill ph-warning-circle" style="color: #F57C00; font-size: 24px;"></i>
                <div style="flex: 1;">
                    <strong style="color: #E65100; font-size: 14px;">Action Required</strong>
                    <div style="font-size: 13px; color: #666;">This request needs a technician assigned.</div>
                </div>
                
                <form method="POST" style="display: flex; gap: 10px;">
                    <select name="technician_id" required style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">Select Technician...</option>
                        <?php while ($t = $techs->fetch_assoc()): ?>
                            <option value="<?php echo $t['id']; ?>">
                                <?php echo $t['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" name="assign" style="background: #F57C00; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 700; cursor: pointer;">
                        Assign
                    </button>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>