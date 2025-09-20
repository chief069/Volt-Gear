
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOLT GEAR - Our Services</title>
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
        
        /* Page header */
        .page-header {
            padding-top: 150px;
            padding-bottom: 50px;
            text-align: center;
            background: radial-gradient(circle at 70% 30%, rgba(138, 43, 226, 0.2), transparent 60%),
            radial-gradient(circle at 30% 70%, rgba(0, 255, 255, 0.2), transparent 60%);
        }
        
        .page-header h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .page-header p {
            max-width: 700px;
            margin: 0 auto;
            color: #cccccc;
            line-height: 1.6;
        }
        
        /* Services section */
        .services-section {
            padding: 5rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .section-title h3 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .section-title p {
            color: #cccccc;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .service-card {
            background-color: #121212;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 2rem;
            text-align: center;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 255, 0.15);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #00FFFF, #8A2BE2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }
        
        .service-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #ffffff;
        }
        
        .service-description {
            color: #cccccc;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .service-link {
            color: #00FFFF;
            text-decoration: none;
            font-weight: 600;
            position: relative;
            transition: color 0.3s;
        }
        
        .service-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #00FFFF;
            transition: width 0.3s;
        }
        
        .service-link:hover::after {
            width: 100%;
        }
        
        /* Custom Build section */
        .custom-build {
            padding: 5rem 5%;
            background: #0f0f0f;
            position: relative;
            overflow: hidden;
        }
        
        .custom-build-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }
        
        .custom-build-content {
            width: 50%;
            padding-right: 3rem;
        }
        
        .custom-build-content h3 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        
        .custom-build-content p {
            color: #cccccc;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .custom-build-steps {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .custom-build-step {
            display: flex;
            align-items: flex-start;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00FFFF, #8A2BE2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .step-content h4 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .step-content p {
            color: #cccccc;
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        .custom-build-image {
            width: 45%;
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }
        
        .custom-build-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .custom-build-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 70% 30%, rgba(138, 43, 226, 0.1), transparent 60%),
                        radial-gradient(circle at 30% 70%, rgba(0, 255, 255, 0.1), transparent 60%);
        }
        
        /* Support section */
        .support-section {
            padding: 5rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .support-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .support-card {
            background-color: #121212;
            border-radius: 10px;
            overflow: hidden;
            padding: 2rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .support-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 255, 0.15);
        }
        
        .support-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .support-card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #00FFFF, #8A2BE2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }
        
        .support-card-title {
            font-size: 1.3rem;
        }
        
        .support-features {
            list-style: none;
        }
        
        .support-features li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.8rem;
            color: #cccccc;
        }
        
        .support-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #00FFFF;
        }
        
        .support-cta {
            text-align: center;
            margin-top: 2rem;
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
        
        /* FAQ section */
        .faq-section {
            padding: 5rem 5%;
            background-color: #0f0f0f;
        }
        
        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .faq-item {
            margin-bottom: 1.5rem;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .faq-question {
            background-color: #121212;
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.2rem;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .faq-question:hover {
            background-color: #1a1a1a;
        }
        
        .faq-question span {
            transition: transform 0.3s;
        }
        
        .faq-question.active span {
            transform: rotate(45deg);
        }
        
        .faq-answer {
            background-color: #1a1a1a;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .faq-answer.active {
            padding: 1.5rem;
            max-height: 300px;
        }
        
        .faq-answer p {
            color: #cccccc;
            line-height: 1.6;
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
            .custom-build-container {
                flex-direction: column;
            }
            
            .custom-build-content {
                width: 100%;
                padding-right: 0;
                margin-bottom: 3rem;
            }
            
            .custom-build-image {
                width: 100%;
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
            
            .page-header h2 {
                font-size: 2.5rem;
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
                <li><a href="index.php" class="active">Home</a></li>
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
    
    <!-- Page Header -->
    <section class="page-header">
        <h2>Our Services</h2>
        <p>From professional gaming setups to comprehensive support, VOLT GEAR provides a complete suite of services to enhance your gaming experience.</p>
    </section>
    
    <!-- Services Section -->
    <section class="services-section">
        <div class="section-title">
            <h3>What We Offer</h3>
            <p>At VOLT GEAR, we go beyond just selling premium gaming products. Explore our range of specialized services designed to take your gaming to the next level.</p>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🔧</div>
                <h4 class="service-title">Hardware Installation</h4>
                <p class="service-description">Expert installation of all gaming peripherals, ensuring optimal performance and perfect integration with your existing setup.</p>
                <a href="#" class="service-link">Learn More</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">⚙️</div>
                <h4 class="service-title">Custom Configuration</h4>
                <p class="service-description">Professional configuration of your gaming gear, from keyboard macros to mouse sensitivity settings tailored to your specific playstyle.</p>
                <a href="#" class="service-link">Learn More</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">🎮</div>
                <h4 class="service-title">Gaming Optimization</h4>
                <p class="service-description">System performance tuning to maximize FPS, reduce latency, and create the ideal environment for competitive gaming.</p>
                <a href="#" class="service-link">Learn More</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">🔄</div>
                <h4 class="service-title">Equipment Maintenance</h4>
                <p class="service-description">Regular cleaning, upkeep, and calibration services to keep your gaming gear in peak condition for years of use.</p>
                <a href="#" class="service-link">Learn More</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">📊</div>
                <h4 class="service-title">Performance Analysis</h4>
                <p class="service-description">Comprehensive analysis of your gaming performance to identify opportunities for equipment upgrades that match your play style.</p>
                <a href="#" class="service-link">Learn More</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">📱</div>
                <h4 class="service-title">RGB Customization</h4>
                <p class="service-description">Create stunning synchronized lighting effects across your entire gaming setup with our professional RGB programming service.</p>
                <a href="#" class="service-link">Learn More</a>
            </div>
        </div>
    </section>
    
    <!-- Custom Build Section -->
    <section class="custom-build">
        <div class="custom-build-bg"></div>
        <div class="custom-build-container">
            <div class="custom-build-content">
                <h3>Custom Gaming Setup Design</h3>
                <p>Our premium service for serious gamers. Work with our team of experts to design the perfect gaming environment from the ground up, tailored to your specific needs, preferences, and budget.</p>
                
                <div class="custom-build-steps">
                    <div class="custom-build-step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Consultation</h4>
                            <p>Meet with our gaming experts to discuss your needs, gaming style, and aesthetic preferences.</p>
                        </div>
                    </div>
                    
                    <div class="custom-build-step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Design Planning</h4>
                            <p>Receive a custom design plan including equipment selection, desk layout, cable management, and lighting.</p>
                        </div>
                    </div>
                    
                    <div class="custom-build-step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Professional Installation</h4>
                            <p>Our technicians handle the complete setup and installation of all components in your space.</p>
                        </div>
                    </div>
                    
                    <div class="custom-build-step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h4>Fine-Tuning & Training</h4>
                            <p>Final adjustments to your setup and personalized training on how to get the most from your new gear.</p>
                        </div>
                    </div>
                </div>
                
                <a href="contact.php" class="cta-button">Schedule a Consultation</a>
            </div>
            
            <div class="custom-build-image">
                <img src="setup.png" alt="Custom Gaming Setup">
            </div>
        </div>
    </section>
    
    <!-- Support Section -->
    <section class="support-section">
        <div class="section-title">
            <h3>Customer Support & Warranty</h3>
            <p>We stand behind every product we sell with industry-leading support and warranty protection.</p>
        </div>
        
        <div class="support-cards">
            <div class="support-card">
                <div class="support-card-header">
                    <div class="support-card-icon">🔍</div>
                    <h4 class="support-card-title">Standard Support</h4>
                </div>
                <ul class="support-features">
                    <li>24/7 technical support via chat and email</li>
                    <li>Comprehensive online knowledge base</li>
                    <li>Video tutorials and guides</li>
                    <li>2-year standard warranty on all products</li>
                    <li>30-day satisfaction guarantee</li>
                </ul>
            </div>
            
            <div class="support-card">
                <div class="support-card-header">
                    <div class="support-card-icon">⭐</div>
                    <h4 class="support-card-title">Premium Support</h4>
                </div>
                <ul class="support-features">
                    <li>All standard support features</li>
                    <li>Priority phone support line</li>
                    <li>Extended 3-year warranty coverage</li>
                    <li>Next-day replacement for critical failures</li>
                    <li>Quarterly performance check-ups</li>
                    <li>50% discount on repairs outside warranty</li>
                </ul>
            </div>
            
            <div class="support-card">
                <div class="support-card-header">
                    <div class="support-card-icon">👑</div>
                    <h4 class="support-card-title">Pro Gamer Support</h4>
                </div>
                <ul class="support-features">
                    <li>All premium support features</li>
                    <li>Dedicated support specialist</li>
                    <li>24/7 emergency phone support</li>
                    <li>5-year comprehensive warranty</li>
                    <li>Same-day replacement service</li>
                    <li>Free biannual maintenance service</li>
                    <li>Early access to new product releases</li>
                </ul>
            </div>
        </div>
        
        <div class="support-cta">
            <a href="subscribe.php" class="cta-button">Upgrade Your Support Plan</a>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="section-title">
            <h3>Frequently Asked Questions</h3>
            <p>Find answers to common questions about our services.</p>
        </div>
        
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    How do I schedule an installation service?
                    <span>+</span>
                </div>
                <div class="faq-answer">
                    <p>You can schedule an installation service by calling our customer service line at (555) 123-4567, using the online booking tool in your account dashboard, or visiting any VOLT GEAR retail location. Installation appointments are typically available within 3-5 business days.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    What areas do you service for custom setups?
                    <span>+</span>
                </div>
                <div class="faq-answer">
                    <p>We currently offer custom setup services in most major metropolitan areas across the United States and Canada. For areas outside our service zone, we provide detailed video consultations and step-by-step guides to help you create your optimal setup. Check our service locator tool to see if your area is covered.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    How much does a custom gaming setup cost?
                    <span>+</span>
                </div>
                <div class="faq-answer">
                    <p>Custom gaming setup costs vary widely based on your needs and preferences. Basic setups typically start around $1,500, while premium setups with top-tier equipment can range from $3,000 to $10,000+. Our free consultation service provides a detailed quote with no obligation to proceed.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    Can I upgrade my support plan after purchase?
                    <span>+</span>
                </div>
                <div class="faq-answer">
                    <p>Yes, you can upgrade your support plan at any time through your account dashboard or by contacting customer service. Upgrades take effect immediately, and you'll only be charged the prorated difference for the remainder of your current warranty period.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    Do you offer services for eSports teams?
                    <span>+</span>
                </div>
                <div class="faq-answer">
                    <p>Absolutely! We offer specialized services for eSports organizations including team equipment packages, training facility design, on-site technical support for events, and custom-branded peripherals. Contact our eSports division at esports@voltgear.com for more information
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Footer -->
        <footer>
            <div class="footer-container">
                <div class="footer-top">
                    <div class="footer-column">
                        <h4>Products</h4>
                        <ul>
                            <li><a href="#">Keyboards</a></li>
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
        
        <!-- JavaScript for interactive elements -->
        <script src="volt-assistant.js">
            // Mobile menu toggle
            document.getElementById('menu-toggle').addEventListener('click', function() {
                document.getElementById('main-nav').classList.toggle('active');
            });
            
            // FAQ accordion functionality
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    // Toggle active class on question
                    question.classList.toggle('active');
                    
                    // Toggle active class on answer
                    const answer = question.nextElementSibling;
                    answer.classList.toggle('active');
                    
                    // Close other FAQ items
                    faqQuestions.forEach(item => {
                        if (item !== question && item.classList.contains('active')) {
                            item.classList.remove('active');
                            item.nextElementSibling.classList.remove('active');
                        }
                    });
                });
            });
        </script>
    </body>
    </html>