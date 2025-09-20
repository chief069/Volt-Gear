<?php
// Rename your file to index.php instead of index.html
session_start(); // Start the session

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['customerID']); // Assuming 'user_id' is set in the session after login

// Database credentials (place this at the top of your file, right after <?php)
$servername = "localhost";
$username = "root";
$password = "";
$db = "voltgear";

// Create connection (use the same variable names as defined above)
$conn = new mysqli($servername, $username, $password, $db,3307);
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
?>
<?php
// Handle add to cart functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if ($isLoggedIn) {
        $productId = $_POST['product_id'];
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
        } else {
            // Insert new cart item
            $insertSql = "INSERT INTO cart (customerId, productId, Quantity)
                         VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("iii", $_SESSION['customerID'], $productId, $quantity);
        }
        
        if ($stmt->execute()) {
            header("Location: ".$_SERVER['PHP_SELF']); // Redirect to prevent resubmission
            exit();
        } else {
            echo "<script>alert('Error adding to cart!');</script>";
        }
    } else {
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>VOLT GEAR - Premium Gaming Gear</title>
    
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

/* Responsive styles for mobile devices */
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
    
    .auth-buttons {
        margin-right: 0.5rem;
    }
    
    .login-button, .signup-button, .logout-button {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }
}
        
        /* Hero section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }
        
        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            padding: 0 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .hero-text {
            width: 50%;
        }
        
        .hero-text h2 {
            font-size: 4rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }
        
        .hero-text h2 span {
            color: #00FFFF;
            display: block;
        }
        
        .hero-text h2 span.purple {
            color: #8A2BE2;
        }
        
        .hero-text p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            color: #cccccc;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            color: white;
            text-decoration: none;
            padding: 1rem 2.5rem;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 255, 0.3);
        }
        
        .hero-image {
            width: 45%;
            position: relative;
        }
        
        .hero-image img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            transform: perspective(800px) rotateY(-15deg);
            box-shadow: 30px 30px 30px rgba(0, 0, 0, 0.5);
            transition: transform 0.5s;
        }
        
        .hero-image img:hover {
            transform: perspective(800px) rotateY(0deg);
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 70% 30%, rgba(138, 43, 226, 0.2), transparent 60%),
                        radial-gradient(circle at 30% 70%, rgba(0, 255, 255, 0.2), transparent 60%);
            z-index: 1;
        }
        
        /* Featured products section */
        .featured-products {
            padding: 5rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-header h3 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .section-header p {
            color: #cccccc;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
            height: 350px;
            overflow: hidden;
            position: relative;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.1);
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
        
        /* Categories section */
        .categories {
            padding: 5rem 5%;
            background-color: #0f0f0f;
        }
        
        .categories-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .category-card {
            position: relative;
            height: 250px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
        }
        
        .category-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .category-card:hover img {
            transform: scale(1.1);
        }
        
        .category-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1.5rem;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
        }
        
        .category-name {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .category-count {
            color: #00FFFF;
            font-size: 0.9rem;
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
            .hero-content {
                flex-direction: column;
                text-align: center;
            }
            
            .hero-text {
                width: 100%;
                margin-bottom: 3rem;
            }
            
            .hero-image {
                width: 80%;
            }
            
            .hero-text h2 {
                font-size: 3rem;
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
            
            .hero-text h2 {
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
                <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About</a></li>
                <li><a href="ourservices.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'ourservices.php' ? 'active' : ''; ?>">Our Services</a></li>
                <li><a href="contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
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


    <!-- Hero section -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-content">
            <div class="hero-text">
                <h2>ELEVATE YOUR <span>GAMING</span> <span class="purple">EXPERIENCE</span></h2>
                <p>Premium gaming gear designed for professional gamers and enthusiasts. Engineered for performance, built to win.</p>
                <a href="#browse-categories" class="cta-button">SHOP NOW</a>
            </div>
            <div class="hero-image">
                <img src="Headset.png" alt="Gaming Headset">
            </div>
        </div>
    </section>
    
<!--featuredProducts-->
<section class="featured-products">
    <div class="section-header">
        <h3>Featured Products</h3>
        <p>Discover our most popular high-performance gaming gear, trusted by professionals worldwide.</p>
    </div>
    
    <div class="products-grid">
        <?php
// SQL query to fetch one product from each category
$sql = "SELECT DISTINCT p.*
FROM (
    SELECT 
        a.productID,
        a.productname,
        a.catID,
        a.price,
        a.ProductImage,
        b.catname,
        ROW_NUMBER() OVER (PARTITION BY a.catID ORDER BY a.productID) as row_num
    FROM product AS a
    JOIN category AS b ON a.CatId = b.CatId
) p
WHERE p.row_num = 1";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // You're already getting catname from the database so we don't need the mapping
        echo '
        <div class="product-card">
            <div class="product-image">
                <img src="' . htmlspecialchars($row['ProductImage']) . '" alt="' . htmlspecialchars($row['productname']) . '">
            </div>
            <div class="product-info">
                <h4 class="product-name">' . htmlspecialchars($row['productname']) . '</h4>
                <div class="product-category">' . htmlspecialchars($row['catname']) . '</div>
                <p class="product-description">Premium gaming gear designed for performance.</p>
                <div class="product-bottom">
                    <div class="product-price">₹' . number_format($row['price'], 2) . '</div>
                    <form method="post" action="">
                        <input type="hidden" name="product_id" value="'.htmlspecialchars($row['productID']) .'">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" name="add_to_cart" class="add-to-cart">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>';
    }
} else {
    echo '<p>No products found.</p>';
}

// Close connection
$conn->close();
?>
    </div>
    
</section>
    
    <!-- Categories section -->
    <section class="categories" id="browse-categories">
        <div class="categories-container">
            <div class="section-header">
                <h3>Browse Categories</h3>
                <p>Find the perfect gear for your gaming setup.</p>
            </div>
            
            <div class="categories-grid">
                <div class="category-card">
                    <a href="keyboard.php">
                        <img  src="Keyboard2.png" alt="Keyboards Category">
                        <div class="category-overlay">
                            <h4 class="category-name" style="color: #ffffff;">Keyboards</h4>
                            <div class="category-count">10 Products</div>
                        </div>
                    </a>
                </div>
                
                <div class="category-card">
                    <a href="mice.php">
                        <img  src="mouse2.png" alt="Mice Category">
                        <div class="category-overlay">
                            <h4 class="category-name" style="color: #ffffff;">Mouse</h4>
                            <div class="category-count">10 Products</div>
                        </div>
                    </a>
                </div>
                
               
                <div class="category-card">
                    <a href="head.php">
                        <img  src="headphone2.png" alt="Headphone Category">
                        <div class="category-overlay">
                            <h4 class="category-name" style="color: #ffffff;">Headphones</h4>
                            <div class="category-count">12 Products</div>
                        </div>
                    </a>
                </div>
                
                <div class="category-card">
                    <a href="accessories.php">
                        <img  src="controller.png" alt="Accessories Category">
                        <div class="category-overlay">
                            <h4 class="category-name" style="color: #ffffff;">Accessories</h4>
                            <div class="category-count">11 Products</div>
                        </div>
                    </a>
                </div>
                </div>
            </div>
        </div>
    </section>
    
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
    
   
         <script src="volt-assistant.js">
        // Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.getElementById('main-nav');
    
    menuToggle.addEventListener('click', () => {
        mainNav.classList.toggle('active');
    });
    
    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const cartCount = document.querySelector('.cart-count');
    
    let currentCartCount = 3;
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentCartCount++;
            cartCount.textContent = currentCartCount;
            
            // Animation for the button
            button.textContent = 'Added!';
            button.style.backgroundColor = '#00FFFF';
            
            setTimeout(() => {
                button.textContent = 'Add to Cart';
                button.style.backgroundColor = '#8A2BE2';
            }, 1500);
        });
    });
    
    // Product hover effect
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-10px)';
            card.style.boxShadow = '0 15px 30px rgba(0, 255, 255, 0.15)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            card.style.boxShadow = '';
        });
    });
    
    // Hero image perspective effect
    const heroImage = document.querySelector('.hero-image img');
    const hero = document.querySelector('.hero');
    
    hero.addEventListener('mousemove', (e) => {
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;
        
        const rotateY = 15 - x * 30;
        const rotateX = y * 20 - 10;
        
        heroImage.style.transform = `perspective(800px) rotateY(${-rotateY}deg) rotateX(${rotateX}deg)`;
    });
    
    hero.addEventListener('mouseleave', () => {
        heroImage.style.transform = 'perspective(800px) rotateY(-15deg) rotateX(0deg)';
    });

    // Add this to your existing JavaScript file or create a new one
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.getElementById('main-nav');
    
    if(menuToggle && mainNav) {
        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('active');
        });
    }
});
});
        </script> 
</body>
</html>