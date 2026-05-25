<?php
// logout.php

session_start();

// Unset all session variables related to the client
$_SESSION = array();

// Destroy the session cookie securely if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session completely
session_destroy();

// Redirect back to the client login page
header("Location: index.php");
exit;