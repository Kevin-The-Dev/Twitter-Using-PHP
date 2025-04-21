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

    // Insert tweet into the database
    $stmt = $conn->prepare("INSERT INTO tweets (user_id, text) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $tweet_text);

    if ($stmt->execute()) {
        header("Location: home.php"); // Redirect back to the home page
        exit();
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>
