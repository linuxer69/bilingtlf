<?php
// admin/user-edit.php

session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = "Edit User - BilingTLF Admin";
$csrf_token = $_SESSION['csrf_token'];
$success_message = '';
$error_message = '';

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id) {
    header("Location: users.php");
    exit;
}

// Fetch current user data
$stmt = $pdo->prepare("SELECT id, full_name, email, status FROM tlf_users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_UNSAFE_RAW);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $status = filter_input(INPUT_POST, 'status', FILTER_UNSAFE_RAW);
    $new_password = $_POST['password'] ?? '';

    // Allowed status values validation
    $allowed_statuses = ['active', 'suspended', 'pending'];

    if ($full_name && $email && in_array($status, $allowed_statuses)) {
        try {
            // Check if email is taken by another user
            $check_email = $pdo->prepare("SELECT id FROM tlf_users WHERE email = :email AND id != :id LIMIT 1");
            $check_email->execute(['email' => $email, 'id' => $user_id]);
            
            if ($check_email->fetch()) {
                $error_message = "This email is already in use by another user.";
            } else {
                // Update basic information
                $update_query = "UPDATE tlf_users SET full_name = :full_name, email = :email, status = :status WHERE id = :id";
                $update_params = [
                    'full_name' => $full_name,
                    'email' => $email,
                    'status' => $status,
                    'id' => $user_id
                ];
                
                $pdo->prepare($update_query)->execute($update_params);

                // Update password if a new one was provided
                if (!empty($new_password)) {
                    if (strlen($new_password) < 8) {
                        $error_message = "Basic data saved, but password must be at least 8 characters long.";
                    } else {
                        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                        $pwd_stmt = $pdo->prepare("UPDATE tlf_users SET password_hash = :hash WHERE id = :id");
                        $pwd_stmt->execute(['hash' => $password_hash, 'id' => $user_id]);
                    }
                }

                if (empty($error_message)) {
                    $success_message = "User updated successfully.";
                    // Refresh local user variables to show updated values in form
                    $user['full_name'] = $full_name;
                    $user['email'] = $email;
                    $user['status'] = $status;
                }
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in all fields with valid data.";
    }
}

require_once __DIR__ . '/../templates/admin/user-edit.tpl.php';