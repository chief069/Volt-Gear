<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "voltgear";

// Create connection (use the same variable names as defined above)
$conn = new mysqli($servername, $username, $password, $db,3307);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: Set charset to avoid issues with encoding
$conn->set_charset("utf8");

// Optional: echo success message (for development only, remove in production)
// echo "Connected successfully";
?>
