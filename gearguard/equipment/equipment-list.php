
<?php
include "../config/auth.php";
include "../config/db.php";

$result = $conn->query("
    SELECT e.*, d.department_name 
    FROM equipment e
    JOIN departments d ON e.department_id = d.id
    ORDER BY e.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipment List | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .eq-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .eq-card { background: white; border: 1px solid #e0e0e0; border-radius: 4px; padding: 15px; position: relative; transition: 0.2s; display: flex; flex-direction: column; }
        .eq-card:hover { border-color: #714B67; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .eq-status { position: absolute; top: 15px; right: 15px; font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 700; text-transform: uppercase; }
        .status-active { background: #e6f9eb; color: #28a745; }
        .status-scrapped { background: #fbeaea; color: #dc3545; }
        .btn-teal { background: #017E84; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 700; font-size: 13px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px; }
    </style>
</head>
<body style="background-color: #ebf2ff;; padding: 30px;">

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="color: #714B67; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="ph-fill ph-desktop-tower"></i> Equipment
        </h2>
        <div>
            <a href="equipment-add.php" class="btn-teal">
                <i class="ph-bold ph-plus"></i> New
            </a>
            <a href="../dashboard/dashboard.php" style="margin-left: 10px; color: #666; text-decoration: none; font-size: 13px; font-weight: 600;">
                <i class="ph-bold ph-squares-four"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="eq-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="eq-card">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <div style="background: #F3EEF1; color: #714B67; padding: 10px; border-radius: 4px;">
                        <i class="ph-fill ph-monitor" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: #212529; font-size: 15px;">
                            <?php echo htmlspecialchars($row['equipment_name']); ?>
                        </div>
                        <div style="font-size: 12px; color: #666;">
                            SN: <?php echo $row['serial_number']; ?>
                        </div>
                    </div>
                </div>

                <?php if ($row['is_scrapped']): ?>
                    <span class="eq-status status-scrapped">Scrapped</span>
                <?php else: ?>
                    <span class="eq-status status-active">Active</span>
                <?php endif; ?>

                <div style="font-size: 12px; color: #555; margin-top: 5px; line-height: 1.6;">
                    <div><strong>Dept:</strong> <?php echo $row['department_name']; ?></div>
                    <div><strong>Loc:</strong> <?php echo $row['location']; ?></div>
                </div>

                <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #f0f0f0; display: flex; gap: 10px;">
                    <a href="equipment-view.php?id=<?php echo $row['id']; ?>" style="color: #017E84; font-weight: 600; font-size: 12px; text-decoration: none;">VIEW</a>
                    <a href="equipment-edit.php?id=<?php echo $row['id']; ?>" style="color: #666; font-weight: 600; font-size: 12px; text-decoration: none;">EDIT</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>