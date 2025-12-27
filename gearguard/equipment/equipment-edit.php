<?php
include "../config/auth.php";
include "../config/db.php";

$id = (int)$_GET['id'];

$equipment = $conn->query("SELECT * FROM equipment WHERE id = $id")->fetch_assoc();
$departments = $conn->query("SELECT * FROM departments");
$teams = $conn->query("SELECT * FROM maintenance_teams");

if (isset($_POST['update'])) {
    $stmt = $conn->prepare("
        UPDATE equipment SET
        equipment_name = ?, serial_number = ?, location = ?, department_id = ?, maintenance_team_id = ?, is_scrapped = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "sssiiii",
        $_POST['equipment_name'],
        $_POST['serial_number'],
        $_POST['location'],
        $_POST['department_id'],
        $_POST['maintenance_team_id'],
        $_POST['is_scrapped'],
        $id
    );

    $stmt->execute();
    header("Location: equipment-list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Equipment | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .odoo-form-card { background: white; border: 1px solid #e0e0e0; border-radius: 4px; padding: 30px; max-width: 800px; margin: 20px auto; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; margin-bottom: 15px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; color: #444; margin-bottom: 5px; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 20px;">

    <div style="max-width: 800px; margin: 0 auto 10px auto; display: flex; align-items: center; justify-content: space-between;">
        <h2 style="color: #714B67; margin: 0;">Edit: <?php echo $equipment['equipment_name']; ?></h2>
        <a href="equipment-list.php" class="btn-outline" style="text-decoration:none; padding: 6px 12px; border-radius: 4px; color: #666; font-size: 13px; font-weight: 600; border: 1px solid #ccc; background: white;">
            <i class="ph-bold ph-arrow-left"></i> Back
        </a>
    </div>

    <div class="odoo-form-card">
        <form method="POST">
            
            <label class="form-label">Equipment Name</label>
            <input name="equipment_name" value="<?php echo $equipment['equipment_name']; ?>" class="form-control" required style="font-weight: 600;">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label class="form-label">Serial Number</label>
                    <input name="serial_number" value="<?php echo $equipment['serial_number']; ?>" class="form-control" required>

                    <label class="form-label">Location</label>
                    <input name="location" value="<?php echo $equipment['location']; ?>" class="form-control">
                    
                    <label class="form-label">Status</label>
                    <select name="is_scrapped" class="form-control">
                        <option value="0" <?php if (!$equipment['is_scrapped']) echo "selected"; ?>>Active</option>
                        <option value="1" <?php if ($equipment['is_scrapped']) echo "selected"; ?>>Scrapped</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-control">
                        <?php while ($d = $departments->fetch_assoc()): ?>
                            <option value="<?php echo $d['id']; ?>" 
                                <?php if ($d['id'] == $equipment['department_id']) echo "selected"; ?>>
                                <?php echo $d['department_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <label class="form-label">Maintenance Team</label>
                    <select name="maintenance_team_id" class="form-control">
                        <?php while ($t = $teams->fetch_assoc()): ?>
                            <option value="<?php echo $t['id']; ?>" 
                                <?php if ($t['id'] == $equipment['maintenance_team_id']) echo "selected"; ?>>
                                <?php echo $t['team_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="submit" name="update" style="background: #017E84; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: 700; text-transform: uppercase; cursor: pointer;">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

</body>
</html>