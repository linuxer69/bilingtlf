<?php
// login.php (Temporary bridge to fix 404 errors)

session_start();
require_once __DIR__ . '/config/db.php';

// If user is already logged in, redirect straight to user dashboard
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username_or_email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tlf_users WHERE username = ? OR email = ?");
            $stmt->execute([$username_or_email, $username_or_email]);
            $user = $stmt->fetch();

            if ($user) {
                $db_password = $user['password_hash'] ?? null;

                if ($db_password !== null) {
                    $hashed_input = hash('sha256', $password);
                    
                    if ($hashed_input === $db_password || password_verify($password, $db_password)) {
                        // REVERTED TO ORIGINAL SESSION KEYS FOR COMPATIBILITY
                        $_SESSION['user_logged_in'] = true;
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        
                        // Update last_login for admin widget
                        $updateStmt = $pdo->prepare("UPDATE tlf_users SET last_login = NOW() WHERE id = ?");
                        $updateStmt->execute([$user['id']]);

                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $error_message = "Invalid password.";
                    }
                } else {
                    $error_message = "Password column missing in DB.";
                }
            } else {
                $error_message = "Account not found.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In - TLFHost</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: #f8fafc; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size: 20px; font-weight: 700; margin-bottom: 20px; text-align: center; }
        .alert { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 16px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; }
        input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        .btn { width: 100%; padding: 12px; background: #1e293b; color: white; border: none; font-weight: 600; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
<div class="login-card">
    <h1>TLFHost Portal</h1>
    <?php if (!empty($error_message)): ?>
        <div class="alert"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form action="" method="POST">
        <div class="form-group">
            <label>Username or Email</label>
            <input type="text" name="username_or_email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Sign In</button>
    </form>
</div>
</body>
</html>