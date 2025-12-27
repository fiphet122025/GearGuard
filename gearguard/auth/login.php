<?php
session_start();
include "../config/db.php";
include "../config/constants.php";

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT * FROM users 
         WHERE email = ? 
         AND role IN (?, ?, ?)"
    );

    $roleEmployee = ROLE_EMPLOYEE;
    $roleManager  = ROLE_MANAGER;
    $roleAdmin    = ROLE_ADMIN;
    
    $stmt->bind_param(
        "ssss",
        $email,
        $roleEmployee,
        $roleManager,
        $roleAdmin
    );

    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: ../dashboard/dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login | GearGuard</title>
    <link rel="stylesheet" href="style_auth.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="auth-card">
    <div class="auth-brand">
        <i class="ph-fill ph-gear-six"></i> GearGuard
    </div>
    <div class="auth-subtitle">Sign in to your account</div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="ph-bold ph-warning-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" name="login" class="btn-primary">Log In</button>
    </form>

    <div class="auth-footer">
        <p>Are you a technician? <a href="technician-login.php" class="auth-link">Login here</a></p>
        <p>Don't have an account? <a href="signup.php" class="auth-link">Sign up</a></p>
    </div>
</div>

</body>
</html>