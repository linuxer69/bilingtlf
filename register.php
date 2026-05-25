<?php
// register.php

session_start();
require_once __DIR__ . '/config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = "Create Your Account - BilingTLF";
$csrf_token = $_SESSION['csrf_token'];
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_UNSAFE_RAW);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if ($full_name && $email && !empty($password)) {
        if (strlen($password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM tlf_users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            
            if ($stmt->fetch()) {
                $error_message = "This email address is already registered.";
            } else {
                // Securely hash password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $insert = $pdo->prepare("INSERT INTO tlf_users (full_name, email, password_hash) VALUES (:full_name, :email, :password_hash)");
                if ($insert->execute(['full_name' => $full_name, 'email' => $email, 'password_hash' => $password_hash])) {
                    $success_message = "Account created successfully! You can now sign in.";
                } else {
                    $error_message = "Something went wrong. Please try again.";
                }
            }
        }
    } else {
        $error_message = "Please provide a valid name, email, and password.";
    }
}

require_once __DIR__ . '/templates/client/register.tpl.php';