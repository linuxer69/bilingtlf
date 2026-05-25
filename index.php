<?php
// index.php

session_start();
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = "Sign In - BilingTLF";
$csrf_token = $_SESSION['csrf_token'];
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if ($email && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, full_name, password_hash, status FROM tlf_users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                $error_message = "Your account is " . $user['status'] . ". Contact support.";
            } else {
                session_regenerate_id(true);

                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];

                header("Location: dashboard.php");
                exit;
            }
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Please fill in all fields correctly.";
    }
}

require_once __DIR__ . '/templates/client/login.tpl.php';