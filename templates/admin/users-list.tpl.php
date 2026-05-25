<!-- templates/admin/users-list.tpl.php -->
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
        .sidebar a { display: block; padding: 12px 16px; color: #475569; text-decoration: none; border-radius: 8px; font-weight: 500; margin-bottom: 8px; transition: all 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #f1f5f9; color: #0f172a; }
        .sidebar .logout { margin-top: auto; color: #ef4444; }
        .main { flex: 1; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .header h1 { font-size: 24px; font-weight: 700; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden; }
        .msg-success { background-color: #f0fdf4; border-bottom: 1px solid #dcfce7; color: #166534; padding: 16px; font-size: 14px; font-weight: 500; }
        .msg-error { background-color: #fef2f2; border-bottom: 1px solid #fee2e2; color: #991b1b; padding: 16px; font-size: 14px; font-weight: 500; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f8fafc; padding: 16px; font-size: 13px; font-weight: 600; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        td { padding: 16px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; }
        tr:hover td { background-color: #f8fafc; }
        .badge { display: inline-block; padding: 4px 8px; font-size: 12px; font-weight: 500; border-radius: 9999px; text-transform: capitalize; }
        .badge-active { background-color: #dcfce7; color: #15803d; }
        .badge-suspended { background-color: #fee2e2; color: #b91c1c; }
        .badge-pending { background-color: #fef9c3; color: #a16207; }
        .btn-group { display: flex; gap: 8px; }
        .btn-edit { padding: 6px 12px; background: #e0f2fe; color: #0369a1; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 500; transition: background 0.2s; }
        .btn-edit:hover { background: #bae6fd; }
        .btn-delete { padding: 6px 12px; background: #fee2e2; color: #b91c1c; border: none; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: background 0.2s; }
        .btn-delete:hover { background: #fecaca; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">Biling<span>TLF</span></div>
    <a href="dashboard.php">Dashboard</a>
    <a href="users.php" class="active">Manage Users</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Registered Users</h1>
    </div>

    <div class="card">
        <?php if (!empty($success_message)): ?>
            <div class="msg-success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="msg-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 32px;">No users found in the database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td style="font-weight: 500; color: #0f172a;"><?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $user['status']; ?>">
                                    <?php echo htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="user-edit.php?id=<?php echo $user['id']; ?>" class="btn-edit">Edit</a>
                                    
                                    <form action="users.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn-delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>