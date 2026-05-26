<?php
// register.php (Client Registration)

session_start();
require_once __DIR__ . '/config/db.php';

// If user is already logged in, redirect straight to user dashboard
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($full_name) && !empty($email) && !empty($password)) {
        try {
            // 1. Check if username or email already exists to prevent duplication
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM tlf_users WHERE username = ? OR email = ?");
            $checkStmt->execute([$username, $email]);
            if ($checkStmt->fetchColumn() > 0) {
                $error_message = "Username or Email address is already registered.";
            } else {
                // 2. Hash password using standard secure bcrypt
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                // 3. FIXED: Insert all required fields including 'username' to prevent default value error
                $insertStmt = $pdo->prepare("INSERT INTO tlf_users (username, full_name, email, password_hash, status, created_at, updated_at) VALUES (?, ?, ?, ?, 'active', NOW(), NOW())");
                $insertStmt->execute([$username, $full_name, $email, $password_hash]);

                $success_message = "Registration successful! You can now log in.";
            }
        } catch (PDOException $e) {
            $error_message = "Registration failed due to database error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in all the required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - TLFHost</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: #f8fafc; color: #0f172a; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .register-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 40px; width: 100%; max-width: 440px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        h1 { font-size: 24px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        p.subtitle { color: #64748b; font-size: 14px; text-align: center; margin-bottom: 24px; }
        .alert { padding: 12px; border-radius: 6px; font-size: 14px; margin-bottom: 16px; font-weight: 500; }
        .alert-danger { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; text-transform: uppercase; }
        input { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .btn-register { width: 100%; padding: 12px; background: #1e293b; color: white; border: none; font-size: 14px; font-weight: 600; border-radius: 6px; cursor: pointer; margin-top: 8px; }
        .btn-register:hover { background: #0f172a; }
        .login-link { display: block; text-align: center; margin-top: 20px; font-size: 14px; color: #3b82f6; text-decoration: none; }
        .login-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="register-card">
    <h1>Create Account</h1>
    <p class="subtitle">Join TLFHost specialized infrastructure portal</p>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autocomplete="username">
        </div>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required autocomplete="email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn-register">Register Account</button>
    </form>

    <a href="login.php" class="login-link">Already have an account? Sign In</a>
</div>

</body>
</html>