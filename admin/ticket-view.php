<?php
// admin/ticket-view.php

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$ticket_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$ticket_id) {
    header("Location: tickets.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = "Review Ticket #" . $ticket_id . " - Admin Panel";
$csrf_token = $_SESSION['csrf_token'];
$error_message = '';

try {
    $stmtTicket = $pdo->prepare("SELECT t.*, u.username FROM tlf_tickets t LEFT JOIN tlf_users u ON t.user_id = u.id WHERE t.id = ?");
    $stmtTicket->execute([$ticket_id]);
    $ticket = $stmtTicket->fetch();

    if (!$ticket) {
        die("Ticket not found.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF validation failed.");
        }

        $message = filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW);
        $new_status = filter_input(INPUT_POST, 'status', FILTER_UNSAFE_RAW);

        $allowed_statuses = ['answered', 'investigating', 'on-hold', 'closed'];
        $status_to_update = in_array($new_status, $allowed_statuses) ? $new_status : 'answered';

        $pdo->beginTransaction();

        if ($message && trim($message) !== '') {
            $stmtReply = $pdo->prepare("INSERT INTO tlf_ticket_replies (ticket_id, admin_id, message) VALUES (?, ?, ?)");
            $stmtReply->execute([$ticket_id, $_SESSION['admin_id'] ?? 1, trim($message)]);
        }

        $stmtUpdate = $pdo->prepare("UPDATE tlf_tickets SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmtUpdate->execute([$status_to_update, $ticket_id]);

        $pdo->commit();
        header("Location: ticket-view.php?id=" . $ticket_id);
        exit;
    }

    $stmtReplies = $pdo->prepare("SELECT r.*, u.username, a.username as admin_name FROM tlf_ticket_replies r LEFT JOIN tlf_users u ON r.user_id = u.id LEFT JOIN tlf_admins a ON r.admin_id = a.id WHERE r.ticket_id = ? ORDER BY r.created_at ASC");
    $stmtReplies->execute([$ticket_id]);
    $replies = $stmtReplies->fetchAll();

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Database error: " . $e->getMessage());
}

require_once __DIR__ . '/../templates/admin/ticket-view.tpl.php';