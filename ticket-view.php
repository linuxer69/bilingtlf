<?php
// ticket-view.php

session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    header("Location: tickets.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM tlf_tickets WHERE id = ? AND user_id = ?");
    $stmt->execute([$ticket_id, $user_id]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        die("Ticket not found or access denied.");
    }

    // Handle user requesting to close their own ticket
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'close_ticket') {
        $updateStatus = $pdo->prepare("UPDATE tlf_tickets SET status = 'closed', updated_at = NOW() WHERE id = ?");
        $updateStatus->execute([$ticket_id]);
        
        header("Location: ticket-view.php?id=" . $ticket_id);
        exit;
    }

    // Handle new reply submission from user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message']) && $ticket['status'] !== 'closed') {
        $message = trim($_POST['reply_message']);

        if (!empty($message)) {
            $pdo->beginTransaction();

            $insertStmt = $pdo->prepare("INSERT INTO tlf_ticket_replies (ticket_id, user_id, admin_id, message, created_at) VALUES (?, ?, NULL, ?, NOW())");
            $insertStmt->execute([$ticket_id, $user_id, $message]);

            $updateStmt = $pdo->prepare("UPDATE tlf_tickets SET status = 'customer-reply', updated_at = NOW() WHERE id = ?");
            $updateStmt->execute([$ticket_id]);

            $pdo->commit();

            header("Location: ticket-view.php?id=" . $ticket_id);
            exit;
        }
    }

    $repliesStmt = $pdo->prepare("SELECT * FROM tlf_ticket_replies WHERE ticket_id = ? ORDER BY created_at ASC");
    $repliesStmt->execute([$ticket_id]);
    $replies = $repliesStmt->fetchAll();

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Database error: " . $e->getMessage());
}

$page_title = "Ticket #" . htmlspecialchars($ticket['id']) . " - TLFHost";
require_once __DIR__ . '/templates/client/ticket-view.tpl.php';