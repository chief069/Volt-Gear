<?php
// Start the session
session_start(); 

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['customerID']); 

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$db = "voltgear";

// Create connection
$conn = new mysqli($servername, $username, $password, $db, 3307);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch cart items for the logged-in user
$cartItems = [];
if ($isLoggedIn) {
    $userId = $_SESSION['customerID'];
    $sql = "SELECT * FROM cart WHERE customerId = '$userId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cartItems[] = $row;
        }
    }
}


$sql = "SELECT productID, productname, price, ProductImage,catID,productdet FROM product WHERE catID = 3";
$result = $conn->query($sql);

// Handle add to cart functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if ($isLoggedIn) {
        $productId = $_POST['productID'];
        $quantity = $_POST['quantity'] ?? 1; // Default to 1 if quantity not set
        
        // Check if product already exists in cart
        $checkSql = "SELECT * FROM cart 
                    WHERE customerId = ? AND productId = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("ii", $_SESSION['customerID'], $productId);
        $stmt->execute();
        $existingItem = $stmt->get_result()->fetch_assoc();

        if ($existingItem) {
            // Update quantity if product exists
            $newQuantity = $existingItem['Quantity'] + $quantity;
            $updateSql = "UPDATE cart SET Quantity = ? 
                         WHERE cartid = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("ii", $newQuantity, $existingItem['cartid']);
            $stmt->execute();
        } else {
            // Insert new cart item
            $insertSql = "INSERT INTO cart (customerId, productId, quantity)
                         VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("iii", $_SESSION['customerID'], $productId, $quantity);
            $stmt->execute();
        }
        
        // Redirect to prevent form resubmission
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        // Redirect to login if not logged in
        header("Location: login.php");
        exit();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productId'])) {
    $productId = $_POST['productId'];
    
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT productdet FROM product WHERE productID = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Return the full product description
        echo $row['productdet'];
    } else {
        echo "Product description not found.";
    }

    
    // Close statement and connection
    $stmt->close();
    $conn->close();
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> VOLT GEAR - Gaming Headphones </title>
    
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

        nav ul li a:hover, nav ul li a.active {
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

        nav ul li a:hover::after, nav ul li a.active::after {
            width: 100%;
        }

        /* Authentication buttons */
        .auth-buttons {
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }

        .login-button, .signup-button, .logout-button {
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
            background-color: #ff5e5e;
            color: white;
            border: none;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #ff3a3a;
        }

        .cart-icon {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            position: relative;
            text-decoration: none;
            display: inline-block;
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

        /* Mobile menu toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Category banner */
        .category-banner {
    padding: 150px 5% 60px;
    background: radial-gradient(circle at 70% 30%, rgba(138, 43, 226, 0.2), transparent 60%),
                        radial-gradient(circle at 30% 70%, rgba(0, 255, 255, 0.2), transparent 60%);
    background-size: cover;
    text-align: center;
}
        
        .category-banner h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .category-banner p {
            max-width: 800px;
            margin: 0 auto;
            color: #cccccc;
        }
        
        /* Filters section */
        .filters {
            padding: 2rem 5%;
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            margin: 0.5rem 0;
        }
        
        .filter-label {
            margin-right: 1rem;
            font-weight: 600;
        }
        
        .filter-select {
            padding: 0.5rem 1rem;
            background-color: #1e1e1e;
            color: white;
            border: 1px solid #333;
            border-radius: 5px;
            outline: none;
        }
        
        .view-toggle {
            display: flex;
            align-items: center;
        }
        
        .view-button {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            margin-left: 1rem;
            transition: color 0.3s;
        }
        
        .view-button.active, .view-button:hover {
            color: #00FFFF;
        }
        
        /* Products section */
        .products-section {
            padding: 2rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .product-card {
            background-color: #121212;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 255, 0.15);
        }
        
        .product-image {
            height: 250px;
            overflow: hidden;
            position: relative;
            background-color: #1a1a1a;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .product-image img {
            max-width: 110%;
            max-height: 110%;
            object-fit: contain;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #8A2BE2;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .product-badge.sale {
            background-color: #ff5e5e;
        }
        
        .product-badge.new {
            background-color: #00FFFF;
            color: #121212;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-name {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }
        
        .product-category {
            color: #00FFFF;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .product-description {
            color: #cccccc;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        
        .product-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .product-price {
            font-size: 1.3rem;
            font-weight: bold;
        }
        
        .add-to-cart {
            background-color: #8A2BE2;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .add-to-cart:hover {
            background-color: #7A1BD2;
        }
        
        /* Newsletter section */
        .newsletter {
            padding: 5rem 5%;
            background: linear-gradient(135deg, rgba(0, 255, 255, 0.1), rgba(138, 43, 226, 0.1));
        }
        
        .newsletter-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        
        .newsletter h3 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .newsletter p {
            color: #cccccc;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .newsletter-form input {
            flex: 1;
            padding: 1rem 1.5rem;
            border: none;
            background-color: #121212;
            color: white;
            border-radius: 30px 0 0 30px;
            font-size: 1rem;
        }
        
        .newsletter-form button {
            background-color: #8A2BE2;
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 0 30px 30px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .newsletter-form button:hover {
            background-color: #7A1BD2;
        }
        
        /* Footer */
        footer {
            background-color: #121212;
            padding: 5rem 5% 2rem;
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
        
        /* Mobile menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .filters {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .view-toggle {
                margin-top: 1rem;
                align-self: flex-end;
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
            
            .category-banner h2 {
                font-size: 2.5rem;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
            
            .newsletter-form input {
                border-radius: 30px;
                margin-bottom: 1rem;
            }
            
            .newsletter-form button {
                border-radius: 30px;
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="ourservices.php">Our Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            
            <div class="auth-buttons">
                <?php if (!$isLoggedIn): ?>
                    <!-- Show login and signup buttons -->
                    <a href="login.php" class="login-button">Login</a>
                    <a href="signup.php" class="signup-button">Sign Up</a>
                <?php else: ?>
                    <!-- Show cart icon -->
                    <a href="cart.php" class="cart-icon">
                        🛒
                        <span class="cart-count"><?php echo count($cartItems); ?></span>
                    </a>
                    <!-- Show logout button -->
                    <form action="logout.php" method="post" style="display: inline; margin-left: 10px;">
                        <button type="submit" class="logout-button">Logout</button>
                    </form>
                <?php endif; ?>
            </div>
            
            <button class="mobile-menu-toggle" id="menu-toggle">☰</button>



            
        </div>
    </header>

    <!-- Category Banner -->
    <section class="category-banner">
        <h2>Gaming Headphones</h2>
        <p>Crush every game with immersive sound, crystal-clear chat, and all-day comfort — your ultimate gaming headset awaits.</p>
    </section>
    
    <!-- Filters Section -->
    <section class="filters">
        <div class="filter-options">
            <div class="filter-group">
                <span class="filter-label">Sort By:</span>
                <select class="filter-select">
                    <option value="popularity">Popularity</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="newest">Newest</option>
                </select>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">Switch Type:</span>
                <select class="filter-select">
                    <option value="all">All Types</option>
                    <option value="mechanical">Mechanical</option>
                    <option value="optical">Optical</option>
                    <option value="membrane">Membrane</option>
                </select>
            </div>
        </div>
        
        <div class="view-toggle">
            <button class="view-button active">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
            </button>
            <button class="view-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="21" y1="10" x2="3" y2="10"></line>
                    <line x1="21" y1="6" x2="3" y2="6"></line>
                    <line x1="21" y1="14" x2="3" y2="14"></line>
                    <line x1="21" y1="18" x2="3" y2="18"></line>
                </svg>
            </button>
        </div>
    </section>
    <div class="products-section">
    <div class="products-grid">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?= $row['ProductImage'] ?>" alt="<?= $row['productname'] ?>">
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?= $row['productname'] ?></h3>
                    <p class="product-description"><?= strlen($row['productdet']) > 100 ? substr($row['productdet'], 0, 100) . '...' : $row['productdet'] ?></p>
                    <div class="product-bottom">
                        <span class="product-price">₹<?= number_format($row['price'], 2) ?></span>
                        
                        <!-- Add to cart form -->
                        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                            <input type="hidden" name="productID" value="<?= $row['productID'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

    
    <!-- Newsletter section -->
    <section class="newsletter">
        <div class="newsletter-container">
            <h3>Join the VOLT GEAR Community</h3>
            <p>Subscribe to our newsletter for exclusive deals, product updates, and gaming tips.</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-top">
                <div class="footer-column">
                    <h4>Products</h4>
                    <ul>
                    <li><a href="keyboard.php">Keyboards</a></li>
                        <li><a href="mouse.php">Mice</a></li>
                        <li><a href="headsets.php">Headsets</a></li>
                        <li><a href="accessories.php">Accessories</a></li>
                        <li><a href="new-arrivals.php">New Arrivals</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="faq.php">FAQs</a></li>
                        <li><a href="shipping.php">Shipping Information</a></li>
                        <li><a href="returns.php">Returns & Exchanges</a></li>
                        <li><a href="warranty.php">Warranty</a></li>
                        <li><a href="contact.php">Contact Support</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="careers.php">Careers</a></li>
                        <li><a href="press.php">Press</a></li>
                        <li><a href="affiliates.php">Affiliate Program</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="terms.php">Terms of Service</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="cookie.php">Cookie Policy</a></li>
                        <li><a href="disclaimer.php">Disclaimer</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="copyright">
                    &copy; <?php echo date('Y'); ?> VOLT GEAR. All rights reserved.
                </div>
                
                <div class="social-icons">
                    <a href="#" class="social-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
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
                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                        </svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                            <rect x="2" y="9" width="4" height="12"></rect>
                            <circle cx="4" cy="4" r="2"></circle>
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
    
    <!-- JavaScript for mobile menu toggle -->
    <script src="volt-assistant.js">
      document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.getElementById('main-nav');
    
    menuToggle.addEventListener('click', function() {
        mainNav.classList.toggle('active');
        menuToggle.textContent = mainNav.classList.contains('active') ? '✕' : '☰';
    });
    
    // View toggle functionality
    const viewButtons = document.querySelectorAll('.view-button');
    const productsGrid = document.querySelector('.products-grid');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Toggle grid/list view
            if (Array.from(viewButtons).indexOf(this) === 0) {
                productsGrid.classList.remove('list-view');
            } else {
                productsGrid.classList.add('list-view');
            }
        });
    });
    
    // Add list view styles dynamically
    const style = document.createElement('style');
    style.textContent = `
        .products-grid.list-view {
            display: block;
        }
        
        .products-grid.list-view .product-card {
            display: grid;
            grid-template-columns: 300px 1fr;
            margin-bottom: 2rem;
        }
        
        .products-grid.list-view .product-image {
            height: 100%;
        }
        
        @media (max-width: 768px) {
            .products-grid.list-view .product-card {
                grid-template-columns: 1fr;
            }
        }
    `;
    document.head.appendChild(style);

    // Read More functionality
    // Get all product descriptions
    const productDescriptions = document.querySelectorAll('.product-description');
    
    // Store original full descriptions
    const originalDescriptions = {};
    
    productDescriptions.forEach((description, index) => {
        // Get the text content
        const text = description.textContent;
        
        // If the text contains ellipsis, it's truncated
        if (text.includes('...')) {
            // Store the original short text
            originalDescriptions[index] = {
                short: text,
                // We'll fetch the full description from the server later
                full: null,
                expanded: false
            };
            
            // Create read more button
            const readMoreBtn = document.createElement('button');
            readMoreBtn.textContent = 'Read More';
            readMoreBtn.className = 'read-more-btn';
            readMoreBtn.style.cssText = 'background: none; border: none; color: #00FFFF; cursor: pointer; font-size: 0.9rem; padding: 5px 0; text-decoration: underline; margin-top: 5px;';
            
            // Add event listener to the button
            readMoreBtn.addEventListener('click', function() {
                // If we already have the full description
                if (originalDescriptions[index].full) {
                    toggleDescription(description, readMoreBtn, index);
                } else {
                    // Get the product ID from the hidden input in the same product card
                    const productCard = description.closest('.product-card');
                    const productIdInput = productCard.querySelector('input[name="productID"]');
                    const productId = productIdInput.value;
                    
                    // Fetch the full description from the server
                    fetchFullDescription(productId).then(fullText => {
                        originalDescriptions[index].full = fullText;
                        toggleDescription(description, readMoreBtn, index);
                    });
                }
            });
            
            // Insert the button after the description
            description.parentNode.insertBefore(readMoreBtn, description.nextSibling);
        }
    });
    
    function toggleDescription(descElement, button, index) {
        // Toggle between short and full description
        if (originalDescriptions[index].expanded) {
            descElement.textContent = originalDescriptions[index].short;
            button.textContent = 'Read More';
        } else {
            descElement.textContent = originalDescriptions[index].full;
            button.textContent = 'Show Less';
        }
        
        // Toggle expanded state
        originalDescriptions[index].expanded = !originalDescriptions[index].expanded;
    }
    
    function fetchFullDescription(productId) {
        // Create a new FormData object
        const formData = new FormData();
        formData.append('productId', productId);
        
        // Return a promise - using current page URL instead of a separate file
        return fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .catch(error => {
            console.error('Error fetching product description:', error);
            return 'Could not load full description. Please try again later.';
        });
    }
    
    // Add styles for the read more functionality
    const style2 = document.createElement('style');
    style2.textContent = `
        .product-description {
            transition: max-height 0.3s ease;
        }
        
        .read-more-btn:hover {
            color: #8A2BE2;
        }
    `;
    document.head.appendChild(style2);
});
    </script>
</body>
</html>