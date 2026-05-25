<?php
// tickets.php

session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page_title = "My Support Tickets - TLFHost";
$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT id, subject, department, priority, status, updated_at FROM tlf_tickets WHERE user_id = :user_id ORDER BY updated_at DESC");
    $stmt->execute(['user_id' => $user_id]);
    $tickets = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

require_once __DIR__ . '/templates/client/tickets-list.tpl.php';