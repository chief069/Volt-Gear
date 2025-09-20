<?php
session_start();
// This would be the code used in checkout.php when form is submitted

// Check if user is logged in
if (!isset($_SESSION['customerID'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$db = "voltgear";

// Create connection - removed the port specification
$conn = new mysqli($servername, $username, $password, $db, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Razorpay API keys
$razorpay_key_id = "rzp_test_WSNPUC733h1RPR"; // key ID
$razorpay_key_secret = "8py01fETzHCjfPDUWuQeNYOy"; // secret key

// Fetch cart items for this customer
$customerID = $_SESSION['customerID'];
$cart = [];
$subtotal = 0;
$tax = 0;
$shipping = 50; // Default shipping cost

// Get cart items with product details
$sql = "SELECT c.productID, c.quantity, p.productname, p.price, p.ProductImage 
        FROM cart c 
        JOIN product p ON c.productID = p.productID 
        WHERE c.customerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cart[] = $row;
    $subtotal += $row['price'] * $row['quantity'];
}

// Calculate tax and total
$tax = $subtotal * 0.08;
$total = $subtotal + $tax + $shipping;
$total_in_paisa = $total * 100; // Convert to paisa for Razorpay

// Get customer information
$sql = "SELECT customername, email FROM customer WHERE customerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$customerInfo = $stmt->get_result()->fetch_assoc();


// At the beginning of your file where you're setting up Razorpay
if (isset($_POST['proceed_to_payment']) && $_POST['paymentway'] == 'razorpay') {
    require 'vendor/autoload.php';
    
    $api = new Razorpay\Api\Api($razorpay_key_id, $razorpay_key_secret);
    
    // Create an order
    $orderData = [
        'receipt' => 'order_' . time(),
        'amount' => $total_in_paisa, // In paisa
        'currency' => 'INR',
        'notes' => [
            'customerID' => $customerID,
            'shipping' => json_encode([
                'name' => $_POST['fname'] . ' ' . $_POST['lname'],
                'email' => $_POST['email'],
                'phone' => $_POST['phoneno'],
                'address' => $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'] . ' - ' . $_POST['zipcode']
            ])
        ]
    ];
    
    try {
        $razorpayOrder = $api->order->create($orderData);
        
        // Save customer data in session to retrieve later
        $_SESSION['checkout_data'] = [
            'fname' => $_POST['fname'],
            'lname' => $_POST['lname'],
            'email' => $_POST['email'],
            'phoneno' => $_POST['phoneno'],
            'street' => $_POST['street'],
            'city' => $_POST['city'],
            'state' => $_POST['state'],
            'country' => $_POST['country'],
            'zipcode' => $_POST['zipcode'],
            'paymentway' => 'razorpay',
            'razorpay_order_id' => $razorpayOrder['id']
        ];
        
        // We'll handle the Razorpay modal below in JavaScript
    } catch (Exception $e) {
        $_SESSION['error'] = "Payment gateway error: " . $e->getMessage();
    }
}


// Handle Razorpay callback for successful payment
if (isset($_POST['razorpay_payment_id']) && isset($_POST['razorpay_order_id']) && isset($_POST['razorpay_signature'])) {
    require 'vendor/autoload.php';
    
    $api = new Razorpay\Api\Api($razorpay_key_id, $razorpay_key_secret);
    
    try {
        // Verify signature
        $api->utility->verifyPaymentSignature([
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_order_id' => $_POST['razorpay_order_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        ]);
        
        // Signature is valid, proceed with order creation
        $checkout_data = $_SESSION['checkout_data'];
        
        // Begin transaction
        $conn->begin_transaction();
        try {
            // 1. Insert into ordermaster
            $sql = "INSERT INTO ordermaster (customerID, fname, lname, email, phoneno, street, city, state, country, zipcode, orderat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssssss", $customerID, $checkout_data['fname'], $checkout_data['lname'], $checkout_data['email'], $checkout_data['phoneno'], $checkout_data['street'], $checkout_data['city'], $checkout_data['state'], $checkout_data['country'], $checkout_data['zipcode']);
            $stmt->execute();
            $ordermasterID = $conn->insert_id;

            // 2. Insert into orderdetail
            foreach ($cart as $item) {
                $sql = "INSERT INTO orderdetail (ordermasterID, productID, quantity) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iii", $ordermasterID, $item['productID'], $item['quantity']);
                $stmt->execute();
            }

            // 3. Insert into payment with Razorpay details
         
$sql = "INSERT INTO payment (ordermasterID, paymentway, payId) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$paymentway = 'razorpay';
$stmt->bind_param("iss", $ordermasterID, $paymentway, $_POST['razorpay_payment_id']);
$stmt->execute();

            // 4. Clear cart
            $sql = "DELETE FROM cart WHERE customerID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $customerID);
            $stmt->execute();

            $conn->commit();

            // Store order info in session for confirmation page
            $_SESSION['order_info'] = [
                'ordermasterID' => $ordermasterID,
                'fname' => $checkout_data['fname'],
                'lname' => $checkout_data['lname'],
                'email' => $checkout_data['email'],
                'phoneno' => $checkout_data['phoneno'],
                'street' => $checkout_data['street'],
                'city' => $checkout_data['city'],
                'state' => $checkout_data['state'],
                'country' => $checkout_data['country'],
                'zipcode' => $checkout_data['zipcode'],
                'paymentway' => 'razorpay',
                'payment_id' => $_POST['razorpay_payment_id'],
                'total' => $total
            ];

            // Clear checkout data
            unset($_SESSION['checkout_data']);

            // Redirect to confirmation page
            header("Location: order_confirmation.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "Order failed: " . $e->getMessage();
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Payment verification failed: " . $e->getMessage();
    }
}

// Regular checkout process for non-Razorpay methods (COD or other)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order']) && $_POST['paymentway'] != 'razorpay') {
    // Get and sanitize form data
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phoneno = filter_input(INPUT_POST, 'phoneno', FILTER_SANITIZE_STRING);
    $street = filter_input(INPUT_POST, 'street', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
    $zipcode = filter_input(INPUT_POST, 'zipcode', FILTER_SANITIZE_STRING);
    $paymentway = filter_input(INPUT_POST, 'paymentway', FILTER_SANITIZE_STRING);

    // Calculate total
    $subtotal = 0;
    $cart = [];
    $sql = "SELECT c.productID, c.quantity, p.price FROM cart c JOIN product p ON c.productID = p.productID WHERE c.customerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cart[] = $row;
        $subtotal += $row['price'] * $row['quantity'];
    }
    $tax = $subtotal * 0.08;
    $shipping = 50;
    $total = $subtotal + $tax + $shipping;

    // Begin transaction
    $conn->begin_transaction();
    try {
        // 1. Insert into ordermaster
        $sql = "INSERT INTO ordermaster (customerID, fname, lname, email, phoneno, street, city, state, country, zipcode, orderat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssssss", $customerID, $fname, $lname, $email, $phoneno, $street, $city, $state, $country, $zipcode);
        $stmt->execute();
        $ordermasterID = $conn->insert_id;

        // 2. Insert into orderdetail
        foreach ($cart as $item) {
            $sql = "INSERT INTO orderdetail (ordermasterID, productID, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $ordermasterID, $item['productID'], $item['quantity']);
            $stmt->execute();
        }

        // 3. Insert into payment
        $sql = "INSERT INTO payment (ordermasterID, paymentway) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $ordermasterID, $paymentway);
        $stmt->execute();

        // 4. Clear cart
        $sql = "DELETE FROM cart WHERE customerID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $customerID);
        $stmt->execute();

        $conn->commit();

        // Store order info in session for confirmation page
        $_SESSION['order_info'] = [
            'ordermasterID' => $ordermasterID,
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'phoneno' => $phoneno,
            'street' => $street,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'zipcode' => $zipcode,
            'paymentway' => $paymentway,
            'total' => $total
        ];

        // Redirect to confirmation page
        header("Location: order_confirmation.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Order failed: " . $e->getMessage();
        header("Location: checkout.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOLT GEAR - Checkout</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        /* Your existing styling from the original file remains the same */
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #0a0a0a;
            color: #ffffff;
            overflow-x: hidden;
            padding-top: 80px;
        }
        
        /* Header styles */
        header {
            background-color: #121212;
            padding: 1rem 5%;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.2);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo svg {
            height: 50px;
            width: auto;
        }
        
        .logo h1 {
            margin-left: 15px;
            font-size: 1.8rem;
        }
        
        .logo h1 span {
            color: #8A2BE2;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin: 0 1rem;
        }
        
        nav ul li a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: color 0.3s;
            padding: 0.5rem 0;
            position: relative;
        }
        
        nav ul li a:hover {
            color: #00FFFF;
        }
        
        nav ul li a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #00FFFF;
            transition: width 0.3s;
        }
        
        nav ul li a:hover::after {
            width: 100%;
        }
        
        /* Authentication buttons */
        .auth-buttons {
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }

        .login-button, .signup-button {
            padding: 0.5rem 1rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border-radius: 5px;
            margin-left: 0.8rem;
        }

        .login-button {
            color: #00FFFF;
            border: 1px solid #00FFFF;
        }

        .login-button:hover {
            background-color: rgba(0, 255, 255, 0.1);
        }

        .signup-button {
            background-color: #8A2BE2;
            color: white;
        }

        .signup-button:hover {
            background-color: #7A1BD2;
            transform: translateY(-2px);
        }
        
        .logout-button {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            background-color: #ff5e5e;
            transition: all 0.3s;
        }

        .logout-button:hover {
            background-color: #ff3a3a;
        }

        /* Checkout Section Styles */
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px 60px;
        }
        
        .checkout-header {
            margin-bottom: 40px;
            text-align: center;
        }
        
        .checkout-header h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 30px;
        }
        
        /* Form styles */
        .checkout-form {
            background-color: #121212;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
            color: #00FFFF;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            background-color: #1e1e1e;
            border: 1px solid #333;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            border-color: #00FFFF;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 255, 255, 0.2);
        }
        
        /* Payment method styles */
        .payment-methods {
            margin-top: 20px;
        }
        
        .payment-method {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #333;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .payment-method:hover {
            border-color: #00FFFF;
            background-color: rgba(0, 255, 255, 0.05);
        }
        
        .payment-method.selected {
            border-color: #00FFFF;
            background-color: rgba(0, 255, 255, 0.1);
        }
        
        .payment-method input[type="radio"] {
            margin-right: 15px;
        }
        
        .payment-icon {
            margin-right: 15px;
            font-size: 1.5rem;
            width: 30px;
            text-align: center;
        }
        
        .payment-details {
            flex: 1;
        }
        
        .payment-method-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .payment-method-description {
            font-size: 0.9rem;
            color: #aaa;
        }
        
        /* Order summary */
        .order-summary {
            background-color: #121212;
            border-radius: 10px;
            padding: 25px;
            position: sticky;
            top: 100px;
            height: fit-content;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .summary-header {
            font-size: 1.5rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
        }
        
        .summary-items {
            margin-bottom: 20px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #222;
        }
        
        .item-info {
            display: flex;
            align-items: center;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 15px;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .item-quantity {
            font-size: 0.9rem;
            color: #aaa;
        }
        
        .item-price {
            font-weight: 600;
            color: #00FFFF;
        }
        
        .summary-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #333;
            font-size: 1.3rem;
            font-weight: bold;
        }
        
        .summary-total .amount {
            color: #00FFFF;
        }
        
        .place-order-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .place-order-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .privacy-notice {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #aaa;
            text-align: center;
        }
        
        .back-to-cart {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #cccccc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .back-to-cart:hover {
            color: #00FFFF;
        }
        
        /* Steps indicator */
        .checkout-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100px;
            position: relative;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            background-color: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .step.active .step-number {
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
        }
        
        .step.completed .step-number {
            background-color: #00FFFF;
        }
        
        .step-title {
            font-size: 0.9rem;
            text-align: center;
        }
        
        .step-connector {
            position: absolute;
            height: 2px;
            background-color: #333;
            top: 15px;
            width: 100%;
            left: 50%;
            z-index: 0;
        }
        
        .step:first-child .step-connector {
            width: 50%;
            left: 50%;
        }
        
        .step:last-child .step-connector {
            width: 50%;
            right: 50%;
        }
        
        .step.active .step-connector,
        .step.completed .step-connector {
            background-color: #00FFFF;
        }
        
        /* Error message */
        .error-message {
            background-color: rgba(255, 0, 0, 0.1);
            border-left: 4px solid #ff3a3a;
            padding: 15px;
            margin: 20px 0;
            color: #ff6b6b;
            border-radius: 5px;
        }
        
        /* Footer styles remain the same */
        footer {
            background-color: #121212;
            padding: 5rem 5% 2rem;
            margin-top: 50px;
        }
        
        /* Add specific styles for Razorpay button */
        .razorpay-payment-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, #3395FF, #22BCEF);
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .razorpay-payment-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        /* Responsive styles and footer styles remain the same */
        @media (max-width: 992px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            
            .order-summary {
                position: static;
                margin-top: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .checkout-steps {
                flex-wrap: wrap;
            }
            
            .step {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    

    <!-- Checkout Section -->
    <section class="checkout-container">
        <div class="checkout-header">
            <h2>Checkout</h2>
        </div>
        
        <div class="checkout-steps">
            <div class="step completed">
                <div class="step-number">1</div>
                <div class="step-title">Cart</div>
                <div class="step-connector"></div>
            </div>
            <div class="step active">
                <div class="step-number">2</div>
                <div class="step-title">Checkout</div>
                <div class="step-connector"></div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-title">Confirmation</div>
            </div>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (count($cart) > 0): ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="checkout-form">
            <div class="checkout-grid">
                <div class="checkout-form">
                    <div class="form-section">
                        <h3>Shipping Information</h3>
                        <div class="form-row">
                        <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="fname" required value="<?php echo isset($customerInfo['customername']) ? explode(' ', $customerInfo['customername'])[0] : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="lname" required value="<?php echo isset($customerInfo['customername']) && strpos($customerInfo['customername'], ' ') !== false ? substr($customerInfo['customername'], strpos($customerInfo['customername'], ' ') + 1) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" required value="<?php echo isset($customerInfo['email']) ? $customerInfo['email'] : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phoneno" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="street">Street Address</label>
                            <input type="text" id="street" name="street" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            <div class="form-group">
                                <label for="state">State/Province</label>
                                <input type="text" id="state" name="state" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="India">India</option>
                                    <option value="United States">United States</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Australia">Australia</option>
                                    <!-- Add more countries as needed -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="zipcode">ZIP/Postal Code</label>
                                <input type="text" id="zipcode" name="zipcode" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Payment Method</h3>
                        <div class="payment-methods">
                            <div class="payment-method" onclick="selectPaymentMethod('razorpay')">
                                <input type="radio" id="razorpay" name="paymentway" value="razorpay" required>
                                <div class="payment-icon">💳</div>
                                <div class="payment-details">
                                    <div class="payment-method-title">Pay with Razorpay</div>
                                    <div class="payment-method-description">Secure online payment via credit/debit card, UPI, or bank transfer</div>
                                </div>
                            </div>
                            <div class="payment-method" onclick="selectPaymentMethod('cod')">
                                <input type="radio" id="cod" name="paymentway" value="cod" required>
                                <div class="payment-icon">💰</div>
                                <div class="payment-details">
                                    <div class="payment-method-title">Cash on Delivery</div>
                                    <div class="payment-method-description">Pay with cash when your order is delivered</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="razorpay-section" style="display: none;">
                        <button type="submit" name="proceed_to_payment" class="razorpay-payment-btn">Proceed to Pay</button>
                    </div>
                    
                    <div id="cod-section" style="display: none;">
                        <button type="submit" name="place_order" class="place-order-btn">Place Order</button>
                    </div>
                    
                    <div class="privacy-notice">
                        By placing your order, you agree to VOLT GEAR's privacy policy and terms of service.
                    </div>
                    
                    <a href="cart.php" class="back-to-cart">← Back to Cart</a>
                </div>
                
                <div class="order-summary">
                    <h3 class="summary-header">Order Summary</h3>
                    <div class="summary-items">
                        <?php foreach ($cart as $item): ?>
                        <div class="summary-item">
                            <div class="item-info">
                                <div class="item-image">
                                    <img src="<?php echo htmlspecialchars($item['ProductImage']); ?>" alt="<?php echo htmlspecialchars($item['productname']); ?>">
                                </div>
                                <div>
                                    <div class="item-name"><?php echo htmlspecialchars($item['productname']); ?></div>
                                    <div class="item-quantity">Qty: <?php echo $item['quantity']; ?></div>
                                </div>
                            </div>
                            <div class="item-price">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-line">
                        <span>Subtotal:</span>
                        <span>₹<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Tax (8%):</span>
                        <span>₹<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Shipping:</span>
                        <span>₹<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <div class="summary-total">
                        <span>Total:</span>
                        <span class="amount">₹<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
            </div>
        </form>
        <?php else: ?>
            <div class="empty-cart-message">
                <p>Your cart is empty. Please add some products before checkout.</p>
                <a href="catagories.php" class="continue-shopping-btn">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>VOLT GEAR</h3>
                <p>Powering your gaming experience with cutting-edge technology and unparalleled performance.</p>
            </div>
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="catagories.php">Shop</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contact Info</h3>
                <p>Email: support@voltgear.com</p>
                <p>Phone: +1 (555) 123-4567</p>
                <p>Address: 123 Gaming Street, Tech City</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 VOLT GEAR. All rights reserved.</p>
        </div>
    </footer>

    <!-- Razorpay script for handling the payment -->
    <script>
    // Function to select payment method
    function selectPaymentMethod(method) {
        // Remove selected class from all payment methods
        document.querySelectorAll('.payment-method').forEach(function(el) {
            el.classList.remove('selected');
        });
        
        // Add selected class to clicked payment method
        document.querySelector('#' + method).closest('.payment-method').classList.add('selected');
        
        // Check the radio button
        document.querySelector('#' + method).checked = true;
        
        // Show/hide the appropriate payment section
        if (method === 'razorpay') {
            document.getElementById('razorpay-section').style.display = 'block';
            document.getElementById('cod-section').style.display = 'none';
        } else if (method === 'cod') {
            document.getElementById('razorpay-section').style.display = 'none';
            document.getElementById('cod-section').style.display = 'block';
        }
    }
    
    <?php if (isset($razorpayOrder)): ?>
    document.addEventListener('DOMContentLoaded', function() {
        // Configure Razorpay checkout
        var options = {
            "key": "<?php echo $razorpay_key_id; ?>",
            "amount": "<?php echo $total_in_paisa; ?>",
            "currency": "INR",
            "name": "VOLT GEAR",
            "description": "Order Payment",
            "image": "your_logo_url.png", // Replace with your actual logo
            "order_id": "<?php echo $razorpayOrder['id']; ?>",
            "handler": function (response) {
                // Set form values from response
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                
                // Submit the form with payment data
                document.getElementById('razorpay-callback-form').submit();
            },
            "prefill": {
                "name": "<?php echo $_SESSION['checkout_data']['fname'] . ' ' . $_SESSION['checkout_data']['lname']; ?>",
                "email": "<?php echo $_SESSION['checkout_data']['email']; ?>",
                "contact": "<?php echo $_SESSION['checkout_data']['phoneno']; ?>"
            },
            "theme": {
                "color": "#8A2BE2"
            }
        };
        
        var rzp1 = new Razorpay(options);
        
        // Manually open Razorpay checkout
        rzp1.open();
        
        // Also allow the button to open it
        document.querySelector('.razorpay-payment-btn').addEventListener('click', function(e){
            e.preventDefault();
            rzp1.open();
        });
    });
    <?php endif; ?>
</script>

<!-- Always include this form, it will be used when Razorpay sends back data -->
<?php if (isset($razorpayOrder)): ?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="razorpay-callback-form">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="<?php echo $razorpayOrder['id']; ?>">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
</form>
<?php endif; ?>
</body>
</html>