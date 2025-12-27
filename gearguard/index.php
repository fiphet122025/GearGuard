<?php
session_start();

// If user is already logged in
if (isset($_SESSION['user'])) {

    $role = $_SESSION['user']['role'];

    // Redirect based on role
    if ($role === 'technician') {
        header("Location: dashboard/kanban.php");
        exit;
    } else {
        header("Location: dashboard/dashboard.php");
        exit;
    }

}

// If not logged in → go to login
header("Location: auth/login.php");
exit;
