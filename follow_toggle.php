<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the profile ID (user to follow/unfollow) is set
if (isset($_POST['profile_id'])) {
    $profile_id = $_POST['profile_id'];

    // Fetch follower's username
    $stmt1 = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $stmt1->bind_result($follower_username);
    $stmt1->fetch();
    $stmt1->close();

    // Fetch followed user's username
    $stmt2 = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt2->bind_param("i", $profile_id);
    $stmt2->execute();
    $stmt2->bind_result($followed_username);
    $stmt2->fetch();
    $stmt2->close();

    // Check if already following
    $query = "SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $profile_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Already following, so unfollow
        $delete_query = "DELETE FROM followers WHERE follower_id = ? AND followed_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ii", $user_id, $profile_id);
        $delete_stmt->execute();
    } else {
        // Not following yet, so follow and store usernames
        $insert_query = "INSERT INTO followers (follower_id, followed_id, follower_username, followed_username) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iiss", $user_id, $profile_id, $follower_username, $followed_username);
        $insert_stmt->execute();
    }

    header("Location: view_profile.php?id=$profile_id");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
