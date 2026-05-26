<?php
// register.php (Integrated with Premium OVHcloud Style Layout)

session_start();
require_once __DIR__ . '/config/db.php';

// If user is already logged in, redirect straight to user dashboard securely
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Basic Validations
    if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid email address.";
        } elseif (strlen($password) < 6) {
            $error_message = "Password must be at least 6 characters long.";
        } elseif ($password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        } else {
            try {
                // Check if username or email already exists
                $stmt = $pdo->prepare("SELECT id FROM tlf_users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                
                if ($stmt->fetch()) {
                    $error_message = "Username or Email address is already registered.";
                } else {
                    // Hash using sha256 to match your established secure schema
                    $hashed_password = hash('sha256', $password);

                    // Insert the new account into database
                    $insertStmt = $pdo->prepare("INSERT INTO tlf_users (username, email, password_hash, created_at, last_login) VALUES (?, ?, ?, NOW(), NOW())");
                    $insertStmt->execute([$username, $email, $hashed_password]);

                    // Get the newly created user ID
                    $new_user_id = $pdo->lastInsertId();

                    // Automatically establish sessions so they log in instantly
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_id'] = $new_user_id;
                    $_SESSION['username'] = $username;

                    // Redirect to welcome/dashboard view
                    header("Location: dashboard.php");
                    exit;
                }
            } catch (PDOException $e) {
                $error_message = "Database error during registration: " . $e->getMessage();
            }
        }
    } else {
        $error_message = "Please fill in all the required registration fields.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account - TLFHost</title>
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
            padding: 40px 20px;
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
            margin-bottom: 16px;
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
            margin-bottom: 6px;
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
            gap: 20px;
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
    <!-- Left Interactive Registration Area -->
    <div class="main_left">
        <div class="main_header">
            <div class="logo-text">TLF<span>Host</span></div>
            <div></div>
        </div>
        
        <div class="main_content">
            <div style="width: 100%; max-width: 28rem;">
                <div class="login-panel">
                    
                    <div class="signin-title">Create account</div>
                    
                    <div style="color: #4d5592; font-size: 14px;">
                        Already have an account? <a href="login.php">Sign in</a>
                    </div>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert-error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>

                    <form id="register-form" method="POST" action="">
                        <div>
                            <div class="ovh-field-label">Username *</div>
                            <div class="ovh-field">
                                <div class="ovh-field-input">
                                    <input type="text" name="username" autofocus required="required" value="<?php echo htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="ovh-field-label">Email address *</div>
                            <div class="ovh-field">
                                <div class="ovh-field-input">
                                    <input type="email" name="email" required="required" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"/>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="ovh-field-label">Password *</div>
                            <div class="ovh-field">
                                <div class="ovh-field-input">
                                    <input type="password" id="password" name="password" required="required"/>
                                    <div class="ovh-field-input-action">
                                        <!-- Toggle eye icons for main password field -->
                                        <svg id="password-show" xmlns="http://www.w3.org/2000/svg" style="cursor: pointer;" width="20" height="20" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M19.778 4.222c.39.39.32 1.094-.157 1.571L5.793 19.621c-.477.477-1.18.548-1.571.157-.39-.39-.32-1.094.157-1.571L18.207 4.379c.477-.477 1.18-.548 1.571-.157zm-3.319 5.947l2.81-2.812a13.098 13.098 0 0 1 3.38 3.486 2.124 2.124 0 0 1-.004 2.32c-2.245 3.39-5.866 5.495-9.815 5.78-.181.022-.364.04-.55.05l-.279.008-.02-.001a6.747 6.747 0 0 1-.796-.055 12.719 12.719 0 0 1-2.943-.56l1.815-1.814A4.602 4.602 0 0 0 12 17l.013-.001c2.632-.006 4.778-2.234 4.778-5a5.18 5.18 0 0 0-.331-1.83l2.81-2.812zm-4.463-5.17h.011c.006 0 .011.003.017.003.271 0 .537.022.8.055.895.063 1.774.22 2.624.466l-1.786 1.786a4.595 4.595 0 0 0-1.37-.3l-.29-.01c-2.638.002-4.79 2.232-4.79 5a5.2 5.2 0 0 0 .227 1.531l-2.946 2.947a13.116 13.116 0 0 1-3.15-3.32 2.128 2.128 0 0 1 .003-2.32c2.252-3.4 5.887-5.506 9.84-5.782.261-.032.525-.053.795-.054h.008L12.007 5zM5.883 8.982a11.161 11.161 0 0 0-2.866 2.955c-.023.037-.023.092-.003.123a11.177 11.177 0 0 0 2.87 2.96 7.139 7.139 0 0 1-.673-3.02c0-1.08.246-2.102.672-3.018zm12.241.01a7.134 7.134 0 0 1 0 6.018 11.154 11.154 0 0 0 2.85-2.944.125.125 0 0 0 .004-.124 11.17 11.17 0 0 0-2.854-2.95z" fill="#0050D7"></path>
                                        </svg>
                                        <svg id="password-show-open" style="display: none; cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M12.016 5c.28.003.545.024.808.057 3.946.28 7.573 2.385 9.825 5.786a2.124 2.124 0 0 1-.004 2.32c-2.245 3.39-5.866 5.495-9.815 5.78a6.657 6.657 0 0 1-.829.058h-.012a6.87 6.87 0 0 1-.804-.056c-3.952-.276-7.587-2.382-9.843-5.787a2.128 2.128 0 0 1 .004-2.321c2.252-3.4 5.887-5.506 9.84-5.782.261-.032.525-.053.795-.054h.035zm-.014 2c-2.638 0-4.79 2.23-4.79 5 0 2.769 2.151 4.999 4.787 5l.013-.001c2.632-.006 4.778-2.234 4.778-5 0-2.768-2.152-4.998-4.788-5zM5.883 8.98a11.161 11.161 0 0 0-2.866 2.956c-.023.037-.023.092-.003.123a11.177 11.177 0 0 0 2.87 2.96 7.139 7.139 0 0 1-.673-3.02c0-1.08.246-2.102.672-3.018zm12.241.012a7.134 7.134 0 0 1 0 6.017 11.154 11.154 0 0 0 2.85-2.944.125.125 0 0 0 .004-.124 11.17 11.17 0 0 0-2.854-2.95zm-6.028.826a1.658 1.658 0 0 0-.095.545c0 .904.707 1.637 1.579 1.637.179 0 .358-.035.526-.099v.1c0 1.204-.942 2.181-2.105 2.181S9.896 13.205 9.896 12s.942-2.182 2.105-2.182z" fill="#0050D7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="ovh-field-label">Confirm Password *</div>
                            <div class="ovh-field">
                                <div class="ovh-field-input">
                                    <input type="password" id="confirm_password" name="confirm_password" required="required"/>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 24px;">
                            <button type="submit" class="btn btn-primary">Create my account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Gradient Branding Sidebar Panel -->
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
    // Visibility toggle logic for the password fields
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    const passwordShowEyeClosed = document.getElementById('password-show');
    const passwordShowEyeOpen = document.getElementById('password-show-open');

    passwordShowEyeClosed.addEventListener("click", () => {
        passwordField.type = 'text';
        confirmPasswordField.type = 'text';
        passwordShowEyeOpen.style.display = 'block';
        passwordShowEyeClosed.style.display = 'none';
    });
    passwordShowEyeOpen.addEventListener("click", () => {
        passwordField.type = 'password';
        confirmPasswordField.type = 'password';
        passwordShowEyeOpen.style.display = 'none';
        passwordShowEyeClosed.style.display = 'block';
    });
</script>
</body>
</html>