<?php
session_start(); // Start session to store user info
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_or_username = trim($_POST['email_or_username']);
    $password = $_POST['password'];

    // Check if email or username exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email_or_username, $email_or_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php"); // Redirect to index.php
            exit();
        } else {
            $error = "❌ Incorrect password.";
        }
    } else {
        $error = "❌ User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Twitter Clone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #15202b;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Login Container */
        .login-container {
            background-color: #192734;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
            position: relative;
        }

        .login-container h2 {
            margin-bottom: 24px;
            font-size: 28px;
            color: #ffffff;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(25, 39, 52, 0.9);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10;
            display: none;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(29, 161, 242, 0.2);
            border-top-color: #1da1f2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        .loading-text {
            color: #ffffff;
            font-size: 16px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Error Message */
        .error-message {
            background-color: rgba(244, 33, 46, 0.1);
            color: #f4212e !important;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Input Fields */
        .login-input {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 16px;
            border: 1px solid #38444d;
            border-radius: 8px;
            background-color: #192734;
            color: #ffffff;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .login-input:focus {
            outline: none;
            border-color: #1da1f2;
        }

        .login-input::placeholder {
            color: #8899a6;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 14px;
            background-color: #1da1f2;
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            position: relative;
        }

        .login-btn:hover {
            background-color: #1991db;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 0 15px;
            }
            
            .login-container h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
            <div class="loading-text">Logging in...</div>
        </div>
        
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="POST" action="" id="loginForm">
            <input type="text" name="email_or_username" class="login-input" placeholder="Email or Username" required><br>
            <input type="password" name="password" class="login-input" placeholder="Password" required><br>
            <button type="submit" class="login-btn" id="loginBtn">Login</button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Show loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Disable the button to prevent multiple submissions
            document.getElementById('loginBtn').disabled = true;
            
            // The form will continue with submission automatically
        });
    </script>
</body>
</html>