<?php
// admin/tickets.php

session_start();
require_once __DIR__ . '/../config/db.php';

// Check admin authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = "Manage Support Tickets - Admin Panel";

try {
    // Fetch all tickets with user names joined for clear display
    // Assuming you have a username or email field in your users table (adjust if needed)
    $query = "SELECT t.*, u.username FROM tlf_tickets t 
              LEFT JOIN tlf_users u ON t.user_id = u.id 
              ORDER BY t.updated_at DESC";
              
    $stmt = $pdo->query($query);
    $tickets = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Load admin template from the correct folder
require_once __DIR__ . '/../templates/admin/tickets-list.tpl.php';