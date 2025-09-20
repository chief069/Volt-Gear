<?php
// add_to_cart.php
session_start();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the required data is received
if (isset($_POST['product_id']) && isset($_POST['product_name']) && isset($_POST['product_price'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = floatval($_POST['product_price']);
    
    // Create a cart item
    $cartItem = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'quantity' => 1
    ];
    
    // Check if the product is already in the cart
    $found = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            // Increment quantity if product already exists
            $_SESSION['cart'][$key]['quantity']++;
            $found = true;
            break;
        }
    }
    
    // Add new item if not found
    if (!$found) {
        $_SESSION['cart'][] = $cartItem;
    }
    
    // Count total items in cart (considering quantities)
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'cart_count' => $total_items
    ]);
    
} else {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Missing required data'
    ]);
}