<!-- templates/admin/user-edit.tpl.php -->
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
        body { background-color: #f8fafc; color: #0f172a; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #ffffff; border-right: 1px solid #e2e8f0; padding: 24px; display: flex; flex-direction: column; }
        .sidebar .logo { font-size: 20px; font-weight: 700; margin-bottom: 32px; color: #0f172a; }
        .sidebar .logo span { color: #3b82f6; }
        .sidebar a { display: block; padding: 12px 16px; color: #475569; text-decoration: none; border-radius: 8px; font-weight: 500; margin-bottom: 8px; }
        .sidebar a.active { background: #f1f5f9; color: #0f172a; }
        .main { flex: 1; padding: 40px; }
        .header { margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between; }
        .header h1 { font-size: 24px; font-weight: 700; }
        .btn-back { padding: 8px 16px; background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500; transition: all 0.2s; }
        .btn-back:hover { background-color: #f8fafc; color: #0f172a; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); max-width: 600px; padding: 32px; }
        .form-group { margin-bottom: 20px; }
        .label { display: block; font-size: 14px; font-weight: 500; color: #334155; margin-bottom: 8px; }
        .control { width: 100%; padding: 11px 14px; font-size: 15px; border: 1px solid #cbd5e1; border-radius: 8px; background-color: #f8fafc; }
        .control:focus { outline: none; border-color: #3b82f6; background-color: #ffffff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .btn-save { padding: 12px 24px; background-color: #3b82f6; border: none; border-radius: 8px; color: white; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-save:hover { background-color: #2563eb; }
        .msg-success { background-color: #f0fdf4; border: 1px solid #dcfce7; color: #166534; padding: 16px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; font-weight: 500; }
        .msg-error { background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; font-weight: 500; }
        .help-text { font-size: 13px; color: #64748b; margin-top: 4px; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">Biling<span>TLF</span></div>
    <a href="dashboard.php">Dashboard</a>
    <a href="users.php" class="active">Manage Users</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Edit User Profile</h1>
        <a href="users.php" class="btn-back">Back to List</a>
    </div>

    <div class="card">
        <?php if (!empty($success_message)): ?>
            <div class="msg-success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="msg-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form action="user-edit.php?id=<?php echo $user['id']; ?>" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label class="label" for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="control" value="<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="form-group">
                <label class="label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="control" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="form-group">
                <label class="label" for="status">Account Status</label>
                <select id="status" name="status" class="control">
                    <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="suspended" <?php echo $user['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                    <option value="pending" <?php echo $user['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                </select>
            </div>

            <div class="form-group">
                <label class="label" for="password">New Password</label>
                <input type="password" id="password" name="password" class="control" placeholder="Leave blank to keep current password">
                <p class="help-text">Only fill this if you want to force change the user's password.</p>
            </div>

            <button type="submit" class="btn-save">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>