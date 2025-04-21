<?php
// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$db   = "twitter_clone";

// Define upload directory
define('UPLOAD_DIR', __DIR__ . '/uploads/'); // Safer with absolute path

// Create a new MySQLi connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
