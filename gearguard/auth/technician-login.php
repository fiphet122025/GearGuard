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
         AND role = ?"
    );

    $technicianRole = ROLE_TECHNICIAN;
    $stmt->bind_param("ss", $email, $technicianRole);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: ../dashboard/kanban.php");
        exit;
    } else {
        $error = "Invalid technician credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Technician Login | GearGuard</title>
    <link rel="stylesheet" href="style_auth.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="auth-card tech-portal">
    <div class="auth-brand">
        <i class="ph-fill ph-wrench" style="color: var(--odoo-teal);"></i> GearGuard
    </div>
    <div class="auth-subtitle">Technician Portal Access</div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="ph-bold ph-warning-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Technician Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" name="login" class="btn-primary">
            Access Portal
        </button>
    </form>

    <div class="auth-footer">
        <a href="login.php" class="auth-link">
            <i class="ph-bold ph-arrow-left"></i> Back to User Login
        </a>
    </div>
</div>

</body>
</html>