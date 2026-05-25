<!-- templates/admin/login.tpl.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; color: #1e293b; }
        .login-wrapper { width: 100%; max-width: 440px; padding: 20px; }
        .brand-logo { text-align: center; margin-bottom: 24px; }
        .brand-logo h1 { font-size: 28px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px; }
        .brand-logo span { color: #3b82f6; }
        .login-card { background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; }
        .card-header { margin-bottom: 32px; text-align: center; }
        .card-header h2 { font-size: 20px; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
        .card-header p { font-size: 14px; color: #64748b; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 14px; font-weight: 500; color: #334155; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; font-size: 15px; border: 1px solid #cbd5e1; border-radius: 8px; background-color: #f8fafc; color: #0f172a; transition: all 0.2s ease; }
        .form-control:focus { outline: none; border-color: #3b82f6; background-color: #ffffff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .btn-submit { width: 100%; padding: 12px; background-color: #3b82f6; border: none; border-radius: 8px; color: #ffffff; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; margin-top: 10px; }
        .btn-submit:hover { background-color: #2563eb; }
        .alert-danger { background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; font-weight: 500; }
        .footer-text { text-align: center; margin-top: 24px; font-size: 13px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="brand-logo">
        <h1>Biling<span>TLF</span></h1>
    </div>

    <div class="login-card">
        <div class="card-header">
            <h2>Welcome Back</h2>
            <p>Sign in to manage your infrastructure</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert-danger">
                <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
            
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required autofocus>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            
            <button type="submit" class="btn-submit">Sign In</button>
        </form>
    </div>

    <div class="footer-text">
        &copy; 2026 BilingTLF Core System. All rights reserved.
    </div>
</div>

</body>
</html>