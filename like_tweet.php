<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$tweet_id = $_POST['tweet_id'];

// Get the username of the user who liked
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($liker_username);
$stmt->fetch();
$stmt->close();

// Get the username of the tweet owner
$stmt2 = $conn->prepare("SELECT users.username 
                         FROM tweets 
                         JOIN users ON tweets.user_id = users.id 
                         WHERE tweets.id = ?");
$stmt2->bind_param("i", $tweet_id);
$stmt2->execute();
$stmt2->bind_result($tweet_owner_username);
$stmt2->fetch();
$stmt2->close();

// Check if the user already liked the tweet
$stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND tweet_id = ?");
$stmt->bind_param("ii", $user_id, $tweet_id);
$stmt->execute();
$like_result = $stmt->get_result();

if ($like_result->num_rows > 0) {
    // User has already liked, so unlike the tweet
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND tweet_id = ?");
    $stmt->bind_param("ii", $user_id, $tweet_id);
    $stmt->execute();
    $liked = false;
} else {
    // User has not liked yet, so like the tweet with usernames
    $stmt = $conn->prepare("INSERT INTO likes (user_id, tweet_id, liker_username, tweet_owner_username) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $tweet_id, $liker_username, $tweet_owner_username);
    $stmt->execute();
    $liked = true;
}

// Get the new like count
$like_count_stmt = $conn->prepare("SELECT COUNT(*) AS total_likes FROM likes WHERE tweet_id = ?");
$like_count_stmt->bind_param("i", $tweet_id);
$like_count_stmt->execute();
$like_count = $like_count_stmt->get_result()->fetch_assoc()['total_likes'];

// Respond with the new like count and like status
echo json_encode(['success' => true, 'new_like_count' => $like_count, 'liked' => $liked]);
?>
