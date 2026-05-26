<?php
// login.php (Integrated with Premium OVHcloud Style Layout)

session_start();
require_once __DIR__ . '/config/db.php';

// If user is already logged in, redirect straight to user dashboard securely
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username_or_email) && !empty($password)) {
        try {
            // 1. Fetch user by username or email directly from your database
            $stmt = $pdo->prepare("SELECT * FROM tlf_users WHERE username = ? OR email = ?");
            $stmt->execute([$username_or_email, $username_or_email]);
            $user = $stmt->fetch();

            if ($user) {
                // Using 'password_hash' matched perfectly with your database column structure
                $db_password = $user['password_hash'] ?? null;

                if ($db_password !== null) {
                    $hashed_input = hash('sha256', $password);
                    
                    // 2. Safely verify client credentials
                    if ($hashed_input === $db_password || password_verify($password, $db_password)) {
                        
                        // 3. Setup standard platform sessions
                        $_SESSION['user_logged_in'] = true;
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        
                        // 4. Update the exact timestamp for the admin dashboard monitoring widget
                        $updateStmt = $pdo->prepare("UPDATE tlf_users SET last_login = NOW() WHERE id = ?");
                        $updateStmt->execute([$user['id']]);

                        // 5. Redirect securely to user panel dashboard
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $error_message = "Invalid password. Please try again.";
                    }
                } else {
                    $error_message = "Database configuration error: Password column missing.";
                }
            } else {
                $error_message = "No account found with that username or email.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error during authentication: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in all authentication fields.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control panel - TLFHost</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0px;
            font-family: "Source Sans 3", sans-serif;
            font-size: 14px;
            color: #4d5592;
            background-color: #ffffff;
        }
        .main {
            display: flex;
            flex-direction: row;
            height: 100vh;
        }
        .main_left {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-height: 100vh;
        }
        .main_header {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
        }
        .main_content {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            width: 100%;
            position: relative;
            padding: 20px;
        }
        .main_side {
            display: none;
            position: relative;
            flex-basis: 41.7%;
            background-image: linear-gradient(to bottom right, #011B67, #110BF5);
            overflow: hidden;
            line-height: 1.25;
        }
        @media (min-width: 48em) {
            .main_left {
                flex-basis: 58.3%;
            }
            .main_side {
                display: block;
            }
        }
        .reassurance_wording {
            position: absolute;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            width: 66.6666666667%;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            z-index: 2;
            animation: appear 1s ease-in 0s 1;
        }
        @keyframes appear {
            0% { opacity: 0 }
            to { opacity: 1 }
        }
        .reassurance_wording_title {
            color: #fff;
            font-size: 36px;
            font-weight: 700;
        }
        .reassurance_wording_description {
            color: #fff;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.6;
        }
        a {
            text-decoration: none;
            font-weight: 600;
            color: #0050d7;
            transition: background-size 0.2s ease-out;
            background-image: linear-gradient(currentcolor, currentcolor);
            background-position: 0px 100%;
            background-repeat: no-repeat;
            background-size: 0px 1px;
        }
        a:hover {
            cursor: pointer;
            text-decoration: none;
            background-size: 100% 1px;
        }
        .btn {
            width: 100%;
            height: 50px;
            border-radius: 6px;
            box-shadow: none;
            text-shadow: none;
            cursor: pointer;
            text-align: center;
            vertical-align: middle;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.2s ease;
        }
        .btn:hover {
            box-shadow: 0 0 6px 0 rgba(0, 14, 156, 0.2);
        }
        .btn-primary {
            color: #ffffff;
            background: #0050d7;
            border: none;
        }
        .btn-primary:hover {
            background-color: #000e9c;
        }
        .btn-primary:active {
            background-color: #00185e;
        }
        .ovh-field {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            align-items: center;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #ffffff;
            margin-bottom: 20px;
            transition: border-color 0.2s;
        }
        .ovh-field:focus-within {
            border-color: #000e9c;
            box-shadow: 0 0 0 1px #000e9c;
        }
        .ovh-field-input {
            flex: 1 1 auto;
            display: inline-block;
            position: relative;
            width: 100%;
        }
        .ovh-field-input > input {
            background: none;
            border: 0px;
            box-sizing: border-box;
            height: 44px;
            padding: 10px 14px;
            width: 100%;
            color: #4d5592;
            font-family: "Source Sans 3";
            font-size: 1rem;
        }
        .ovh-field-input > input:focus {
            outline: none;
        }
        .ovh-field-input-action {
            position: absolute;
            top: 0px;
            bottom: 0px;
            right: 12px;
            display: flex;
            align-items: center;
        }
        .ovh-field-label {
            color: #00185e;
            font-family: "Source Sans 3";
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .signin-title {
            font-size: 32px;
            font-weight: 700;
            color: #00185e;
            margin-bottom: 6px;
        }
        .login-panel {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }
        .lost-user-password {
            margin-top: 10px;
        }
        .lost-user-password button {
            background: none;
            border: none;
            margin: 0;
            padding: 0;
            color: #0050d7;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
        }
        .lost-user-password button:hover {
            text-decoration: underline;
        }
        .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: #011B67;
        }
        .logo-text span {
            color: #0050d7;
        }
    </style>
</head>
<body>

<div class="main">
    <div class="main_left">
        <div class="main_header">
            <div class="logo-text">TLF<span>Host</span></div>
            <div></div>
        </div>
        
        <div class="main_content">
            <div style="width: 100%; max-width: 28rem;">
                <div id="login-panel" class="login-panel">
                    
                    <div class="signin-title">Sign in</div>
                    
                    <div style="color: #4d5592; font-size: 14px;">
                        You do not have an account? <a href="register.php">Create an account</a>
                    </div>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>

                    <form id="login-form" method="POST" action="">
                        <div>
                            <div id="login-account-label" class="ovh-field-label">Account ID, Username or Email address *</div>
                            <div class="ovh-field">
                                <div class="ovh-field-input">
                                    <input aria-labelledby="login-account-label" type="text" id="account" name="username_or_email" autofocus required="required" value="<?php echo htmlspecialchars($username_or_email ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div id="login-account-password" class="ovh-field-label">Password *</div>
                            <div class="ovh-field">
                                <div class="ovh-field-input">
                                    <input aria-labelledby="login-account-password" type="password" id="password" name="password" required="required"/>
                                    <div class="ovh-field-input-action">
                                        <svg id="password-show" xmlns="http://www.w3.org/2000/svg" style="cursor: pointer; flex-shrink: 0;" width="20" height="20" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M19.778 4.222c.39.39.32 1.094-.157 1.571L5.793 19.621c-.477.477-1.18.548-1.571.157-.39-.39-.32-1.094.157-1.571L18.207 4.379c.477-.477 1.18-.548 1.571-.157zm-3.319 5.947l2.81-2.812a13.098 13.098 0 0 1 3.38 3.486 2.124 2.124 0 0 1-.004 2.32c-2.245 3.39-5.866 5.495-9.815 5.78-.181.022-.364.04-.55.05l-.279.008-.02-.001a6.747 6.747 0 0 1-.796-.055 12.719 12.719 0 0 1-2.943-.56l1.815-1.814A4.602 4.602 0 0 0 12 17l.013-.001c2.632-.006 4.778-2.234 4.778-5a5.18 5.18 0 0 0-.331-1.83l2.81-2.812zm-4.463-5.17h.011c.006 0 .011.003.017.003.271 0 .537.022.8.055.895.063 1.774.22 2.624.466l-1.786 1.786a4.595 4.595 0 0 0-1.37-.3l-.29-.01c-2.638.002-4.79 2.232-4.79 5a5.2 5.2 0 0 0 .227 1.531l-2.946 2.947a13.116 13.116 0 0 1-3.15-3.32 2.128 2.128 0 0 1 .003-2.32c2.252-3.4 5.887-5.506 9.84-5.782.261-.032.525-.053.795-.054h.008L12.007 5zM5.883 8.982a11.161 11.161 0 0 0-2.866 2.955c-.023.037-.023.092-.003.123a11.177 11.177 0 0 0 2.87 2.96 7.139 7.139 0 0 1-.673-3.02c0-1.08.246-2.102.672-3.018zm12.241.01a7.134 7.134 0 0 1 0 6.018 11.154 11.154 0 0 0 2.85-2.944.125.125 0 0 0 .004-.124 11.17 11.17 0 0 0-2.854-2.95z" fill="#0050D7"></path>
                                        </svg>
                                        <svg id="password-show-open" style="display: none; cursor: pointer; flex-shrink: 0;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M12.016 5c.28.003.545.024.808.057 3.946.28 7.573 2.385 9.825 5.786a2.124 2.124 0 0 1-.004 2.32c-2.245 3.39-5.866 5.495-9.815 5.78a6.657 6.657 0 0 1-.829.058h-.012a6.87 6.87 0 0 1-.804-.056c-3.952-.276-7.587-2.382-9.843-5.787a2.128 2.128 0 0 1 .004-2.321c2.252-3.4 5.887-5.506 9.84-5.782.261-.032.525-.053.795-.054h.035zm-.014 2c-2.638 0-4.79 2.23-4.79 5 0 2.769 2.151 4.999 4.787 5l.013-.001c2.632-.006 4.778-2.234 4.778-5 0-2.768-2.152-4.998-4.788-5zM5.883 8.98a11.161 11.161 0 0 0-2.866 2.956c-.023.037-.023.092-.003.123a11.177 11.177 0 0 0 2.87 2.96 7.139 7.139 0 0 1-.673-3.02c0-1.08.246-2.102.672-3.018zm12.241.012a7.134 7.134 0 0 1 0 6.017 11.154 11.154 0 0 0 2.85-2.944.125.125 0 0 0 .004-.124 11.17 11.17 0 0 0-2.854-2.95zm-6.028.826a1.658 1.658 0 0 0-.095.545c0 .904.707 1.637 1.579 1.637.179 0 .358-.035.526-.099v.1c0 1.204-.942 2.181-2.105 2.181S9.896 13.205 9.896 12s.942-2.182 2.105-2.182z" fill="#0050D7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 24px;">
                            <button id="login-submit" type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>

<div class="lost-user-password">
    <a href="forgot-password.php" style="font-size: 14px; font-weight: 600;">Forgot password?</a>
</div>                </div>
            </div>
        </div>
    </div>
    
    <div class="main_side">
        <div class="reassurance_wording">
            <div class="reassurance_wording_title">TLFHost Infrastructure</div>
            <div class="reassurance_wording_description">
                Deploy specialized pre-configured environments, manage high-privacy server nodes, and access semi-managed support ticketing system. Enhanced privacy beyond just standard parameters.
            </div>
        </div>
    </div>
</div>

<script>
    // Pure Javascript logic to toggle input text visibility safely using the precise elements
    const passwordField = document.getElementById('password');
    const passwordShowEyeClosed = document.getElementById('password-show');
    const passwordShowEyeOpen = document.getElementById('password-show-open');

    passwordShowEyeClosed.addEventListener("click", () => {
        passwordField.type = 'text';
        passwordShowEyeOpen.style.display = 'block';
        passwordShowEyeClosed.style.display = 'none';
    });
    passwordShowEyeOpen.addEventListener("click", () => {
        passwordField.type = 'password';
        passwordShowEyeOpen.style.display = 'none';
        passwordShowEyeClosed.style.display = 'block';
    });
</script>
</body>
</html>