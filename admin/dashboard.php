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

// Helper function to calculate relative time (e.g., 2 minutes ago)
function timeAgo($timestamp) {
    if (!$timestamp) return 'Never';
    
    $time = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time;
    
    if ($time_difference < 1) { return 'Just now'; }
    
    $condition = array(
        12 * 30 * 24 * 60 * 60 => 'year',
        30 * 24 * 60 * 60      => 'month',
        24 * 60 * 60           => 'day',
        60 * 60                => 'hour',
        60                     => 'minute',
        1                      => 'second'
    );
    
    foreach ($condition as $secs => $str) {
        $d = $time_difference / $secs;
        if ($d >= 1) {
            $t = round($d);
            return $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ago';
        }
    }
}

try {
    // 1. Count total registered users
    $stmtUsers = $pdo->query("SELECT COUNT(*) FROM tlf_users");
    $total_users = $stmtUsers->fetchColumn();

    // 2. Count active/pending tickets awaiting staff attention
    $stmtTickets = $pdo->query("SELECT COUNT(*) FROM tlf_tickets WHERE status IN ('open', 'customer-reply')");
    $pending_tickets = $stmtTickets->fetchColumn();

    // 3. Count total active services
    $total_services = 0;
    $stmtCheckServices = $pdo->query("SHOW TABLES LIKE 'tlf_services'");
    if ($stmtCheckServices->rowCount() > 0) {
        $total_services = $pdo->query("SELECT COUNT(*) FROM tlf_services WHERE status = 'active'")->fetchColumn();
    }

    // 4. Fetch ONLY active/pending tickets for the overview table
    $queryRecent = "SELECT t.id, t.subject, t.status, t.updated_at, u.username 
                    FROM tlf_tickets t 
                    LEFT JOIN tlf_users u ON t.user_id = u.id 
                    WHERE t.status IN ('open', 'customer-reply')
                    ORDER BY t.updated_at DESC LIMIT 5";
    $stmtRecent = $pdo->query($queryRecent);
    $recent_tickets = $stmtRecent->fetchAll();

    // 5. NEW: Fetch top 10 users with the most recent login activity
    $queryLogins = "SELECT id, username, last_login FROM tlf_users WHERE last_login IS NOT NULL ORDER BY last_login DESC LIMIT 10";
    $stmtLogins = $pdo->query($queryLogins);
    $recent_logins = $stmtLogins->fetchAll();

} catch (PDOException $e) {
    die("Database error on admin dashboard: " . $e->getMessage());
}

// Load admin template from the correct folder
require_once __DIR__ . '/../templates/admin/dashboard.tpl.php';