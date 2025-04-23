<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

</style>

<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <a href="home.php" class="nav-item" style="text-decoration: none;">
                <i class="fas fa-home"></i><span> Home</span>
            </a>
            <a href="view_profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="nav-item"
                style="text-decoration: none;">
                <i class="fas fa-user"></i><span> Profile</span>
            </a>
            <a href="#tweet-form" class="post-tweet-btn" style="display: block; text-align: center;">Tweet</a>
            <a href="logout.php" class="nav-item"
                style="text-decoration: none; margin-top: 20px; color: red; text-align: center;">
                <i class="fas fa-sign-out-alt"></i><span> Logout</span>
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
                    <img src="uploads/<?php echo $tweet['profile_picture'] ?: 'default.jpg'; ?>" alt="profile picture"
                        class="tweet-avatar">

                    <div class="tweet-content">
                        <div class="tweet-header">
                            <strong>
                                <a href="view_profile.php?id=<?php echo $tweet['user_id']; ?>" class="tweet-user">
                                    @<?php echo htmlspecialchars($tweet['username']); ?>
                                </a>
                            </strong>
                            <small class="tweet-handle"><?php echo $tweet['created_at']; ?></small>
                        </div>
                        <p class="tweet-text"><?php echo nl2br(htmlspecialchars($tweet['text'])); ?></p>


                       <!-- Replace the existing Comment Form and Like Logic section with this code: -->
<div style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">
    <!-- Like Logic -->
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
        <form method="POST" action="unlike_tweet.php" style="display:inline;">
            <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
            <button type="submit" style="
                background: none;
                border: none;
                color: #f91880;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 5px;
                font-size: 14px;
                padding: 6px 12px;
                transition: color 0.2s;
            ">
                <span style="font-size: 16px;">❤️</span> <?php echo $like_count; ?>
            </button>
        </form>
    <?php else: ?>
        <form method="POST" action="like_tweet.php" style="display:inline;">
            <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
            <button type="submit" style="
                background: none;
                border: none;
                color: #71767b;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 5px;
                font-size: 14px;
                padding: 6px 12px;
                transition: color 0.2s;
            ">
                <span style="font-size: 16px;">🤍</span> <?php echo $like_count; ?>
            </button>
        </form>
    <?php endif; ?>

    <!-- Comment Button -->
    <button type="button" onclick="document.getElementById('comment-form-<?php echo $tweet['id']; ?>').style.display='block'; this.style.display='none'" style="
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
<form method="POST" action="comment_tweet.php" id="comment-form-<?php echo $tweet['id']; ?>" style="margin-top:10px; display:none;">
    <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
    <textarea name="comment_text" placeholder="Write a comment..." required
        style="width:100%; height:60px; border:1px solid #e1e8ed; border-radius:10px; padding:10px; font-size:14px; resize:none; margin-bottom:8px;"></textarea>
    <div style="display: flex; gap: 10px;">
        <button type="submit"
            style="background-color:#1da1f2; color:white; border:none; padding:6px 12px; border-radius:30px; font-size:14px; cursor:pointer;">
            Post Comment
        </button>
        <button type="button" onclick="document.getElementById('comment-form-<?php echo $tweet['id']; ?>').style.display='none'; document.querySelector('button[onclick]').style.display='flex'"
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
                            SELECT comments.comment_text, comments.created_at, users.username, users.profile_picture
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
                                    <img src="uploads/<?php echo $comment['profile_picture'] ?: 'default.jpg'; ?>"
                                        alt="profile picture" class="comment-avatar">
                                    <strong class="comment-user">@<?php echo htmlspecialchars($comment['username']); ?></strong>
                                    <small class="comment-handle"><?php echo $comment['created_at']; ?></small>
                                    <p class="comment-text"><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </main>
    </div>
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>
</body>

</html>