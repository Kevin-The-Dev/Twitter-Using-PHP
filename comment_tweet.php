<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tweet_id'], $_POST['comment_text'])) {
    $user_id = $_SESSION['user_id'];
    $tweet_id = $_POST['tweet_id'];
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text)) {

        // Get commenter's username
        $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($commenter_username);
        $stmt->fetch();
        $stmt->close();

        // Get tweet owner's user_id and username
        $stmt2 = $conn->prepare("SELECT users.username 
                                 FROM tweets 
                                 JOIN users ON tweets.user_id = users.id 
                                 WHERE tweets.id = ?");
        $stmt2->bind_param("i", $tweet_id);
        $stmt2->execute();
        $stmt2->bind_result($tweet_owner_username);
        $stmt2->fetch();
        $stmt2->close();

        // Insert comment with usernames
        $stmt3 = $conn->prepare("INSERT INTO comments (user_id, tweet_id, comment_text, created_at, commenter_username, tweet_owner_username)
                                 VALUES (?, ?, ?, NOW(), ?, ?)");
        $stmt3->bind_param("iisss", $user_id, $tweet_id, $comment_text, $commenter_username, $tweet_owner_username);
        $stmt3->execute();
        $stmt3->close();
    }
}

header("Location: index.php");
exit();
