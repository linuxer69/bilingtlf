<?php
// reset-password.php

session_start();
require_once __DIR__ . '/config/db.php';

$error_message = '';
$success_message = '';
$valid_token = false;
$user_id = null;

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');

if (!empty($token)) {
    try {
        // Verify token authenticity and expiration time window
        $stmt = $pdo->prepare("SELECT id FROM tlf_users WHERE reset_token = ? AND reset_token_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            $valid_token = true;
            $user_id = $user['id'];
        } else {
            $error_message = "The recovery link is invalid or has expired. Please request a new one.";
        }
    } catch (PDOException $e) {
        $error_message = "Database verification error: " . $e->getMessage();
    }
} else {
    $error_message = "Missing secure verification token parameter.";
}

// Process password change request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (!empty($password) && !empty($confirm_password)) {
        if (strlen($password) < 6) {
            $error_message = "Password must be at least 6 characters long.";
        } elseif ($password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        } else {
            try {
                // Hash using sha256 to remain fully compliant with your DB records
                $hashed_password = hash('sha256', $password);

                // Update password and completely invalidate the consumed token
                $updateStmt = $pdo->prepare("UPDATE tlf_users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
                $updateStmt->execute([$hashed_password, $user_id]);

                $success_message = "Your password has been reset successfully! <a href='login.php' style='font-weight:700; color:#166534;'>Click here to Login</a>";
                $valid_token = false; // Hide form on success
            } catch (PDOException $e) {
                $error_message = "Failed to update security parameters: " . $e->getMessage();
            }
        }
    } else {
        $error_message = "Please fill in all security credential fields.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - TLFHost</title>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: "Source Sans 3", sans-serif; font-size: 14px; color: #4d5592; background-color: #ffffff; }
        .main { display: flex; flex-direction: row; height: 100vh; }
        .main_left { display: flex; flex-direction: column; width: 100%; max-height: 100vh; }
        .main_header { display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 1.5rem; }
        .main_content { display: flex; flex-direction: column; flex: 1 1 auto; align-items: center; justify-content: center; overflow-y: auto; width: 100%; position: relative; padding: 20px; }
        .main_side { display: none; position: relative; flex-basis: 41.7%; background-image: linear-gradient(to bottom right, #011B67, #110BF5); overflow: hidden; }
        @media (min-width: 48em) { .main_left { flex-basis: 58.3%; } .main_side { display: block; } }
        .reassurance_wording { position: absolute; display: flex; flex-direction: column; gap: 1.5rem; width: 66.6666666667%; top: 50%; left: 50%; transform: translate(-50%,-50%); z-index: 2; }
        .reassurance_wording_title { color: #fff; font-size: 36px; font-weight: 700; }
        .reassurance_wording_description { color: #fff; font-size: 16px; font-weight: 400; line-height: 1.6; }
        .btn { width: 100%; height: 50px; border-radius: 6px; cursor: pointer; text-align: center; font-size: 16px; font-weight: bold; }
        .btn-primary { color: #ffffff; background: #0050d7; border: none; }
        .btn-primary:hover { background-color: #000e9c; }
        .ovh-field { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; margin-bottom: 16px; }
        .ovh-field:focus-within { border-color: #000e9c; }
        .ovh-field-input { flex: 1 1 auto; display: inline-block; width: 100%; }
        .ovh-field-input > input { background: none; border: 0px; box-sizing: border-box; height: 44px; padding: 10px 14px; width: 100%; color: #4d5592; font-size: 1rem; }
        .ovh-field-input > input:focus { outline: none; }
        .ovh-field-label { color: #00185e; font-size: 15px; font-weight: 600; margin-bottom: 6px; }
        .signin-title { font-size: 32px; font-weight: 700; color: #00185e; margin-bottom: 6px; }
        .login-panel { display: flex; flex-direction: column; gap: 20px; width: 100%; }
        .alert-error { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: 6px; font-size: 14px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px; border-radius: 6px; font-size: 14px; }
        .logo-text { font-size: 22px; font-weight: 700; color: #011B67; }
        .logo-text span { color: #0050d7; }
    </style>
</head>
<body>
<div class="main">
    <div class="main_left">
        <div class="main_header">
            <div class="logo-text">TLF<span>Host</span></div>
        </div>
        <div class="main_content">
            <div style="width: 100%; max-width: 28rem;">
                <div class="login-panel">
                    <div class="signin-title">Set New Password</div>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success_message)): ?>
                        <div class="alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>

                    <?php if ($valid_token): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                            
                            <div>
                                <div class="ovh-field-label">New Password *</div>
                                <div class="ovh-field">
                                    <div class="ovh-field-input">
                                        <input type="password" name="password" required="required" autofocus/>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="ovh-field-label">Confirm New Password *</div>
                                <div class="ovh-field">
                                    <div class="ovh-field-input">
                                        <input type="password" name="confirm_password" required="required"/>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 24px;">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="main_side"></div>
</div>
</body>
</html>