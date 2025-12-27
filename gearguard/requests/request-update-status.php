<?php
include "../config/auth.php";
include "../config/db.php";
include "../config/constants.php";

if ($_SESSION['user']['role'] !== ROLE_TECHNICIAN) {
    die("Access denied");
}

if (isset($_POST['update'])) {
    $stmt = $conn->prepare("
        UPDATE maintenance_requests
        SET status = ?, duration_hours = ?
        WHERE id = ? AND assigned_technician_id = ?
    ");

    $stmt->bind_param(
        "sdii",
        $_POST['status'],
        $_POST['duration_hours'],
        $_POST['request_id'],
        $_SESSION['user']['id']
    );

    $stmt->execute();
    header("Location: ../dashboard/kanban.php");
    exit;
}
