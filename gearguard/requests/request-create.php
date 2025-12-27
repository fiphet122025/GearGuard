<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

// Only employee / manager
if (!in_array($_SESSION['user']['role'], [ROLE_EMPLOYEE, ROLE_MANAGER])) {
    die("Access denied");
}

$equipmentList = $conn->query("
    SELECT id, equipment_name 
    FROM equipment 
    WHERE is_scrapped = 0
");

if (isset($_POST['save'])) {
    $equipmentId = $_POST['equipment_id'];

    // Auto-fetch maintenance team from equipment
    $teamQuery = $conn->query("
        SELECT maintenance_team_id 
        FROM equipment 
        WHERE id = $equipmentId
    ");
    $teamId = $teamQuery->fetch_assoc()['maintenance_team_id'];

    $stmt = $conn->prepare("
        INSERT INTO maintenance_requests
        (subject, request_type, equipment_id, maintenance_team_id, scheduled_date, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssiiii",
        $_POST['subject'],
        $_POST['request_type'],
        $equipmentId,
        $teamId,
        $_POST['scheduled_date'],
        $_SESSION['user']['id']
    );

    $stmt->execute();
    header("Location: request-list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Request | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .odoo-form-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 30px;
            max-width: 800px;
            margin: 20px auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; margin-bottom: 20px; }
        .form-control:focus { border-color: #714B67; outline: none; }
        .form-label { display: block; font-size: 13px; font-weight: 700; color: #444; margin-bottom: 5px; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 20px;">

    <div style="max-width: 800px; margin: 0 auto 10px auto; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="color: #714B67; margin: 0;">New Maintenance Request</h2>
        <a href="../dashboard/dashboard.php" class="btn-outline" style="text-decoration:none; padding: 6px 12px; border-radius: 4px; color: #666; font-size: 13px; font-weight: 600; border: 1px solid #ccc; background: white;">
            <i class="ph-bold ph-arrow-left"></i> Discard
        </a>
    </div>

    <div class="odoo-form-card">
        <form method="POST">
            
            <div style="margin-bottom: 25px;">
                <label style="display:block; font-size: 12px; color: #017E84; font-weight: 700; text-transform: uppercase;">Subject / Issue</label>
                <input name="subject" class="form-control" placeholder="e.g. Conveyor Belt Stuck" style="font-size: 20px; font-weight: 600; border: none; border-bottom: 1px solid #ccc; padding-left: 0; border-radius: 0; margin-bottom: 5px;" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <label class="form-label">Equipment</label>
                    <select name="equipment_id" class="form-control" required>
                        <option value="">Select Equipment...</option>
                        <?php while ($e = $equipmentList->fetch_assoc()): ?>
                            <option value="<?php echo $e['id']; ?>">
                                <?php echo $e['equipment_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <label class="form-label">Request Type</label>
                    <select name="request_type" class="form-control" required>
                        <option value="<?php echo REQUEST_CORRECTIVE; ?>">Corrective (Repair)</option>
                        <option value="<?php echo REQUEST_PREVENTIVE; ?>">Preventive (Maintenance)</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Scheduled Date</label>
                    <input type="date" name="scheduled_date" class="form-control">
                    
                    <div style="background: #F3EEF1; padding: 15px; border-radius: 4px; font-size: 12px; color: #714B67;">
                        <i class="ph-fill ph-info"></i> The maintenance team will be automatically assigned based on the selected equipment.
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="submit" name="save" style="background: #017E84; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: 700; text-transform: uppercase; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="ph-bold ph-check"></i> Submit Request
                </button>
            </div>

        </form>
    </div>

</body>
</html>