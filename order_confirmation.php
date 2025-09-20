<?php
session_start();
if (!isset($_SESSION['customerID'])) {
    die("Customer not logged in!");
}

$customerID = $_SESSION['customerID'];

// Check if we have order information in session
if (!isset($_SESSION['order_info'])) {
    // Redirect back to cart if no order info
    header("Location: cart.php");
    exit();
}

// Retrieve order information from session
$order_info = $_SESSION['order_info'];
$ordermasterID = $order_info['ordermasterID'];
$firstName = $order_info['fname'];
$lastName = $order_info['lname'];
$email = $order_info['email'];
$phone = $order_info['phoneno'];
$paymentMethod = $order_info['paymentway'];
$total = $order_info['total'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$db = "voltgear";

// Create connection
$conn = new mysqli($servername, $username, $password, $db, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order details
$sql = "SELECT orderDetailId, p.productname, p.price, p.ProductImage, quantity 
        FROM OrderDetail AS od 
        JOIN product AS p ON od.productID = p.productID 
        WHERE ordermasterID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ordermasterID);
$stmt->execute();
$result = $stmt->get_result();

$orderItems = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderItems[] = $row;
    }
}

// Fetch payment info
$sql = "SELECT payId, paymentway FROM Payment WHERE ordermasterID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ordermasterID);
$stmt->execute();
$paymentResult = $stmt->get_result();
$paymentInfo = $paymentResult->fetch_assoc();

$stmt->close();
$conn->close();

