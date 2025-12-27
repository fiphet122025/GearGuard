<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Logged-in user details
$currentUser = $_SESSION['user'];
$currentRole = $currentUser['role'];
