<?php
session_start();

// TEMPORARY DEV LOGIN (REMOVE LATER)
$_SESSION['user'] = [
    'id' => 999,
    'name' => 'Admin User',
    'email' => 'admin@gearguard.com',
    'role' => 'admin'
];

header("Location: ../dashboard/dashboard.php");
exit;
