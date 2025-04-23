<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all tweets
$query = "
    SELECT tweets.id, tweets.text, tweets.created_at, tweets.user_id, users.username, users.profile_picture
    FROM tweets
    JOIN users ON tweets.user_id = users.id
    ORDER BY tweets.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->execute();
$tweets = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <title>Home - Twitter Clone</title>
</head>
<style>
    body {
        margin: 0;
        font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: #f5f8fa;
    }

    .container {
        display: flex;
        flex-direction: row;
        min-height: 100vh;
    }

    .tweet-action {
        background-color: transparent;
        color: #1da1f2;
        border: 1px solid #1da1f2;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .tweet-action:hover {
        background-color: #1da1f2;
        color: white;
    }

    .tweet-action:disabled {
        background-color: #e1e8ed;
        color: #657786;
        border-color: #e1e8ed;
        cursor: not-allowed;
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

    .post-tweet-btn {
        background-color: #1da1f2;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 20px;
        width: 100%;
    }

    .main-content {
        flex: 1;
        padding: 20px;
        max-width: 600px;
        margin: auto;
    }

    .post-tweet-form {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .post-tweet-input {
        width: 100%;
        border: none;
        resize: none;
        font-size: 16px;
        padding: 10px;
        outline: none;
    }

    .tweet {
        background-color: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        display: flex;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .tweet-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .tweet-content {
        flex: 1;
    }

    .tweet-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .tweet-user {
        color: #1da1f2;
        text-decoration: none;
        font-weight: bold;
    }

    .tweet-handle {
        color: #657786;
        font-size: 12px;
    }

    .tweet-text {
        font-size: 15px;
        margin: 10px 0;
        line-height: 1.4;
    }

    .tweet-action {
        background: none;
        border: none;
        color: #1da1f2;
        cursor: pointer;
        font-size: 14px;
        margin-right: 10px;
    }

    .comments-section {
        background-color: #ffffff;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
        border: 1px solid #e1e8ed;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .comments-section strong {
        font-size: 15px;
        color: #14171a;
        display: block;
        margin-bottom: 12px;
    }

    .comment-item {
        margin-bottom: 15px;
        padding: 12px;
        border-radius: 6px;
        display: flex;
        gap: 12px;
        background-color: #f7f9fa;
        transition: background-color 0.2s;
    }

    .comment-item:hover {
        background-color: #f0f2f5;
    }

    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e1e8ed;
    }

    .comment-user {
        font-size: 14px;
        color: #1da1f2;
        font-weight: 600;
        text-decoration: none;
    }


    .comment-user:hover {
        text-decoration: underline;
    }

    .comment-handle {
        font-size: 12px;
        color: #657786;
        margin-left: 6px;
    }

    .comment-text {
        margin-top: 8px;
        font-size: 14px;
        color: #14171a;
        line-height: 1.5;
    }

    @media screen and (max-width: 768px) {
        .container {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            height: auto;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px;
        }

        .main-content {
            padding: 10px;
        }

        .post-tweet-btn {
            margin-top: 0;
            width: auto;
        }

        .comment-item {
            padding: 10px;
            gap: 10px;
        }

        .comment-avatar {
            width: 35px;
            height: 35px;
        }
    }
</style>

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
            <a href="#tweet-form" class="post-tweet-btn">Tweet</a>
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </nav>


        <!-- Main Content -->
        <main class="main-content">
            <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>

            <!-- Post a Tweet Form -->
            <form method="POST" action="post_tweet.php" class="post-tweet-form">
                <textarea class="post-tweet-input" name="tweet_text" placeholder="What's happening?"
                    required></textarea>
                <button type="submit" class="post-tweet-btn">Post Tweet</button>
            </form>

            <h4>Recent Tweets</h4>
            <?php while ($tweet = $tweets->fetch_assoc()): ?>
                <div class="tweet">
                    <!-- Display Profile Picture of User -->
                    <img src="Uploads/<?php echo $tweet['profile_picture'] ?: 'default.jpg'; ?>" alt="Profile picture"
                        class="tweet-avatar">

                    <div class="tweet-content">
                        <div class="tweet-header">
                            <strong>
                                <a href="view_profile.php?id=<?php echo $tweet['user_id']; ?>" class="tweet-user">
                                    @<?php echo htmlspecialchars($tweet['username']); ?>
                                </a>
                            </strong>
                            <small
                                class="tweet-handle"><?php echo (new DateTime($tweet['created_at']))->format('d M Y H:i'); ?></small>
                        </div>
                        <p class="tweet-text"><?php echo nl2br(htmlspecialchars($tweet['text'])); ?></p>

                        <div style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND tweet_id = ?");
                            $stmt->bind_param("ii", $user_id, $tweet['id']);
                            $stmt->execute();
                            $like_result = $stmt->get_result();

                            $like_count_stmt = $conn->prepare("SELECT COUNT(*) AS total_likes FROM likes WHERE tweet_id = ?");
                            $like_count_stmt->bind_param("i", $tweet['id']);
                            $like_count_stmt->execute();
                            $like_count = $like_count_stmt->get_result()->fetch_assoc()['total_likes'];
                            ?>

                            <?php if ($like_result->num_rows > 0): ?>
                                <!-- Liked Tweet -->
                                <form id="like-form-<?php echo $tweet['id']; ?>" style="display:inline;">
                                    <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                                    <button type="button" onclick="likeTweet(<?php echo $tweet['id']; ?>)"
                                        style="background: none; border: none; color: #f91880; cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 14px; padding: 6px 12px; transition: color 0.2s;">
                                        <span style="font-size: 16px;">❤️</span> <span
                                            id="like-count-<?php echo $tweet['id']; ?>"><?php echo $like_count; ?></span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <!-- Unliked Tweet -->
                                <form id="like-form-<?php echo $tweet['id']; ?>" style="display:inline;">
                                    <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                                    <button type="button" onclick="likeTweet(<?php echo $tweet['id']; ?>)"
                                        style="background: none; border: none; color: #71767b; cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 14px; padding: 6px 12px; transition: color 0.2s;">
                                        <span style="font-size: 16px;">🤍</span> <span
                                            id="like-count-<?php echo $tweet['id']; ?>"><?php echo $like_count; ?></span>
                                    </button>
                                </form>
                            <?php endif; ?>


                            <!-- Comment Button -->
                            <button type="button"
                                onclick="document.getElementById('comment-form-<?php echo $tweet['id']; ?>').style.display='block'; this.style.display='none'"
                                style="
                                background: none;
                                border: none;
                                color: #1d9bf0;
                                cursor: pointer;
                                display: flex;
                                align-items: center;
                                gap: 5px;
                                font-size: 14px;
                                padding: 6px 12px;
                                transition: color 0.2s;
                            ">
                                <span style="font-size: 16px;">💬</span> Comment
                            </button>
                        </div>

                        <!-- Comment Form (hidden by default) -->
                        <form method="POST" action="comment_tweet.php" id="comment-form-<?php echo $tweet['id']; ?>"
                            style="margin-top:10px; display:none;">
                            <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                            <textarea name="comment_text" placeholder="Write a comment..." required
                                style="width:100%; height:60px; border:1px solid #e1e8ed; border-radius:10px; padding:10px; font-size:14px; resize:none; margin-bottom:8px;"></textarea>
                            <div style="display: flex; gap: 10px;">
                                <button type="submit"
                                    style="background-color:#1da1f2; color:white; border:none; padding:6px 12px; border-radius:30px; font-size:14px; cursor:pointer;">
                                    Post Comment
                                </button>
                                <button type="button"
                                    onclick="document.getElementById('comment-form-<?php echo $tweet['id']; ?>').style.display='none'; document.querySelector('button[onclick]').style.display='flex'"
                                    style="background-color:#e1e8ed; color:#657786; border:none; padding:6px 12px; border-radius:30px; font-size:14px; cursor:pointer;">
                                    Cancel
                                </button>
                            </div>
                        </form>

                        <!-- Show Comments -->
                        <div class="comments-section">
                            <strong>Comments:</strong><br>
                            <?php
                            $comment_query = $conn->prepare("
                                SELECT comments.comment_text, comments.created_at, comments.user_id, users.username, users.profile_picture
                                FROM comments
                                JOIN users ON comments.user_id = users.id
                                WHERE tweet_id = ?
                                ORDER BY comments.created_at DESC
                            ");
                            $comment_query->bind_param("i", $tweet['id']);
                            $comment_query->execute();
                            $comments = $comment_query->get_result();

                            while ($comment = $comments->fetch_assoc()):
                                ?>
                                <div class="comment-item">
                                    <!-- Display Profile Picture of Commenter -->
                                    <img src="Uploads/<?php echo htmlspecialchars($comment['profile_picture'] ?: 'default.jpg'); ?>"
                                        alt="Profile picture" class="comment-avatar">
                                    <div>
                                        <div>
                                            <a href="view_profile.php?id=<?php echo $comment['user_id']; ?>"
                                                class="comment-user">
                                                @<?php echo htmlspecialchars($comment['username']); ?>
                                            </a>
                                            <span
                                                class="comment-handle"><?php echo (new DateTime($comment['created_at']))->format('d M Y H:i'); ?></span>
                                        </div>
                                        <p class="comment-text"><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </main>
    </div>
    <!-- Font Awesome for icons -->
    <script>
        function likeTweet(tweetId) {
            var form = document.getElementById('like-form-' + tweetId);
            var formData = new FormData(form);

            // Create an XMLHttpRequest (AJAX request)
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'like_tweet.php', true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Handle the response
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        var likeCountElement = document.getElementById('like-count-' + tweetId);
                        likeCountElement.innerText = response.new_like_count;

                        // Change button color and icon based on like status
                        var button = form.querySelector('button');
                        if (response.liked) {
                            button.style.color = '#f91880';  // Red color for liked tweet
                            button.querySelector('span').innerText = '❤️';
                        } else {
                            button.style.color = '#71767b';  // Default color for unliked tweet
                            button.querySelector('span').innerText = '🤍';
                        }
                    } else {
                        console.error('Error processing like');
                    }
                }
            };

            // Send the form data via AJAX
            xhr.send(formData);
        }
    </script>

    </script>
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>
</body>

</html>