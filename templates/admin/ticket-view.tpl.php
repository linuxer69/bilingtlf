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
        body { background-color: #f1f5f9; color: #0f172a; display: flex; min-height: 100vh; }
        
        .sidebar { width: 260px; background: #1e293b; padding: 24px; display: flex; flex-direction: column; color: white; }
        .sidebar .logo { font-size: 20px; font-weight: 700; margin-bottom: 32px; }
        .sidebar a { display: block; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 8px; font-weight: 500; }
        .sidebar a.active { background: #334155; color: white; }
        
        .main { flex: 1; padding: 40px; max-width: 1000px; }
        .header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 22px; font-weight: 700; }
        .btn-back { padding: 8px 16px; background: #ffffff; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500; }
        
        .meta-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .meta-item span { display: block; font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .meta-item p { font-size: 15px; font-weight: 500; color: #1e293b; margin-top: 4px; }
        
        .chat-container { margin-bottom: 32px; }
        .reply-box { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .reply-box.admin-reply { border-left: 4px solid #3b82f6; background-color: #f8fafc; }
        .reply-box.user-reply { border-left: 4px solid #10b981; }
        .reply-header { display: flex; justify-content: space-between; font-size: 13px; color: #64748b; margin-bottom: 10px; }
        .reply-body { font-size: 14px; line-height: 1.6; color: #334155; white-space: pre-wrap; }
        
        .action-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
        textarea { width: 100%; min-height: 120px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: #f8fafc; margin-bottom: 16px; resize: vertical; }
        
        .flex-actions { display: flex; justify-content: space-between; align-items: center; }
        select { padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: white; }
        .btn-submit { padding: 10px 20px; background: #3b82f6; border: none; color: white; font-weight: 600; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">TLF<span>Admin</span></div>
    <a href="dashboard.php">Dashboard</a>
    <a href="users.php">Manage Users</a>
    <a href="tickets.php" class="active">Support Tickets</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Ticket ID: #<?php echo $ticket['id']; ?></h1>
        <a href="tickets.php" class="btn-back">Back to List</a>
    </div>

    <div class="meta-card">
        <div class="meta-item"><span>Client</span><p><?php echo htmlspecialchars($ticket['username'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></p></div>
        <div class="meta-item"><span>Department</span><p><?php echo htmlspecialchars($ticket['department'], ENT_QUOTES, 'UTF-8'); ?></p></div>
        <div class="meta-item"><span>Priority</span><p style="text-transform: uppercase;"><?php echo htmlspecialchars($ticket['priority'], ENT_QUOTES, 'UTF-8'); ?></p></div>
        <div class="meta-item"><span>Status</span><p style="text-transform: uppercase; font-weight:600;"><?php echo str_replace('-', ' ', $ticket['status']); ?></p></div>
    </div>

    <div class="chat-container">
        <?php foreach ($replies as $reply): ?>
            <?php $isAdmin = !empty($reply['admin_id']); ?>
            <div class="reply-box <?php echo $isAdmin ? 'admin-reply' : 'user-reply'; ?>">
                <div class="reply-header">
                    <span style="font-weight:600; color:#0f172a;">
                        <?php echo $isAdmin ? 'Staff (' . htmlspecialchars($reply['admin_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') . ')' : htmlspecialchars($reply['username'] ?? 'Client', ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <span><?php echo $reply['created_at']; ?></span>
                </div>
                <div class="reply-body"><?php echo htmlspecialchars($reply['message'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="action-card">
        <h3>Update Ticket Status / Reply</h3>
        <form action="ticket-view.php?id=<?php echo $ticket['id']; ?>" method="POST" style="margin-top: 16px;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <textarea name="message" placeholder="Type an optional response message here..."></textarea>
            
            <div class="flex-actions">
                <div>
                    <label style="font-size: 13px; color: #475569; font-weight: 500; margin-right: 8px;">Action / Status:</label>
                    <select name="status">
                        <option value="answered" <?php echo $ticket['status'] === 'answered' ? 'selected' : ''; ?>>Answered (Send Response)</option>
                        <option value="investigating" <?php echo $ticket['status'] === 'investigating' ? 'selected' : ''; ?>>Investigating (Under Review)</option>
                        <option value="on-hold" <?php echo $ticket['status'] === 'on-hold' ? 'selected' : ''; ?>>On Hold (Paused)</option>
                        <option value="closed" <?php echo $ticket['status'] === 'closed' ? 'selected' : ''; ?>>Close & Archive Ticket</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Submit Update</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>