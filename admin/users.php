<?php
// admin/users.php

session_start();
require_once __DIR__ . '/../config/db.php';

// Check admin authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Generate CSRF Token for delete actions
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = "Manage Users - BilingTLF Admin";
$csrf_token = $_SESSION['csrf_token'];
$success_message = '';
$error_message = '';

// Handle Delete Request (POST to prevent accidental link clicks)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    if ($user_id) {
        $stmt = $pdo->prepare("DELETE FROM tlf_users WHERE id = :id");
        if ($stmt->execute(['id' => $user_id])) {
            $success_message = "User deleted successfully.";
        } else {
            $error_message = "Failed to delete the user.";
        }
    } else {
        $error_message = "Invalid user ID.";
    }
}

// Fetch all users
$stmt = $pdo->query("SELECT id, full_name, email, status, created_at FROM tlf_users ORDER BY id DESC");
$users = $stmt->fetchAll();

// Load the layout template
require_once __DIR__ . '/../templates/admin/users-list.tpl.php';