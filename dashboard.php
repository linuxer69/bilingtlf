<?php
// dashboard.php

session_start();
require_once __DIR__ . '/config/db.php';

// Check user authentication
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = "Client Dashboard - TLFHost";
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Client';

try {
    // 1. Fetch count of active tickets for the user
    $stmtOpenTickets = $pdo->prepare("SELECT COUNT(*) FROM tlf_tickets WHERE user_id = :user_id AND status IN ('open', 'answered', 'customer-reply')");
    $stmtOpenTickets->execute(['user_id' => $user_id]);
    $open_tickets_count = $stmtOpenTickets->fetchColumn();

    // 2. Fetch count of unpaid invoices safely
    $unpaid_invoices_count = 0;
    $stmtCheckTable = $pdo->query("SHOW TABLES LIKE 'tlf_invoices'");
    if ($stmtCheckTable->rowCount() > 0) {
        $stmtInvoices = $pdo->prepare("SELECT COUNT(*) FROM tlf_invoices WHERE user_id = :user_id AND status = 'unpaid'");
        $stmtInvoices->execute(['user_id' => $user_id]);
        $unpaid_invoices_count = $stmtInvoices->fetchColumn();
    }

    // 3. Get recent support tickets for dashboard overview
    $stmtRecentTickets = $pdo->prepare("SELECT id, subject, status, updated_at FROM tlf_tickets WHERE user_id = :user_id ORDER BY updated_at DESC LIMIT 5");
    $stmtRecentTickets->execute(['user_id' => $user_id]);
    $recent_tickets = $stmtRecentTickets->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Load the updated client template
require_once __DIR__ . '/templates/client/dashboard.tpl.php';