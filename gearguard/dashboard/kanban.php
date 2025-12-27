<?php
include "../config/auth.php";
include "../config/constants.php";

$role = strtolower(trim($_SESSION['user']['role'] ?? ''));
if (!in_array($role, [ROLE_TECHNICIAN, ROLE_MANAGER, ROLE_ADMIN], true)) {
    die("Access denied");
}

$name = htmlspecialchars($_SESSION['user']['name'] ?? 'User');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kanban | GearGuard</title>
    <link rel="stylesheet" href="style_dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="sidebar">
        <div class="brand"><i class="ph-fill ph-gear-six"></i> GearGuard</div>
        <div class="menu">
            <a href="dashboard.php" class="menu-item"><i class="ph-bold ph-squares-four"></i> Dashboard</a>
            <a href="kanban.php" class="menu-item active"><i class="ph-bold ph-kanban"></i> Kanban Board</a>
            <a href="../auth/logout.php" class="menu-item"><i class="ph-bold ph-sign-out"></i> Logout</a>
        </div>
        <div class="user-profile">
            <div style="font-weight: 600;"><?php echo $name; ?></div>
            <div class="user-role"><?php echo ucfirst($role); ?></div>
        </div>
</div>

<div class="main-content">
    
    <div class="top-header">
        <div class="page-title">
            <i class="ph-fill ph-kanban" style="margin-right: 10px;"></i>
            Maintenance Board
        </div>
        <div style="display: flex; gap: 10px;">
            <input type="text" placeholder="Search..." style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;">
            <button class="btn btn-outline" style="background: white;"><i class="ph-bold ph-funnel"></i> Filter</button>
        </div>
    </div>

    <div class="kanban-board">
            <?php
            // Load requests grouped by status
            include "../config/db.php";
            $sql = "SELECT r.*, e.equipment_name FROM maintenance_requests r JOIN equipment e ON r.equipment_id = e.id ORDER BY r.created_at DESC";
            $res = $conn->query($sql);

            $groups = [
                'New' => [],
                'In Progress' => [],
                'Repaired' => [],
                'Scrap' => []
            ];
            while ($row = $res->fetch_assoc()) {
                $status = $row['status'] ?? 'New';
                if (!isset($groups[$status])) { $groups[$status] = []; }
                $groups[$status][] = $row;
            }

            $map = [
                'New' => 'status-new',
                'In Progress' => 'status-progress',
                'Repaired' => 'status-repaired',
                'Scrap' => 'status-scrap'
            ];

            foreach ($groups as $label => $items) {
                $colClass = $map[$label] ?? 'status-new';
                echo "<div class='kanban-col " . $colClass . "' data-status='" . htmlspecialchars($label) . "'>";
                echo "<h4><i class='ph-fill ph-circle' style='font-size:12px;'></i> " . htmlspecialchars($label) . "</h4>";
                foreach ($items as $r) {
                    $rid = (int)$r['id'];
                    $title = htmlspecialchars($r['subject']);
                    $equip = htmlspecialchars($r['equipment_name']);
                    $assigned = (int)$r['assigned_technician_id'];
                    $draggable = ($role === ROLE_TECHNICIAN) ? "draggable='true'" : '';
                    echo "<div class='kanban-card' " . $draggable . " data-id='" . $rid . "' data-assigned='" . $assigned . "'>";
                    echo "<div style='font-weight:700;color:#212529;margin-bottom:6px;'>" . $title . "</div>";
                    echo "<div style='font-size:13px;color:#666;margin-bottom:8px;'><i class='ph-bold ph-desktop-tower' style='margin-right:6px;'></i>" . $equip . "</div>";
                    echo "</div>";
                }
                echo "</div>";
            }
            ?>

    </div>

</div>

</body>
</html>

<?php if ($role === ROLE_TECHNICIAN): ?>
<script>
// Drag and drop handlers for kanban (only for technicians)
document.addEventListener('DOMContentLoaded', function () {
    let dragged = null;

    document.querySelectorAll('.kanban-card[draggable="true"]').forEach(card => {
        card.addEventListener('dragstart', (e) => {
            dragged = card;
            card.classList.add('dragging');
            e.dataTransfer.setData('text/plain', card.dataset.id);
            e.dataTransfer.effectAllowed = 'move';
        });
        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            dragged = null;
        });
    });

    document.querySelectorAll('.kanban-col').forEach(col => {
        col.addEventListener('dragover', (e) => {
            e.preventDefault();
            col.classList.add('drag-over');
        });
        col.addEventListener('dragleave', () => {
            col.classList.remove('drag-over');
        });
        col.addEventListener('drop', (e) => {
            e.preventDefault();
            col.classList.remove('drag-over');
            const id = e.dataTransfer.getData('text/plain');
            const card = document.querySelector(".kanban-card[data-id='" + id + "']");
            if (!card) return;
            // append in DOM
            col.appendChild(card);
            // send update to server
            const status = col.dataset.status;
            fetch('kanban-update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ request_id: id, status: status })
            }).then(r => r.json()).then(resp => {
                if (!resp.success) {
                    alert('Update failed: ' + (resp.error || 'unknown'));
                    location.reload();
                }
            }).catch(() => { alert('Network error'); location.reload(); });
        });
    });
});
</script>
<?php endif; ?>
