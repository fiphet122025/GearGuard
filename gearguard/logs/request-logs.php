<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

// Only Admin / Manager
if (!in_array($_SESSION['user']['role'], [ROLE_ADMIN, ROLE_MANAGER])) {
    die("Access denied");
}

$requestId = (int)($_GET['request_id'] ?? 0);

if ($requestId <= 0) {
    die("Invalid request");
}

// Fetch request info
$request = $conn->query("
    SELECT r.subject, e.equipment_name
    FROM maintenance_requests r
    JOIN equipment e ON r.equipment_id = e.id
    WHERE r.id = $requestId
")->fetch_assoc();

// Fetch logs
$logs = $conn->query("
    SELECT l.*, u.name AS user_name
    FROM request_logs l
    JOIN users u ON l.created_by = u.id
    WHERE l.request_id = $requestId
    ORDER BY l.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Logs | GearGuard</title>
    <link rel="stylesheet" href="../css/style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Specific Styles for Log Page */
        .log-container {
            max-width: 900px;
            margin: 20px auto;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .log-header {
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            background: #fdfdfd;
        }

        .log-title {
            margin: 0;
            color: var(--odoo-purple);
            font-size: 20px;
            font-weight: 700;
        }

        .log-subtitle {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        /* Timeline Style Table */
        .log-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .log-table th {
            background: #F9F9F9;
            color: #666;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .log-table td {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
            color: #333;
            vertical-align: top;
        }

        .log-table tr:last-child td { border-bottom: none; }
        .log-table tr:hover { background-color: #fcfcfc; }

        /* Status Pills */
        .status-pill {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            background: #eee;
            color: #555;
        }

        /* Avatar Circle */
        .user-avatar {
            width: 28px;
            height: 28px;
            background: #E0F2F1;
            color: #00695C;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }

        .user-cell {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body style="background-color: #F9F9F9; padding: 20px;">

    <div style="max-width: 900px; margin: 0 auto 15px auto;">
        <a href="../requests/request-view.php?id=<?php echo $requestId; ?>" class="btn-outline" style="text-decoration: none; padding: 6px 12px; border-radius: 4px; background: white; font-size: 13px; font-weight: 600; color: #444; border: 1px solid #ccc; display: inline-flex; align-items: center; gap: 5px;">
            <i class="ph-bold ph-arrow-left"></i> Back to Request
        </a>
    </div>

    <div class="log-container">
        
        <div class="log-header">
            <h2 class="log-title">Audit Log: <?php echo htmlspecialchars($request['subject']); ?></h2>
            <div class="log-subtitle">
                <i class="ph-fill ph-desktop-tower" style="margin-right: 5px;"></i>
                Equipment: <strong><?php echo $request['equipment_name']; ?></strong>
            </div>
        </div>

        <table class="log-table">
            <thead>
                <tr>
                    <th style="width: 180px;">Date & Time</th>
                    <th>User</th>
                    <th>Status Change</th>
                    <th>Note / Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $logs->fetch_assoc()): ?>
                <tr>
                    <td style="color: #666;">
                        <?php echo date("M d, Y H:i", strtotime($log['created_at'])); ?>
                    </td>
                    
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($log['user_name'], 0, 1)); ?>
                            </div>
                            <strong><?php echo htmlspecialchars($log['user_name']); ?></strong>
                        </div>
                    </td>

                    <td>
                        <?php if($log['old_status'] || $log['new_status']): ?>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <span class="status-pill"><?php echo $log['old_status'] ?? 'New'; ?></span>
                                <i class="ph-bold ph-arrow-right" style="font-size: 10px; color: #999;"></i>
                                <span class="status-pill" style="background: #E3F2FD; color: #0D47A1;">
                                    <?php echo $log['new_status'] ?? '-'; ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <span style="color: #999; font-style: italic;">No status change</span>
                        <?php endif; ?>
                    </td>

                    <td style="color: #444;">
                        <?php if(!empty($log['note'])): ?>
                            <i class="ph-fill ph-chat-text" style="color: #ccc; margin-right: 5px;"></i>
                            <?php echo htmlspecialchars($log['note']); ?>
                        <?php else: ?>
                            <span style="color: #ccc;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        </div>

</body>
</html>