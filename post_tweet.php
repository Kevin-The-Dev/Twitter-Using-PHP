<?php
session_start();
include 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tweet_text = trim($_POST['tweet_text']);
    $user_id = $_SESSION['user_id'];

    // Fetch the user's username
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();

    // Insert tweet with username
    $stmt = $conn->prepare("INSERT INTO tweets (user_id, username, text) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $username, $tweet_text);

    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect back to the index page
        exit();
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>
