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
        body { background-color: #f8fafc; color: #0f172a; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        
        .ticket-header { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); position: relative; }
        .ticket-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .ticket-id { font-size: 14px; font-weight: 600; color: #64748b; }
        
        .status-badge { display: inline-block; padding: 4px 12px; font-size: 12px; font-weight: 600; border-radius: 9999px; text-transform: uppercase; }
        .status-open { background: #fef2f2; color: #991b1b; }
        .status-answered { background: #e0f2fe; color: #0369a1; }
        .status-customer-reply { background: #fef9c3; color: #a16207; }
        .status-investigating { background: #fae8ff; color: #86198f; }
        .status-on-hold { background: #ffedd5; color: #9a3412; }
        .status-closed { background: #f1f5f9; color: #475569; }
        
        h1 { font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 8px; }
        .ticket-date { font-size: 13px; color: #64748b; margin-top: 6px; }
        
        .thread { display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px; }
        .message-box { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.01); }
        .message-box.admin-reply { border-left: 4px solid #3b82f6; background: #f8fafc; }
        .message-box.user-reply { border-left: 4px solid #10b981; }
        .msg-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; font-weight: 600; color: #64748b; }
        
        .reply-box { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        textarea { width: 100%; height: 120px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; resize: vertical; margin-bottom: 16px; }
        .btn-submit { background: #1e293b; color: white; border: none; padding: 10px 20px; font-size: 14px; font-weight: 500; border-radius: 6px; cursor: pointer; }
        .btn-close-ticket { background: #ef4444; color: white; border: none; padding: 8px 14px; font-size: 12px; font-weight: 600; border-radius: 6px; cursor: pointer; }
        .btn-close-ticket:hover { background: #dc2626; }
        .back-link { display: inline-block; margin-bottom: 16px; font-size: 14px; color: #3b82f6; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>

<div class="container">
    <a href="tickets.php" class="back-link">&larr; Back to Support Tickets</a>

    <div class="ticket-header">
        <div class="ticket-meta">
            <span class="ticket-id">Ticket #<?php echo $ticket['id']; ?></span>
            <div>
                <span class="status-badge status-<?php echo $ticket['status']; ?>" style="margin-right: 8px;">
                    <?php echo str_replace('-', ' ', $ticket['status']); ?>
                </span>
                <?php if ($ticket['status'] !== 'closed'): ?>
                    <form action="" method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="close_ticket">
                        <button type="submit" class="btn-close-ticket">Close Ticket</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <h1><?php echo htmlspecialchars($ticket['subject'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="ticket-date">Last Update: <?php echo $ticket['updated_at']; ?></div>
    </div>

    <div class="thread">
        <?php foreach ($replies as $reply): ?>
            <?php $isAdmin = !empty($reply['admin_id']); ?>
            <div class="message-box <?php echo $isAdmin ? 'admin-reply' : 'user-reply'; ?>">
                <div class="msg-header">
                    <span style="color: <?php echo $isAdmin ? '#3b82f6' : '#10b981'; ?>;">
                        <?php echo $isAdmin ? 'Support Agent' : 'You (Client)'; ?>
                    </span>
                    <span><?php echo $reply['created_at']; ?></span>
                </div>
                <div class="msg-body"><?php echo htmlspecialchars($reply['message'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($ticket['status'] !== 'closed'): ?>
        <div class="reply-box">
            <form action="" method="POST">
                <textarea name="reply_message" placeholder="Type your response here..." required></textarea>
                <button type="submit" class="btn-submit">Submit Response</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>