<?php
include "../config/auth.php";
include "../config/db.php";

$departments = $conn->query("SELECT * FROM departments");
$teams = $conn->query("SELECT * FROM maintenance_teams");

if (isset($_POST['save'])) {
    $stmt = $conn->prepare("
        INSERT INTO equipment 
        (equipment_name, serial_number, purchase_date, warranty_end_date, location, department_id, maintenance_team_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssi",
        $_POST['equipment_name'],
        $_POST['serial_number'],
        $_POST['purchase_date'],
        $_POST['warranty_end_date'],
        $_POST['location'],
        $_POST['department_id'],
        $_POST['maintenance_team_id']
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
    <title>Add Equipment | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Local Form Styling to match Odoo */
        .odoo-form-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 30px;
            max-width: 800px;
            margin: 20px auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #444;
            margin-bottom: 5px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-control:focus { border-color: #714B67; outline: none; }
        .page-header { max-width: 800px; margin: 0 auto 10px auto; display: flex; align-items: center; justify-content: space-between; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 20px;">

    <div class="page-header">
        <h2 style="color: #714B67; margin: 0;">New Equipment</h2>
        <a href="equipment-list.php" class="btn-outline" style="text-decoration:none; padding: 6px 12px; border-radius: 4px; color: #666; font-size: 13px; font-weight: 600; border: 1px solid #ccc; background: white;">
            <i class="ph-bold ph-arrow-left"></i> Discard
        </a>
    </div>

    <div class="odoo-form-card">
        <form method="POST">
            
            <div style="margin-bottom: 20px;">
                <label style="display:block; font-size: 12px; color: #017E84; font-weight: 700; text-transform: uppercase;">Equipment Name</label>
                <input name="equipment_name" class="form-control" placeholder="e.g. Samsung Monitor S24" style="font-size: 20px; font-weight: 600; border: none; border-bottom: 1px solid #ccc; padding-left: 0; border-radius: 0;" required>
            </div>

            <div class="form-grid">
                <div>
                    <div class="form-group">
                        <label>Serial Number</label>
                        <input name="serial_number" class="form-control" placeholder="SN-123456" required>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Location</label>
                        <input name="location" class="form-control" placeholder="Office 1, Desk 4">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Department</label>
                        <select name="department_id" class="form-control" required>
                            <option value="">Select Department...</option>
                            <?php while ($d = $departments->fetch_assoc()): ?>
                                <option value="<?php echo $d['id']; ?>">
                                    <?php echo $d['department_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="form-group">
                        <label>Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Warranty End Date</label>
                        <input type="date" name="warranty_end_date" class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <label>Maintenance Team</label>
                        <select name="maintenance_team_id" class="form-control" required>
                            <option value="">Select Team...</option>
                            <?php while ($t = $teams->fetch_assoc()): ?>
                                <option value="<?php echo $t['id']; ?>">
                                    <?php echo $t['team_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                <button type="submit" name="save" style="background: #017E84; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: 700; text-transform: uppercase; cursor: pointer;">
                    Save Equipment
                </button>
            </div>

        </form>
    </div>

</body>
</html>