<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

$user = $_SESSION['user'];
$role = $user['role'];

$sql = "
    SELECT r.*, e.equipment_name 
    FROM maintenance_requests r
    JOIN equipment e ON r.equipment_id = e.id
";

if ($role === ROLE_EMPLOYEE) {
    $sql .= " WHERE r.created_by = " . $user['id'];
}

$sql .= " ORDER BY r.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Requests | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .req-table { width: 100%; border-collapse: collapse; background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .req-table th { background: #F9F9F9; color: #666; font-size: 12px; font-weight: 700; text-transform: uppercase; padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .req-table td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
        .req-table tr:hover { background: #fcfcfc; }
        
        .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; display: inline-block; }
        .status-new { background: #E9ECEF; color: #495057; }
        .status-in-progress { background: #FFF3CD; color: #856404; }
        .status-repaired { background: #D1E7DD; color: #0F5132; }
        .status-scrap { background: #F8D7DA; color: #721C24; }

        .type-badge { font-size: 11px; font-weight: 600; color: #017E84; background: #E0F2F1; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 30px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #714B67; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="ph-fill ph-clipboard-text"></i> Maintenance Requests
        </h2>
        <div>
            <?php if ($role === ROLE_EMPLOYEE): ?>
            <a href="request-create.php" style="background: #017E84; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 700; font-size: 13px; text-transform: uppercase;">
                <i class="ph-bold ph-plus"></i> New Request
            </a>
            <?php endif; ?>
            <a href="../dashboard/dashboard.php" class="btn-outline" style="margin-left: 10px; background: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; color: #444; border: 1px solid #ccc; font-size: 13px; font-weight: 600;">
                Dashboard
            </a>
        </div>
    </div>

    <table class="req-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Equipment</th>
                <th>Type</th>
                <th>Status</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr onclick="window.location='request-view.php?id=<?php echo $row['id']; ?>'" style="cursor: pointer;">
                <td style="font-weight: 600; color: #212529;">
                    <?php echo htmlspecialchars($row['subject']); ?>
                </td>
                <td style="color: #555;">
                    <i class="ph-bold ph-desktop-tower" style="margin-right: 5px; font-size: 12px;"></i>
                    <?php echo $row['equipment_name']; ?>
                </td>
                <td>
                    <span class="type-badge"><?php echo ucfirst($row['request_type']); ?></span>
                </td>
                <td>
                    <span class="status-badge <?php echo 'status-' . strtolower(str_replace(' ', '-', $row['status'])); ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </td>
                <td style="text-align: right;">
                    <a href="request-view.php?id=<?php echo $row['id']; ?>" style="color: #017E84; font-weight: 700; font-size: 12px; text-decoration: none;">
                        VIEW <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>