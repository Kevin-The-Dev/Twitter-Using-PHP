<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the profile ID (the user to follow/unfollow) is set
if (isset($_POST['profile_id'])) {
    $profile_id = $_POST['profile_id'];

    // Check if the user is trying to follow or unfollow
    $query = "SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $profile_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // The user is already following, so we unfollow them
        $delete_query = "DELETE FROM followers WHERE follower_id = ? AND followed_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ii", $user_id, $profile_id);
        $delete_stmt->execute();
        header("Location: view_profile.php?id=$profile_id"); // Redirect to profile page after unfollowing
    } else {
        // The user is not following, so we follow them
        $insert_query = "INSERT INTO followers (follower_id, followed_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ii", $user_id, $profile_id);
        $insert_stmt->execute();
        header("Location: view_profile.php?id=$profile_id"); // Redirect to profile page after following
    }
} else {
    // If no profile_id is provided, redirect to home page or show an error
    header("Location: home.php");
    exit();
}
?>
