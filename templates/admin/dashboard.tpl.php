<!-- templates/admin/dashboard.tpl.php -->
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
        
        /* Admin Sidebar */
        .sidebar { width: 260px; background: #1e293b; padding: 24px; display: flex; flex-direction: column; color: white; }
        .sidebar .logo { font-size: 20px; font-weight: 700; margin-bottom: 32px; color: #ffffff; }
        .sidebar .logo span { color: #3b82f6; }
        .sidebar a { display: block; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; font-weight: 500; margin-bottom: 8px; transition: all 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #334155; color: #ffffff; }
        
        /* Main Panel Content */
        .main { flex: 1; padding: 40px; }
        .header { margin-bottom: 32px; }
        .header h1 { font-size: 24px; font-weight: 700; color: #0f172a; }
        .header p { color: #64748b; font-size: 14px; margin-top: 4px; }
        
        /* Admin Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; display: flex; flex-direction: column; box-shadow: 0 1px 3px rgba(0,0,0,0.01); }
        .stat-card .stat-label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card .stat-value { font-size: 28px; font-weight: 700; color: #0f172a; margin-top: 8px; }
        .stat-card.alert-card { border-top: 4px solid #ef4444; }
        
        /* Content Section */
        .dashboard-section { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden; }
        .section-header { padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .section-header h2 { font-size: 16px; font-weight: 600; color: #0f172a; }
        .btn-text { font-size: 13px; font-weight: 600; color: #3b82f6; text-decoration: none; }
        .btn-text:hover { text-decoration: underline; }
        
        /* Data Table */
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f8fafc; padding: 14px 24px; font-size: 12px; font-weight: 600; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        td { padding: 16px 24px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; }
        tr:last-child td { border-bottom: none; }
        
        /* Badges for Admin View */
        .badge { display: inline-block; padding: 4px 8px; font-size: 12px; font-weight: 500; border-radius: 9999px; text-transform: capitalize; }
        .status-open { background-color: #fef2f2; color: #991b1b; }
        .status-answered { background-color: #e0f2fe; color: #0369a1; }
        .status-customer-reply { background-color: #fef9c3; color: #a16207; }
        .status-closed { background-color: #f1f5f9; color: #475569; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">TLF<span>Admin</span></div>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="users.php">Manage Users</a>
    <a href="tickets.php">Support Tickets</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($admin_username, ENT_QUOTES, 'UTF-8'); ?></h1>
        <p>System infrastructure overview and business metrics monitoring center.</p>
    </div>

    <!-- Metrics Cards Container -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Clients</span>
            <span class="stat-value"><?php echo $total_users; ?></span>
        </div>
        <div class="stat-card <?php echo $pending_tickets > 0 ? 'alert-card' : ''; ?>">
            <span class="stat-label">Tickets Awaiting Action</span>
            <span class="stat-value"><?php echo $pending_tickets; ?></span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Active Deployments</span>
            <span class="stat-value"><?php echo $total_services; ?></span>
        </div>
    </div>

    <!-- Urgent Tickets Overview Box -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Recent Support Activity</h2>
            <a href="tickets.php" class="btn-text">Manage All Tickets</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Client</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Last Update</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_tickets)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 24px;">No recent support tickets in queue.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent_tickets as $ticket): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($ticket['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td style="font-weight: 500; color: #0f172a;"><?php echo htmlspecialchars($ticket['username'] ?? 'System Account', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><a href="ticket-view.php?id=<?php echo $ticket['id']; ?>" style="color: #3b82f6; text-decoration: none; font-weight: 500;"><?php echo htmlspecialchars($ticket['subject'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                            <td>
                                <span class="badge status-<?php echo $ticket['status']; ?>">
                                    <?php echo htmlspecialchars($ticket['status'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['updated_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>