// Clear cart session information
// unset($_SESSION['order_info']); // Uncomment this in production to prevent refreshing the success page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOLT GEAR - Order Success</title>
    
    <style>
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
        }
        
        /* Header styles */
        header {
            background-color: #121212;
            padding: 1rem 5%;
            position: fixed;
            width: 100%;
            z-index: 100;
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
        
        .logo h1 {
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
        
        /* Success Page Styles */
        .success-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 120px 20px 60px;
        }
        
        .success-header {
            margin-bottom: 40px;
            text-align: center;
        }
        
        .success-header h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }
        
        .success-header p {
            color: #cccccc;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        
        .success-content {
            background-color: #121212;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }
        
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #333;
        }
        
        .info-group h4 {
            font-size: 1rem;
            color: #999;
            margin-bottom: 8px;
        }
        
        .info-group p {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .order-id {
            color: #00FFFF;
        }
        
        .payment-method {
            text-transform: capitalize;
            padding: 5px 10px;
            background-color: #1e1e1e;
            border-radius: 20px;
            display: inline-block;
        }
        
        .payment-method.cash {
            background-color: #4CAF50;
            color: white;
        }
        
        .payment-method.card {
            background-color: #2196F3;
            color: white;
        }
        
        .payment-method.online {
            background-color: #8A2BE2;
            color: white;
        }
        
        .order-items {
            margin-top: 30px;
        }
        
        .order-items h3 {
            margin-bottom: 20px;
            font-size: 1.3rem;
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
        }
        
        .item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }
        
        .order-item {
            background-color: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .order-item:hover {
            transform: translateY(-5px);
        }
        
        .item-image {
            height: 150px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0f0f0f;
        }
        
        .item-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .item-details {
            padding: 15px;
        }
        
        .item-name {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 8px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        
        .item-price {
            color: #00FFFF;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .item-quantity {
            font-size: 0.9rem;
            color: #999;
        }
        
        .order-summary {
            background-color: #1a1a1a;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .summary-header {
            font-size: 1.3rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .total-label {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #00FFFF;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }
        
        .action-button {
            padding: 12px 30px;
            border-radius: 5px;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .continue-shopping {
            background-color: #333;
            color: white;
            text-decoration: none;
        }
        
        .continue-shopping:hover {
            background-color: #444;
        }
        
        .view-orders {
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            color: white;
            text-decoration: none;
        }
        
        .view-orders:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        /* Footer */
        footer {
            background-color: #121212;
            padding: 5rem 5% 2rem;
            margin-top: 50px;
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-top {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .footer-column h4 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-column h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 0.8rem;
        }
        
        .footer-column ul li a {
            color: #cccccc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: #00FFFF;
        }
        
        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .copyright {
            color: #999;
            font-size: 0.9rem;
        }
        
        .social-icons {
            display: flex;
        }
        
        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #1e1e1e;
            border-radius: 50%;
            margin-left: 1rem;
            transition: background-color 0.3s;
        }
        
        .social-icon:hover {
            background-color: #8A2BE2;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .order-info {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .action-button {
                width: 100%;
                text-align: center;
            }
            
            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
            
            .copyright {
                margin-bottom: 1rem;
            }
            
            .social-icons {
                justify-content: center;
            }
            
            .social-icon {
                margin: 0 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>VOLT GEAR</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['customerID'])): ?>
                    <a href="logout.php" class="logout-button">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="login-button">Login</a>
                    <a href="signup.php" class="signup-button">Signup</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Success Section -->
    <section class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h2>Order Placed Successfully!</h2>
            <p>Thank you for your purchase. Your order has been confirmed and will be shipped soon.</p>
        </div>
        
        <div class="success-content">
            <div class="order-info">
                <div class="info-group">
                    <h4>Order ID</h4>
                    <p class="order-id">#<?php echo sprintf('%06d', $ordermasterID); ?></p>
                </div>
                <div class="info-group">
                    <h4>Customer Name</h4>
                    <p><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></p>
                </div>
                <div class="info-group">
                    <h4>Email</h4>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>
                <div class="info-group">
                    <h4>Phone</h4>
                    <p><?php echo htmlspecialchars($phone); ?></p>
                </div>
                <div class="info-group">
                    <h4>Payment Method</h4>
                    <p><span class="payment-method <?php echo strtolower($paymentMethod); ?>"><?php echo htmlspecialchars($paymentMethod); ?></span></p>
                </div>
                <div class="info-group">
                    <h4>Order Date</h4>
                    <p><?php echo date('F j, Y'); ?></p>
                </div>
            </div>
            
            <?php if (!empty($orderItems)): ?>
            <div class="order-items">
                <h3>Order Items</h3>
                <div class="item-grid">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="order-item">
                        <div class="item-image">
                            <img src="<?php echo htmlspecialchars($item['ProductImage']); ?>" alt="<?php echo htmlspecialchars($item['productname']); ?>">
                        </div>
                        <div class="item-details">
                            <h4 class="item-name"><?php echo htmlspecialchars($item['productname']); ?></h4>
                            <div class="item-price">₹<?php echo number_format($item['price'], 2); ?></div>
                            <div class="item-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="order-summary">
                <h3 class="summary-header">Order Summary</h3>
                <div class="summary-total">
                    <div class="total-label">Total Amount</div>
                    <div class="total-amount">₹<?php echo number_format($total, 2); ?></div>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="index.php" class="action-button continue-shopping">Continue Shopping</a>
            <a href="my-orders.php" class="action-button view-orders">View My Orders</a>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-top">
                <div class="footer-column">
                    <h4>Products</h4>
                    <ul>
                        <li><a href="keyboard.html">Keyboards</a></li>
                        <li><a href="#">Mice</a></li>
                        <li><a href="#">Headsets</a></li>
                        <li><a href="#">Mousepads</a></li>
                        <li><a href="#">Chairs</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Press</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Warranty</a></li>
                        <li><a href="#">Returns</a></li>
                        <li><a href="#">Order Status</a></li>
                        <li><a href="#">FAQs</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="privacy.html">Terms of Service</a></li>
                        <li><a href="privacy.html">Privacy Policy</a></li>
                        <li><a href="privacy.html">Shipping Policy</a></li>
                        <li><a href="privacy.html">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="copyright">© 2025 VOLT GEAR. All Rights Reserved.</div>
                <div class="social-icons">
                    <a href="#" class="social-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                        </svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                            <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>