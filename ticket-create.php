<?php
// ticket-create.php

session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = "Open New Ticket - TLFHost";
$csrf_token = $_SESSION['csrf_token'];
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $subject = filter_input(INPUT_POST, 'subject', FILTER_UNSAFE_RAW);
    $department = filter_input(INPUT_POST, 'department', FILTER_UNSAFE_RAW);
    $priority = filter_input(INPUT_POST, 'priority', FILTER_UNSAFE_RAW);
    $message = filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW);

    $allowed_departments = ['Technical Support', 'Billing & Sales', 'General Inquiry'];
    $allowed_priorities = ['low', 'medium', 'high'];

    if ($subject && $message && in_array($department, $allowed_departments) && in_array($priority, $allowed_priorities)) {
        try {
            $pdo->beginTransaction();

            $stmtTicket = $pdo->prepare("INSERT INTO tlf_tickets (user_id, subject, department, priority, status) VALUES (:user_id, :subject, :department, :priority, 'open')");
            $stmtTicket->execute([
                'user_id' => $_SESSION['user_id'],
                'subject' => trim($subject),
                'department' => $department,
                'priority' => $priority
            ]);

            $ticket_id = $pdo->lastInsertId();

            $stmtReply = $pdo->prepare("INSERT INTO tlf_ticket_replies (ticket_id, user_id, message) VALUES (:ticket_id, :user_id, :message)");
            $stmtReply->execute([
                'ticket_id' => $ticket_id,
                'user_id' => $_SESSION['user_id'],
                'message' => trim($message)
            ]);

            $pdo->commit();
            
            header("Location: tickets.php");
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "System error occurred. Please try again.";
        }
    } else {
        $error_message = "Please fill in all fields with valid information.";
    }
}

require_once __DIR__ . '/templates/client/ticket-create.tpl.php';