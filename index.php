<?php
// index.php (Client Portal Gateway)

session_start();
require_once __DIR__ . '/config/db.php';

// If user is already logged in, redirect straight to user dashboard securely
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TLFHost Client Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: #f8fafc; color: #0f172a; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .gateway-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 40px; width: 100%; max-width: 480px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); text-align: center; }
        .logo { font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 12px; }
        .logo span { color: #3b82f6; }
        h1 { font-size: 20px; font-weight: 600; margin-bottom: 8px; color: #334155; }
        p { color: #64748b; font-size: 14px; line-height: 1.5; margin-bottom: 32px; }
        .btn-group { display: flex; flex-direction: column; gap: 12px; }
        .btn { display: block; width: 100%; padding: 14px; text-decoration: none; font-size: 14px; font-weight: 600; border-radius: 6px; transition: all 0.2s; }
        .btn-primary { background: #1e293b; color: white; border: 1px solid #1e293b; }
        .btn-primary:hover { background: #0f172a; }
        .btn-secondary { background: white; color: #475569; border: 1px solid #cbd5e1; }
        .btn-secondary:hover { background: #f8fafc; color: #1e293b; }
        .footer-text { margin-top: 32px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="gateway-card">
    <div class="logo">TLF<span>Host</span></div>
    <h1>Secure Client Portal</h1>
    <p>Deploy specialized pre-configured environments, manage high-privacy server nodes, and access semi-managed support ticketing system.</p>

    <div class="btn-group">
        <a href="login.php" class="btn btn-primary">Sign In to Account</a>
        <a href="register.php" class="btn btn-secondary">Create Client Account</a>
    </div>

    <div class="footer-text">
        &copy; <?php echo date('Y'); ?> TLFHost Infrastructure Services. All rights reserved.
    </div>
</div>

</body>
</html>