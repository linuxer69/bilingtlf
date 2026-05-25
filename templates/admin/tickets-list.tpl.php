<!-- templates/admin/tickets-list.tpl.php -->
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
        .sidebar .logo { font-size: 20px; font-weight: 700; margin-bottom: 32px; color: #ffffff; }
        .sidebar .logo span { color: #3b82f6; }
        .sidebar a { display: block; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; font-weight: 500; margin-bottom: 8px; transition: all 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #334155; color: #ffffff; }
        
        .main { flex: 1; padding: 40px; }
        .header { margin-bottom: 32px; }
        .header h1 { font-size: 24px; font-weight: 700; color: #0f172a; }
        .header p { color: #64748b; font-size: 14px; margin-top: 4px; }
        
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f8fafc; padding: 16px 24px; font-size: 13px; font-weight: 600; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        td { padding: 16px 24px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; }
        tr:hover td { background-color: #f8fafc; }
        
        .badge { display: inline-block; padding: 4px 8px; font-size: 12px; font-weight: 500; border-radius: 9999px; text-transform: capitalize; }
        .status-open { background-color: #fef2f2; color: #991b1b; }
        .status-answered { background-color: #e0f2fe; color: #0369a1; }
        .status-customer-reply { background-color: #fef9c3; color: #a16207; }
        .status-closed { background-color: #f1f5f9; color: #475569; }
        
        .priority-high { color: #b91c1c; font-weight: 600; }
        .priority-medium { color: #d97706; font-weight: 500; }
        .priority-low { color: #475569; }
        
        .btn-action { padding: 6px 12px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 500; }
        .btn-action:hover { background: #2563eb; }
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
        <h1>Support Tickets Management</h1>
        <p>Review, filter, and respond to incoming client support inquiries.</p>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Client</th>
                    <th>Subject</th>
                    <th>Department</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Last Update</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #94a3b8; padding: 32px;">No support tickets in the system.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($ticket['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td style="font-weight: 500; color: #0f172a;"><?php echo htmlspecialchars($ticket['username'] ?? 'Unknown User', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($ticket['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($ticket['department'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <span class="priority-<?php echo $ticket['priority']; ?>">
                                    <?php echo htmlspecialchars($ticket['priority'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge status-<?php echo $ticket['status']; ?>">
                                    <?php echo htmlspecialchars($ticket['status'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['updated_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a href="ticket-view.php?id=<?php echo $ticket['id']; ?>" class="btn-action">Process</a>
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