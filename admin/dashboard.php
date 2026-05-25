<?php
// admin/dashboard.php

session_start();
require_once __DIR__ . '/../config/db.php';

// Check admin authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = "Admin Dashboard - TLFHost Panel";
$admin_username = $_SESSION['admin_username'] ?? 'Administrator';

try {
    // 1. Count total registered users
    $stmtUsers = $pdo->query("SELECT COUNT(*) FROM tlf_users");
    $total_users = $stmtUsers->fetchColumn();

    // 2. Count pending or open tickets awaiting staff attention
    $stmtTickets = $pdo->query("SELECT COUNT(*) FROM tlf_tickets WHERE status IN ('open', 'customer-reply')");
    $pending_tickets = $stmtTickets->fetchColumn();

    // 3. Count total active services (placeholder or count if table exists)
    $total_services = 0;
    $stmtCheckServices = $pdo->query("SHOW TABLES LIKE 'tlf_services'");
    if ($stmtCheckServices->rowCount() > 0) {
        $total_services = $pdo->query("SELECT COUNT(*) FROM tlf_services WHERE status = 'active'")->fetchColumn();
    }

    // 4. Fetch 5 most recent pending or updated tickets for the overview table
    $queryRecent = "SELECT t.id, t.subject, t.status, t.updated_at, u.username 
                    FROM tlf_tickets t 
                    LEFT JOIN tlf_users u ON t.user_id = u.id 
                    ORDER BY t.updated_at DESC LIMIT 5";
    $stmtRecent = $pdo->query($queryRecent);
    $recent_tickets = $stmtRecent->fetchAll();

} catch (PDOException $e) {
    die("Database error on admin dashboard: " . $e->getMessage());
}

// Load admin template from the correct folder
require_once __DIR__ . '/../templates/admin/dashboard.tpl.php';