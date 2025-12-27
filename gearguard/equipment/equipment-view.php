<?php
include "../config/auth.php";
include "../config/db.php";

$id = (int)$_GET['id'];

$equipment = $conn->query("
    SELECT e.*, d.department_name 
    FROM equipment e
    JOIN departments d ON e.department_id = d.id
    WHERE e.id = $id
")->fetch_assoc();

$countResult = $conn->query("
    SELECT COUNT(*) AS total 
    FROM maintenance_requests 
    WHERE equipment_id = $id 
    AND status != 'Repaired'
");
$count = $countResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Equipment | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .odoo-sheet { background: white; border: 1px solid #dcdcdc; border-radius: 4px; margin: 20px auto; max-width: 900px; padding: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .sheet-header { padding: 20px 40px; border-bottom: 1px solid #eee; position: relative; }
        .smart-button {
            position: absolute; right: 20px; top: 20px;
            border: 1px solid #ddd; background: white; padding: 5px 15px; border-radius: 2px;
            display: flex; align-items: center; gap: 10px; text-decoration: none; color: #444; min-width: 140px; cursor: pointer;
        }
        .smart-button:hover { background: #f8f8f8; }
        .sb-icon { font-size: 20px; color: #666; }
        .sb-info { display: flex; flex-direction: column; }
        .sb-num { font-weight: 700; color: #017E84; font-size: 14px; }
        .sb-label { font-size: 11px; color: #666; }
        
        .field-row { display: flex; margin-bottom: 10px; }
        .field-label { width: 150px; font-weight: 700; font-size: 13px; color: #444; border-right: 3px solid transparent; }
        .field-val { font-size: 13px; color: #000; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 20px;">

    <div style="max-width: 900px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 13px;">
            <a href="equipment-list.php" style="text-decoration:none; color: #017E84; font-weight: 600;">Equipment</a> / 
            <span style="color: #666;"><?php echo htmlspecialchars($equipment['equipment_name']); ?></span>
        </div>
        <a href="equipment-list.php" class="btn-outline" style="text-decoration:none; padding: 5px 10px; background:white; border:1px solid #ccc; border-radius: 4px; color: #333; font-size: 12px; font-weight: 600;">
            <i class="ph-bold ph-arrow-left"></i> Back
        </a>
    </div>

    <div class="odoo-sheet">
        <div class="sheet-header">
            
            <a href="../requests/request-list.php?equipment_id=<?php echo $id; ?>" class="smart-button">
                <i class="ph-bold ph-wrench sb-icon"></i>
                <div class="sb-info">
                    <span class="sb-num"><?php echo $count; ?></span>
                    <span class="sb-label">Maintenance</span>
                </div>
            </a>

            <h1 style="margin: 0; color: #714B67; font-size: 24px;"><?php echo htmlspecialchars($equipment['equipment_name']); ?></h1>
            <div style="font-size: 12px; color: #666; margin-top: 5px;">
                Serial: <strong><?php echo $equipment['serial_number']; ?></strong>
            </div>
        </div>

        <div style="padding: 30px 40px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                
                <div>
                    <div class="field-row">
                        <div class="field-label">Department</div>
                        <div class="field-val"><?php echo $equipment['department_name']; ?></div>
                    </div>
                    <div class="field-row">
                        <div class="field-label">Location</div>
                        <div class="field-val"><?php echo $equipment['location']; ?></div>
                    </div>
                </div>

                <div>
                     <div class="field-row">
                        <div class="field-label">Next Action</div>
                        <div class="field-val" style="color: #017E84; font-weight: 700;">Scheduled Maintenance</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
