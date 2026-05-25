<!-- templates/client/ticket-create.tpl.php -->
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
        
        .btn-back { padding: 8px 16px; background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500; }
        .btn-back:hover { background-color: #f8fafc; color: #0f172a; }
        
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); max-width: 700px; padding: 32px; }
        .form-group { margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        
        .label { display: block; font-size: 14px; font-weight: 500; color: #334155; margin-bottom: 8px; }
        .control { width: 100%; padding: 11px 14px; font-size: 15px; border: 1px solid #cbd5e1; border-radius: 8px; background-color: #f8fafc; }
        .control:focus { outline: none; border-color: #3b82f6; background-color: #ffffff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        
        textarea.control { min-height: 160px; resize: vertical; }
        
        .btn-submit { padding: 12px 24px; background-color: #3b82f6; border: none; border-radius: 8px; color: white; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-submit:hover { background-color: #2563eb; }
        
        .msg-error { background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; font-weight: 500; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">TLF<span>Host</span></div>
    <a href="dashboard.php">Dashboard</a>
    <a href="tickets.php" class="active">Support Tickets</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Open New Support Ticket</h1>
        <a href="tickets.php" class="btn-back">Back to Tickets</a>
    </div>

    <div class="card">
        <?php if (!empty($error_message)): ?>
            <div class="msg-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form action="ticket-create.php" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label class="label" for="subject">Subject</label>
                <input type="text" id="subject" name="subject" class="control" placeholder="e.g., Server connection issue" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="label" for="department">Department</label>
                    <select id="department" name="department" class="control">
                        <option value="Technical Support">Technical Support</option>
                        <option value="Billing & Sales">Billing & Sales</option>
                        <option value="General Inquiry">General Inquiry</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="label" for="priority">Priority</label>
                    <select id="priority" name="priority" class="control">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="label" for="message">Message</label>
                <textarea id="message" name="message" class="control" placeholder="Describe your issue in detail..." required></textarea>
            </div>

            <button type="submit" class="btn-submit">Submit Ticket</button>
        </form>
    </div>
</div>

</body>
</html>