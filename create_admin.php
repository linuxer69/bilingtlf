<?php
// create_admin.php
require_once __DIR__ . '/config/db.php';

$username = 'admin';
$password = 'Admin@TLF2026';
$email = 'admin@tlfhost.local';

// Securely hash the password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Clear old sample admin if exists
    $pdo->exec("DELETE FROM tlf_admins WHERE username = 'admin'");
    
    $stmt = $pdo->prepare("INSERT INTO tlf_admins (username, password_hash, email) VALUES (:username, :password_hash, :email)");
    $stmt->execute([
        'username' => $username,
        'password_hash' => $passwordHash,
        'email' => $email
    ]);
    echo "Admin user created successfully! Username: admin | Password: Admin@TLF2026";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}