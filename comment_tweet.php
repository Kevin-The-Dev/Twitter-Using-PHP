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
        $stmt = $conn->prepare("INSERT INTO comments (user_id, tweet_id, comment_text, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $user_id, $tweet_id, $comment_text);
        $stmt->execute();
    }
}

header("Location: home.php");

exit();
