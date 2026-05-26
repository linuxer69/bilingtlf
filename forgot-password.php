<?php
// forgot-password.php

session_start();
require_once __DIR__ . '/config/db.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email'] ?? '');

    if (!empty($username_or_email)) {
        try {
            // Find user
            $stmt = $pdo->prepare("SELECT id, email FROM tlf_users WHERE username = ? OR email = ?");
            $stmt->execute([$username_or_email, $username_or_email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate a highly secure random token
                $token = bin2hex(random_bytes(32));
                // Set expiration time to 1 hour from now
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Save token in DB
                $updateStmt = $pdo->prepare("UPDATE tlf_users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
                $updateStmt->execute([$token, $expires, $user['id']]);

                // Create the absolute secure reset URL
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset-password.php?token=" . $token;
                
                // In production, send this via mail(). For now, we display it beautifully.
                $success_message = "Recovery link generated! For development, click here: <br><a href='{$reset_link}' style='color:#166534; font-weight:700;'>[ Reset Password Link ]</a>";
            } else {
                $error_message = "No account found with that username or email address.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please enter your account details.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - TLFHost</title>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: "Source Sans 3", sans-serif; font-size: 14px; color: #4d5592; background-color: #ffffff; }
        .main { display: flex; flex-direction: row; height: 100vh; }
        .main_left { display: flex; flex-direction: column; width: 100%; max-height: 100vh; }
        .main_header { display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 1.5rem; }
        .main_content { display: flex; flex-direction: column; flex: 1 1 auto; align-items: center; justify-content: center; overflow-y: auto; width: 100%; position: relative; padding: 20px; }
        .main_side { display: none; position: relative; flex-basis: 41.7%; background-image: linear-gradient(to bottom right, #011B67, #110BF5); overflow: hidden; line-height: 1.25; }
        @media (min-width: 48em) { .main_left { flex-basis: 58.3%; } .main_side { display: block; } }
        .reassurance_wording { position: absolute; display: flex; flex-direction: column; gap: 1.5rem; width: 66.6666666667%; top: 50%; left: 50%; transform: translate(-50%,-50%); z-index: 2; }
        .reassurance_wording_title { color: #fff; font-size: 36px; font-weight: 700; }
        .reassurance_wording_description { color: #fff; font-size: 16px; font-weight: 400; line-height: 1.6; }
        a { text-decoration: none; font-weight: 600; color: #0050d7; }
        a:hover { text-decoration: underline; }
        .btn { width: 100%; height: 50px; border-radius: 6px; cursor: pointer; text-align: center; font-size: 16px; font-weight: bold; transition: all 0.2s ease; }
        .btn-primary { color: #ffffff; background: #0050d7; border: none; }
        .btn-primary:hover { background-color: #000e9c; }
        .ovh-field { display: flex; flex-wrap: wrap; gap: 4px; align-items: center; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; margin-bottom: 20px; }
        .ovh-field:focus-within { border-color: #000e9c; box-shadow: 0 0 0 1px #000e9c; }
        .ovh-field-input { flex: 1 1 auto; display: inline-block; width: 100%; }
        .ovh-field-input > input { background: none; border: 0px; box-sizing: border-box; height: 44px; padding: 10px 14px; width: 100%; color: #4d5592; font-size: 1rem; }
        .ovh-field-input > input:focus { outline: none; }
        .ovh-field-label { color: #00185e; font-size: 15px; font-weight: 600; margin-bottom: 8px; }
        .signin-title { font-size: 32px; font-weight: 700; color: #00185e; margin-bottom: 6px; }
        .login-panel { display: flex; flex-direction: column; gap: 24px; width: 100%; }
        .alert-error { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px; border-radius: 6px; font-size: 14px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px; border-radius: 6px; font-size: 14px; line-height: 1.5; }
        .logo-text { font-size: 22px; font-weight: 700; color: #011B67; }
        .logo-text span { color: #0050d7; }
        .back-link { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; }
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
                    <div class="back-link">
                        <a href="login.php">← Back to Login</a>
                    </div>
                    <div class="signin-title">Forgot password?</div>
                    <div style="color: #4d5592; font-size: 14px; margin-bottom: 10px;">
                        Enter your username or email address and we will generate a secure validation link.
                    </div>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success_message)): ?>
                        <div class="alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div>
                            <div class="ovh-field-label">Account ID or email address *</div>
                            <div class="ovh-field">
                                <div class="ovh-field-input">
                                    <input type="text" name="username_or_email" autofocus required="required" value="<?php echo htmlspecialchars($username_or_email ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 24px;">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="main_side">
        <div class="reassurance_wording">
            <div class="reassurance_wording_title">Account Recovery</div>
            <div class="reassurance_wording_description">
                Security tokens are fully encrypted and set to automatically expire to guarantee standard infrastructure privacy access.
            </div>
        </div>
    </div>
</div>
</body>
</html>