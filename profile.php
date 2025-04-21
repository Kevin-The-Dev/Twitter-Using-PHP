<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    if (getimagesize($file['tmp_name'])) {
        $upload_dir = 'uploads/';
        $file_name = time() . '_' . basename($file['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $file_name, $user_id);
            $stmt->execute();
            header("Location: profile.php");
            exit();
        } else {
            $error = "Error uploading file.";
        }
    } else {
        $error = "Please upload a valid image.";
    }
}

// Handle bio update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bio'])) {
    $bio = trim($_POST['bio']);
    $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->bind_param("si", $bio, $user_id);
    $stmt->execute();
    header("Location: profile.php");
    exit();
}

// Fetch user info
$stmt = $conn->prepare("SELECT username, email, bio, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Follower & Following count
$follower_stmt = $conn->prepare("SELECT COUNT(*) AS count FROM followers WHERE followed_id = ?");
$follower_stmt->bind_param("i", $user_id);
$follower_stmt->execute();
$follower_count = $follower_stmt->get_result()->fetch_assoc()['count'];

$following_stmt = $conn->prepare("SELECT COUNT(*) AS count FROM followers WHERE follower_id = ?");
$following_stmt->bind_param("i", $user_id);
$following_stmt->execute();
$following_count = $following_stmt->get_result()->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Twitter Clone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background-color: #f5f8fa;
            color: #14171a;
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            min-height: 100vh;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #e1e8ed;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        /* Navigation items */
        .sidebar .nav-item {
            display: flex;
            align-items: center;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 30px;
            color: #14171a;
            transition: background-color 0.3s, color 0.3s;
            text-decoration: none;
        }

        .sidebar .nav-item i {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Hover effect */
        .sidebar .nav-item:hover {
            background-color: #e8f5fd;
            color: #1da1f2;
        }

        /* Tweet button */
        .sidebar .post-tweet-btn {
            background-color: #1da1f2;
            color: white;
            padding: 10px 0;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s;
            text-align: center;
        }

        .sidebar .post-tweet-btn:hover {
            background-color: #0d8ddb;
        }

        /* Logout button */
        .sidebar a[href="logout.php"] {
         
            color: #e0245e;
            font-weight: bold;
            border-top: 1px solid #e1e8ed;
            padding-top: 15px;
        }

        .sidebar a[href="logout.php"]:hover {
            color: #c2184f;
        }

        .main-content {
            margin-left: 270px;
            padding: 20px;
            flex: 1;
        }

        .profile-header {
            display: flex;
            align-items: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            border: 2px solid #e1e8ed;
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .profile-handle {
            color: #657786;
            font-size: 16px;
        }

        h3 {
            margin: 20px 0 10px;
            font-size: 20px;
            color: #14171a;
        }

        p {
            line-height: 1.6;
            margin-bottom: 15px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #e1e8ed;
            border-radius: 5px;
            resize: vertical;
            margin-bottom: 10px;
            font-size: 14px;
        }

        input[type="file"] {
            margin-bottom: 10px;
            font-size: 14px;
        }

        button {
            padding: 12px 24px;
            background-color: #1da1f2;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
            min-width: 100px;
        }

        button:hover {
            background-color: #1a91da;
        }

        .tweet {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .tweet small {
            color: #657786;
            font-size: 12px;
        }

        hr {
            border: none;
            border-top: 1px solid #e1e8ed;
            margin: 10px 0;
        }

        /* Responsive design for mobile */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                margin: 0;
            }

            .sidebar {
                width: 100%;
                height: auto;
                flex-direction: row;
                justify-content: space-around;
                align-items: center;
                position: fixed;
                bottom: 0;
                top: auto;
                border-top: 1px solid #e1e8ed;
                border-right: none;
                background-color: #fff;
                box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
                padding: 10px;
                z-index: 1000;
            }

            .sidebar .nav-item {
                font-size: 0; /* Hide text */
                padding: 12px;
                border-radius: 50%;
                width: 48px;
                height: 48px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .sidebar .nav-item i {
                margin-right: 0;
                font-size: 24px;
            }

            .sidebar .nav-item span {
                display: none; /* Hide text labels */
            }

            .sidebar .post-tweet-btn {
                font-size: 0;
                padding: 12px;
                border-radius: 50%;
                width: 48px;
                height: 48px;
                display: flex;
                justify-content: center;
                align-items: center;
                margin-top: 0;
                position: relative;
            }

            .sidebar .post-tweet-btn::before {
                content: '\f075'; /* Font Awesome comment icon for Tweet */
                font-family: 'Font Awesome 6 Free';
                font-weight: 900;
                font-size: 24px;
            }

            .sidebar a[href="logout.php"] {
                margin-top: 0;
                border-top: none;
                padding-top: 0;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
                margin-bottom: 70px; /* Space for bottom sidebar */
            }

            .profile-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .profile-avatar {
                width: 80px;
                height: 80px;
                margin-right: 0;
                margin-bottom: 15px;
            }

            .profile-name {
                font-size: 20px;
            }

            .profile-handle {
                font-size: 14px;
            }

            h3 {
                font-size: 18px;
            }

            form {
                padding: 15px;
            }

            textarea {
                font-size: 13px;
            }

            input[type="file"] {
                font-size: 13px;
            }

            button {
                padding: 10px 20px;
                font-size: 13px;
                min-width: 80px;
            }

            .tweet {
                padding: 12px;
            }

            .tweet small {
                font-size: 11px;
            }
        }

        /* Extra small devices (e.g., iPhone SE, 320px) */
        @media (max-width: 375px) {
            .sidebar .nav-item,
            .sidebar .post-tweet-btn {
                width: 40px;
                height: 40px;
                padding: 10px;
            }

            .sidebar .nav-item i,
            .sidebar .post-tweet-btn::before {
                font-size: 20px;
            }

            .main-content {
                padding: 10px;
                margin-bottom: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <a href="home.php" class="nav-item">
                <i class="fas fa-home"></i><span>Home</span>
            </a>
            <a href="profile.php" class="nav-item">
                <i class="fas fa-user"></i><span>Profile</span>
            </a>
            <a href="home.php" class="post-tweet-btn">Tweet</a>
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </nav>
        <main class="main-content">
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
            <div class="profile-header">
                <img src="Uploads/<?php echo $user_data['profile_picture'] ?: 'default.jpg'; ?>" alt="Profile Picture" class="profile-avatar">
                <div class="profile-info">
                    <div class="profile-name">Welcome, <?php echo $user_data['username']; ?>!</div>
                    <div class="profile-handle">
                        <strong>Email:</strong> <?php echo $user_data['email']; ?> | 
                        <strong>Followers:</strong> <?php echo $follower_count; ?> | 
                        <strong>Following:</strong> <?php echo $following_count; ?>
                    </div>
                </div>
            </div>

            <h3>Bio</h3>
            <p><?php echo nl2br(htmlspecialchars($user_data['bio'])); ?></p>

            <!-- Bio Update Form -->
            <form method="POST">
                <textarea name="bio" placeholder="Update your bio..." required><?php echo htmlspecialchars($user_data['bio']); ?></textarea><br>
                <button type="submit">Update Bio</button>
            </form>

            <h3>Change Profile Picture</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_picture" accept="image/*" required>
                <button type="submit">Upload</button>
            </form>

            <h3>Your Tweets</h3>
            <?php
            $stmt = $conn->prepare("SELECT id, text, created_at FROM tweets WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $tweets = $stmt->get_result();

            while ($tweet = $tweets->fetch_assoc()):
            ?>
                <div class="tweet">
                    <p><?php echo htmlspecialchars($tweet['text']); ?></p>
                    <small><?php echo $tweet['created_at']; ?></small>
                </div>
                <hr>
            <?php endwhile; ?>
        </main>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>