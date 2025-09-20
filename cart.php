<?php
session_start();
if (!isset($_SESSION['customerID'])) {
    die("Customer not logged in!");
}

$customerID = $_SESSION['customerID'];
?>
<?php
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

// Fetch cart items for the logged-in customer
$sql = "SELECT c.productID, p.productname, p.price, p.ProductImage, c.quantity 
        FROM cart AS c 
        JOIN product AS p ON c.productID = p.productID 
        WHERE c.customerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();

$cart = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['productID'];
        $cart[$product_id] = [
            'id' => $product_id,
            'name' => $row['productname'],
            'price' => $row['price'],
            'image' => $row['ProductImage'],
            'quantity' => $row['quantity']
        ];
    }
}
// Calculate cart totals
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Calculate tax and total
$tax = $subtotal * 0.08; // 8% tax rate
$shipping = 9.99;
$total = $subtotal + $tax + $shipping;
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOLT GEAR - Your Cart</title>
    
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
        
        .cart-icon {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            position: relative;
        }
        
        .cart-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #8A2BE2;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        
        /* Mobile menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Cart Page Styles */
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 20px 60px;
        }
        
        .cart-header {
            margin-bottom: 40px;
            text-align: center;
        }
        
        .cart-header h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }
        
        .cart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .cart-items {
            background-color: #121212;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .cart-item {
            display: flex;
            border-bottom: 1px solid #333;
            padding: 20px 0;
            position: relative;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 20px;
            background-color: #1a1a1a;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .item-price {
            color: #00FFFF;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .item-actions {
            display: flex;
            align-items: center;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            background-color: #1e1e1e;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .quantity-btn:hover {
            background-color: #333;
        }
        
        .quantity-input {
            width: 50px;
            height: 30px;
            text-align: center;
            background-color: #1e1e1e;
            border: none;
            color: white;
            margin: 0 5px;
            border-radius: 5px;
        }
        
        .remove-item {
            background: none;
            border: none;
            color: #ff5e5e;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            transition: color 0.3s;
        }
        
        .remove-item:hover {
            color: #ff3a3a;
        }
        
        .remove-icon {
            margin-right: 5px;
        }
        
        .item-subtotal {
            position: absolute;
            right: 0;
            top: 20px;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .cart-summary {
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
        
        .promo-code {
            margin: 20px 0;
        }
        
        .promo-code input {
            width: 100%;
            padding: 12px;
            background-color: #1e1e1e;
            border: 1px solid #333;
            border-radius: 5px;
            color: white;
            margin-bottom: 10px;
        }
        
        .apply-promo {
            width: 100%;
            padding: 12px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: 600;
        }
        
        .apply-promo:hover {
            background-color: #444;
        }
        
        .checkout-btn {
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
        
        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #cccccc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .continue-shopping:hover {
            color: #00FFFF;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        
        .empty-cart p {
            color: #cccccc;
            margin-bottom: 30px;
        }
        
        .shop-now-btn {
            display: inline-block;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .shop-now-btn:hover {
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
        @media (max-width: 992px) {
            .cart-grid {
                grid-template-columns: 1fr;
            }
            
            .cart-summary {
                position: static;
                margin-top: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
            
            nav {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background-color: #121212;
                transition: left 0.3s;
                z-index: 99;
            }
            
            nav.active {
                left: 0;
            }
            
            nav ul {
                flex-direction: column;
                padding: 2rem;
            }
            
            nav ul li {
                margin: 1rem 0;
            }
            
            .cart-item {
                flex-direction: column;
            }
            
            .item-image {
                width: 100%;
                height: 200px;
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .item-subtotal {
                position: static;
                margin-top: 15px;
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

/* Add these styles to your existing CSS */
nav ul li a.active {
    color: #00FFFF;
}

nav ul li a.active::after {
    width: 100%;
}

.logo a {
    text-decoration: none;
    display: flex;
    align-items: center;
    color: inherit;
}
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo">
            <a href="index.php" style="text-decoration: none; display: flex; align-items: center; color: inherit;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 60" width="100" height="60">
                    <!-- Lightning bolt element - aqua -->
                    <path d="M15 10 L30 10 L25 30 L40 30 L15 50 L20 35 L10 35 Z" fill="#00FFFF"/>
                    
                    <!-- Gear element - purple -->
                    <g transform="translate(70, 30)">
                        <!-- Outer gear -->
                        <path d="M0 -15 L3 -15 L5 -10 L10 -12 L12 -7 L8 -4 L12 0 L8 4 L12 7 L8 12 L4 10 L2 15 L0 15 L-2 15 L-4 10 L-8 12 L-12 7 L-8 4 L-12 0 L-8 -4 L-12 -7 L-8 -12 L-4 -10 L-3 -15 Z" fill="#8A2BE2"/>
                        <!-- Inner gear -->
                        <circle cx="0" cy="0" r="5" fill="#121212"/>
                        <circle cx="0" cy="0" r="2" fill="#8A2BE2"/>
                    </g>
                </svg>
                <h1>VOLT<span>GEAR</span></h1>
            </a>
        </div>
        
        <nav id="main-nav">
            <ul>
                <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About</a></li>
                <li><a href="ourservices.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'ourservices.php' ? 'active' : ''; ?>">Our Services</a></li>
                <li><a href="contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
            </ul>
        </nav>
        
        <div class="auth-buttons">
            <?php if (!isset($_SESSION['customerID'])): ?>
                <!-- Show login and signup buttons -->
                <a href="login.php" class="login-button">Login</a>
                <a href="signup.php" class="signup-button">Sign Up</a>
            <?php else: ?>
                <!-- Show logout button -->
                <form action="logout.php" method="post" style="display: inline;">
                    <button type="submit" class="logout-button">Logout</button>
                </form>
            <?php endif; ?>
        </div>
        
        <button class="mobile-menu-toggle" id="menu-toggle">☰</button>
    </div>
</header>

    <!-- Cart Section -->
    <section class="cart-container">
        <div class="cart-header">
            <h2>Your Shopping Cart</h2>
        </div>
        
        <?php if (count($cart) > 0): ?>
        <div class="cart-grid">
            <div class="cart-items">
                <?php foreach ($cart as $item): ?>
                <div class="cart-item">
                    <div class="item-image">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="item-details">
                        <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <div class="item-price">₹<?php echo number_format($item['price'], 2); ?></div>
                        <div class="item-actions">
    <div class="quantity-control">
        <form method="POST" action="update_cart.php">
            <button type="submit" name="action" value="decrease" class="quantity-btn">-</button>
            <input type="hidden" name="productID" value="<?php echo $item['id']; ?>">
            <input type="text" name="quantity" value="<?php echo $item['quantity']; ?>" class="quantity-input" readonly>
            <button type="submit" name="action" value="increase" class="quantity-btn">+</button>
        </form>
    </div>
    <form method="POST" action="update_cart.php">
        <input type="hidden" name="productID" value="<?php echo $item['id']; ?>">
        <button type="submit" name="action" value="remove" class="remove-item">
            Remove
        </button>
    </form>
</div>

                    </div>
                    <div class="item-subtotal">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <h3 class="summary-header">Order Summary</h3>
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-line">
                    <span>Tax (8%)</span>
                    <span>₹<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="summary-line">
                    <span>Shipping</span>
                    <span>₹<?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="promo-code">
                    <input type="text" placeholder="Promo Code">
                    <button class="apply-promo">Apply</button>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span class="amount">₹<?php echo number_format($total, 2); ?></span>
                </div>
                <button class="checkout-btn" onclick="window.location.href='checkout.php'">Proceed to Checkout</button>
                <a href="index.php" class="continue-shopping">Continue Shopping</a>
            </div>
        </div>
        <?php else: ?>
        <div class="empty-cart">
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="index.php" class="shop-now-btn">Start Shopping</a>
        </div>
        <?php endif; ?>
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
    <script src="">
    // Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.getElementById('main-nav');
    
    menuToggle.addEventListener('click', () => {
        mainNav.classList.toggle('active');
    });
    
    // Cart functionality
    const decreaseButtons = document.querySelectorAll('.decrease-qty');
    const increaseButtons = document.querySelectorAll('.increase-qty');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const removeButtons = document.querySelectorAll('.remove-item');
    
    // Handle quantity decrease
    decreaseButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-id');
            const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
            let value = parseInt(input.value);
            
            if (value > 1) {
                value--;
                input.value = value;
                updateCart(productId, value);
            }
        });
    });
    
    // Handle quantity increase
    increaseButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-id');
            const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
            let value = parseInt(input.value);
            
            if (value < 10) {
                value++;
                input.value = value;
                updateCart(productId, value);
            }
        });
    });
    
    // Handle direct quantity input
    quantityInputs.forEach(input => {
        input.addEventListener('change', () => {
            const productId = input.getAttribute('data-id');
            let value = parseInt(input.value);
            
            // Ensure value is within range
            if (value < 1) value = 1;
            if (value > 10) value = 10;
            
            input.value = value;
            updateCart(productId, value);
        });
    });
    
    // Handle remove item
    removeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-id');
            removeFromCart(productId);
        });
    });
    
    // Update cart function (AJAX)
    function updateCart(productId, quantity) {
        // In a real implementation, this would be an AJAX call to update the server
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity,
                action: 'update'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the UI
                updateCartUI(productId, quantity, data.price);
                updateOrderSummary(data.subtotal, data.tax, data.shipping, data.total);
            } else {
                alert('Failed to update cart. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error updating cart:', error);
        });
        
        // For demo purposes, update UI directly (remove in production)
        const priceElement = document.querySelector(`.cart-item:has(.quantity-input[data-id="${productId}"]) .item-price`);
        const subtotalElement = document.querySelector(`.cart-item:has(.quantity-input[data-id="${productId}"]) .item-subtotal`);
        
        if (priceElement && subtotalElement) {
            const price = parseFloat(priceElement.textContent.replace('$', ''));
            subtotalElement.textContent = '$' + (price * quantity).toFixed(2);
            
            // Update order summary (simplified for demo)
            recalculateOrderSummary();
        }
    }
    
    // Remove from cart function (AJAX)
    function removeFromCart(productId) {
        // In a real implementation, this would be an AJAX call to update the server
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                action: 'remove'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove item from UI
                const cartItem = document.querySelector(`.cart-item:has(.remove-item[data-id="${productId}"])`);
                if (cartItem) {
                    cartItem.remove();
                    
                    // Update cart count
                    const cartCount = document.querySelector('.cart-count');
                    cartCount.textContent = data.cartCount;
                    
                    // Update order summary
                    updateOrderSummary(data.subtotal, data.tax, data.shipping, data.total);
                    
                    // Check if cart is empty
                    if (data.cartCount === 0) {
                        displayEmptyCart();
                    }
                }
            } else {
                alert('Failed to remove item. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error removing item:', error);
        });
        
        // For demo purposes (remove in production)
        const cartItem = document.querySelector(`.cart-item:has(.remove-item[data-id="${productId}"])`);
        if (cartItem) {
            cartItem.remove();
            recalculateOrderSummary();
            
            // Update cart count
            const cartItems = document.querySelectorAll('.cart-item');
            const cartCount = document.querySelector('.cart-count');
            cartCount.textContent = cartItems.length;
            
            // Check if cart is empty
            if (cartItems.length === 0) {
                displayEmptyCart();
            }
        }
    }
    
    // Update UI functions
    function updateCartUI(productId, quantity, price) {
        const subtotalElement = document.querySelector(`.cart-item:has(.quantity-input[data-id="${productId}"]) .item-subtotal`);
        if (subtotalElement && price) {
            subtotalElement.textContent = '$' + (price * quantity).toFixed(2);
        }
    }
    
    function updateOrderSummary(subtotal, tax, shipping, total) {
        const subtotalElement = document.querySelector('.summary-line:nth-child(1) span:last-child');
        const taxElement = document.querySelector('.summary-line:nth-child(2) span:last-child');
        const shippingElement = document.querySelector('.summary-line:nth-child(3) span:last-child');
        const totalElement = document.querySelector('.summary-total .amount');
        
        if (subtotalElement && taxElement && shippingElement && totalElement) {
            subtotalElement.textContent = '$' + subtotal.toFixed(2);
            taxElement.textContent = '$' + tax.toFixed(2);
            shippingElement.textContent = '$' + shipping.toFixed(2);
            totalElement.textContent = '$' + total.toFixed(2);
        }
    }
    
    // Recalculate order summary based on current cart items (for demo)
    function recalculateOrderSummary() {
        let subtotal = 0;
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach(item => {
            const priceText = item.querySelector('.item-price').textContent;
            const price = parseFloat(priceText.replace('$', ''));
            const quantity = parseInt(item.querySelector('.quantity-input').value);
            subtotal += price * quantity;
        });
        
        const tax = subtotal * 0.08;
        const shipping = cartItems.length > 0 ? 9.99 : 0;
        const total = subtotal + tax + shipping;
        
        updateOrderSummary(subtotal, tax, shipping, total);
    }
    
    // Display empty cart message
    function displayEmptyCart() {
        const cartGrid = document.querySelector('.cart-grid');
        const cartContainer = document.querySelector('.cart-container');
        
        if (cartGrid) {
            cartGrid.remove();
            
            const emptyCartHTML = `
                <div class="empty-cart">
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added anything to your cart yet.</p>
                    <a href="index.php" class="shop-now-btn">Start Shopping</a>
                </div>
            `;
            
            cartContainer.insertAdjacentHTML('beforeend', emptyCartHTML);
        }
    }
    
    // Handle promo code application
    const promoButton = document.querySelector('.apply-promo');
    if (promoButton) {
        promoButton.addEventListener('click', () => {
            const promoInput = document.querySelector('.promo-code input');
            const promoCode = promoInput.value.trim();
            
            if (promoCode) {
                // In a real app, send AJAX request to validate promo code
                alert('Promo code applied successfully!');
                
                // For demo: apply 10% discount
                let subtotalText = document.querySelector('.summary-line:nth-child(1) span:last-child').textContent;
                let subtotal = parseFloat(subtotalText.replace('$', ''));
                let tax = subtotal * 0.08;
                let shipping = 9.99;
                let discount = subtotal * 0.1;
                let total = subtotal + tax + shipping - discount;
                
                // Add discount line if not exists
                const discountLine = document.querySelector('.summary-line.discount');
                if (!discountLine) {
                    const discountHTML = `
                        <div class="summary-line discount" style="color: #00FFFF;">
                            <span>Discount (10%)</span>
                            <span>-$${discount.toFixed(2)}</span>
                        </div>
                    `;
                    
                    const summaryTotal = document.querySelector('.summary-total');
                    summaryTotal.insertAdjacentHTML('beforebegin', discountHTML);
                }
                
                updateOrderSummary(subtotal, tax, shipping, total);
            } else {
                alert('Please enter a valid promo code.');
            }
        });
    }
    
    // Handle checkout button
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            // Redirect to checkout page
            window.location.href = 'checkout.php';
        });
    }
});
</script>
</body>
</html>