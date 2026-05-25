<!-- templates/client/register.tpl.php -->
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
        body { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; color: #0f172a; }
        .wrapper { width: 100%; max-width: 460px; padding: 20px; }
        .card { background: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 10px 15px -3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { text-align: center; margin-bottom: 28px; }
        .header h1 { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        .header p { font-size: 14px; color: #64748b; }
        .form-group { margin-bottom: 18px; }
        .label { display: block; font-size: 14px; font-weight: 500; color: #334155; margin-bottom: 6px; }
        .control { width: 100%; padding: 11px 14px; font-size: 15px; border: 1px solid #cbd5e1; border-radius: 8px; background-color: #f8fafc; }
        .control:focus { outline: none; border-color: #3b82f6; background-color: #ffffff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .btn { width: 100%; padding: 12px; background-color: #3b82f6; border: none; border-radius: 8px; color: white; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 8px; }
        .btn:hover { background-color: #2563eb; }
        .msg-error { background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; font-weight: 500; }
        .msg-success { background-color: #f0fdf4; border: 1px solid #dcfce7; color: #166534; padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; font-weight: 500; }
        .redirect-link { text-align: center; margin-top: 20px; font-size: 14px; color: #64748b; }
        .redirect-link a { color: #3b82f6; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>Create Account</h1>
            <p>Get started with BilingTLF infrastructure</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="msg-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="msg-success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
            
            <div class="form-group">
                <label class="label" strip for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="control" placeholder="John Doe" required>
            </div>

            <div class="form-group">
                <label class="label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="control" placeholder="name@example.com" required>
            </div>

            <div class="form-group">
                <label class="label" for="password">Password</label>
                <input type="password" id="password" name="password" class="control" placeholder="At least 8 characters" required>
            </div>

            <button type="submit" class="btn">Sign Up</button>
        </form>

        <div class="redirect-link">
            Already have an account? <a href="index.php">Sign In</a>
        </div>
    </div>
</div>

</body>
</html>