<?php
include "../config/db.php";
include "../config/constants.php";

$error = "";
$success = "";

if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $department = $_POST['department_id'];

    if (!in_array($role, [ROLE_EMPLOYEE, ROLE_TECHNICIAN])) {
        $error = "Invalid role selected";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password, role, department_id)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssi",
            $name,
            $email,
            $hashedPassword,
            $role,
            $department
        );

        if ($stmt->execute()) {
            $success = "Account created successfully. Please login.";
        } else {
            $error = "Email already exists";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | GearGuard</title>
    <link rel="stylesheet" href="style_auth.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="auth-card">
    <div class="auth-brand">
        <i class="ph-fill ph-user-plus"></i> Join GearGuard
    </div>
    <div class="auth-subtitle">Create your new account</div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="ph-bold ph-warning-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
        <div class="alert alert-success">
            <i class="ph-bold ph-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control"  required>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
        </div>

        <div class="form-group">
            <label class="form-label">Role</label>
            <select name="role" class="form-control" required>
                <option value="">Select Role...</option>
                <option value="employee">Employee (Standard User)</option>
                <option value="technician">Technician (Maintenance)</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Department ID (Optional)</label>
            <input type="number" name="department_id" class="form-control" placeholder="e.g. 1">
        </div>

        <button type="submit" name="signup" class="btn-primary">Create Account</button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="login.php" class="auth-link">Log In</a>
    </div>
</div>

</body>
</html>