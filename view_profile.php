<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "User not specified.";
    exit();
}

$profile_id = intval($_GET['id']);
$logged_in_user = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT username, bio, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user = $user_result->fetch_assoc();

// Follower / Following count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM followers WHERE followed_id = ?");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$follower_count = $stmt->get_result()->fetch_assoc()['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM followers WHERE follower_id = ?");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$following_count = $stmt->get_result()->fetch_assoc()['count'];

// Check if current user is following this profile
$stmt = $conn->prepare("SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?");
$stmt->bind_param("ii", $logged_in_user, $profile_id);
$stmt->execute();
$is_following = $stmt->get_result()->num_rows > 0;

// Fetch tweets
$stmt = $conn->prepare("SELECT text, created_at FROM tweets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$tweets = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@<?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
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

        .stats {
            display: flex;
            gap: 20px;
            margin: 10px 0;
        }

        .stat {
            font-weight: bold;
        }

        .stat span {
            color: #657786;
            font-weight: normal;
        }

        .follow-btn {
            background-color: <?php echo $is_following ? '#e0245e' : '#1da1f2'; ?>;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .follow-btn:hover {
            background-color: <?php echo $is_following ? '#c21c4d' : '#0d8ddb'; ?>;
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

        .tweets-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .tweet {
            padding: 15px;
            border-bottom: 1px solid #e1e8ed;
        }

        .tweet:last-child {
            border-bottom: none;
        }

        .tweet-content {
            margin-bottom: 5px;
        }

        .tweet-time {
            color: #657786;
            font-size: 14px;
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
                font-size: 0;
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
                display: none;
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
                content: '\f075';
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
                margin-bottom: 70px;
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

            .tweet {
                padding: 12px;
            }

            .tweet-time {
                font-size: 11px;
            }
        }

        /* Extra small devices */
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
            <a href="index.php" class="nav-item">
                <i class="fas fa-home"></i><span>Home</span>
            </a>
            <a href="profile.php" class="nav-item">
                <i class="fas fa-user"></i><span>Profile</span>
            </a>
            <a href="index.php" class="post-tweet-btn">Tweet</a>
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </nav>
        
        <main class="main-content">
            <div class="profile-header">
                <img src="uploads/<?php echo $user['profile_picture'] ?: 'default.jpg'; ?>" alt="Profile Picture" class="profile-avatar">
                <div class="profile-info">
                    <h1 class="profile-name">@<?php echo htmlspecialchars($user['username']); ?></h1>
                    <p class="bio"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                    <div class="stats">
                        <div class="stat"><?php echo $follower_count; ?> <span>Followers</span></div>
                        <div class="stat"><?php echo $following_count; ?> <span>Following</span></div>
                    </div>
                    <?php if ($profile_id != $logged_in_user): ?>
                        <form action="follow_toggle.php" method="POST" style="display: inline;">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <button type="submit" class="follow-btn">
                                <?php echo $is_following ? "Unfollow" : "Follow"; ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tweets-container">
                <h3>Tweets</h3>
                <?php if ($tweets->num_rows > 0): ?>
                    <?php while ($tweet = $tweets->fetch_assoc()): ?>
                        <div class="tweet">
                            <p class="tweet-content"><?php echo htmlspecialchars($tweet['text']); ?></p>
                            <small class="tweet-time"><?php echo $tweet['created_at']; ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No tweets yet.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>