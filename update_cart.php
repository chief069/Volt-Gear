<?php
session_start();
if (!isset($_SESSION['customerID'])) {
    die("Customer not logged in!");
}

$customerID = $_SESSION['customerID'];
$productID = $_POST['productID'];
$action = $_POST['action'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$db = "voltgear";

// Create connection (use the same variable names as defined above)
$conn = new mysqli($servername, $username, $password, $db,3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle actions: increase, decrease, or remove
if ($action === "increase") {
    // Increase the quantity of the product
    $sql = "UPDATE cart SET quantity = quantity + 1 WHERE customerID = ? AND productID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customerID, $productID);
    $stmt->execute();
} elseif ($action === "decrease") {
    // Decrease the quantity of the product (minimum is 1)
    $sql = "UPDATE cart SET quantity = GREATEST(quantity - 1, 1) WHERE customerID = ? AND productID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customerID, $productID);
    $stmt->execute();
} elseif ($action === "remove") {
    // Remove the product from the cart
    $sql = "DELETE FROM cart WHERE customerID = ? AND productID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customerID, $productID);
    $stmt->execute();
}

$stmt->close();
$conn->close();

// Redirect back to the cart page
header("Location: cart.php");
exit();
?>
