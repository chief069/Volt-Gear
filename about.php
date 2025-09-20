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
    <title>VOLT GEAR - About Us</title>
    <script src="AIassistant.js"></script>
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
        
        /* Hero section */
        .about-hero {
            height: 60vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 100px;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('gaming-setup.jpg') no-repeat center center/cover;
        }
        
        .about-hero-content {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            padding: 0 5%;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        
        .about-hero-content h2 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
        }
        
        .about-hero-content h2 span {
            color: #00FFFF;
        }
        
        .about-hero-content p {
            font-size: 1.2rem;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            color: #cccccc;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 70% 30%, rgba(138, 43, 226, 0.3), transparent 60%),
                        radial-gradient(circle at 30% 70%, rgba(0, 255, 255, 0.3), transparent 60%);
            z-index: 1;
        }
        
        /* Our Story section */
        .our-story {
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
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .story-content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 3rem;
        }
        
        .story-text {
            flex: 1;
            min-width: 300px;
        }
        
        .story-text p {
            color: #cccccc;
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }
        
        .story-image {
            flex: 1;
            min-width: 300px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.2);
        }
        
        .story-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* Mission & Values section */
        .mission-values {
            padding: 5rem 5%;
            background-color: #0f0f0f;
        }
        
        .mission-values-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .value-card {
            background-color: #121212;
            border-radius: 10px;
            padding: 2rem;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }
        
        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 255, 0.15);
        }
        
        .value-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: #8A2BE2;
        }
        
        .value-card h4 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #00FFFF;
        }
        
        .value-card p {
            color: #cccccc;
            line-height: 1.6;
        }
        
        /* Team section */
        .team {
            padding: 5rem 5%;
        }
        
        .team-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .team-member {
            background-color: #121212;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 255, 0.15);
        }
        
        .member-image {
            height: 300px;
            overflow: hidden;
        }
        
        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .team-member:hover .member-image img {
            transform: scale(1.1);
        }
        
        .member-info {
            padding: 1.5rem;
            text-align: center;
        }
        
        .member-name {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .member-position {
            color: #00FFFF;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        
        .member-bio {
            color: #cccccc;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        /* Stats section */
        .stats {
            padding: 5rem 5%;
            background: linear-gradient(135deg, rgba(0, 255, 255, 0.1), rgba(138, 43, 226, 0.1));
        }
        
        .stats-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-label {
            font-size: 1.2rem;
            color: #cccccc;
        }
        
        /* CTA section */
        .cta {
            padding: 5rem 5%;
            text-align: center;
        }
        
        .cta-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .cta h3 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        
        .cta p {
            color: #cccccc;
            margin-bottom: 2rem;
            line-height: 1.6;
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
        }
        
        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 255, 0.3);
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
            .story-content {
                flex-direction: column;
            }
            
            .about-hero-content h2 {
                font-size: 2.8rem;
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
            
            .about-hero-content h2 {
                font-size: 2.2rem;
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
    
    <!-- About Hero section -->
    <section class="about-hero">
        <div class="hero-bg"></div>
        <div class="about-hero-content">
            <h2>ABOUT <span>VOLT GEAR</span></h2>
            <p>We create premium gaming gear engineered for performance and designed for victory. Behind every product is our passion for gaming and commitment to innovation.</p>
        </div>
    </section>
    
    <!-- Our Story section -->
    <section class="our-story">
        <div class="section-header">
            <h3>Our Story</h3>
            <p>From passionate gamers to industry leaders</p>
        </div>
        
        <div class="story-content">
            <div class="story-text">
                <p>Founded in 2018 by a team of competitive gamers and hardware engineers, VOLT GEAR was born from a simple frustration: existing gaming peripherals weren't meeting the demands of serious players.</p>
                
                <p>Our founders spent countless hours competing in tournaments, and they intimately understood how critical every millisecond of response time could be. They knew that gaming gear shouldn't just look good—it needed to provide a genuine competitive advantage.</p>
                
                <p>What began as a small startup creating custom mechanical keyboards quickly expanded into a full lineup of premium gaming peripherals. Today, VOLT GEAR products are used by professional esports teams and enthusiast gamers in over 50 countries worldwide.</p>
                
                <p>We remain committed to our original mission: creating gaming gear with no compromises, where performance, durability, and aesthetics coexist in perfect harmony.</p>
            </div>
            
            <div class="story-image">
                <img src="setup2.png" alt="VOLT GEAR founding team">
            </div>
        </div>
    </section>
    
    <!-- Mission & Values section -->
    <section class="mission-values">
        <div class="mission-values-container">
            <div class="section-header">
                <h3>Our Mission & Values</h3>
                <p>The principles that drive everything we do</p>
            </div>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">⚡</div>
                    <h4>Performance First</h4>
                    <p>We believe that superior performance should never be compromised. Every millisecond matters, and our products are engineered to deliver the fastest, most responsive gaming experience possible.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">🔍</div>
                    <h4>Obsessive Attention to Detail</h4>
                    <p>From the tactile feedback of our switches to the ergonomics of our designs, we obsess over every detail to create products that feel like a natural extension of the player.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">🔧</div>
                    <h4>Relentless Innovation</h4>
                    <p>The gaming world evolves rapidly, and so do we. Our R&D team constantly pushes the boundaries of what's possible to keep our community at the cutting edge.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">🎮</div>
                    <h4>For Gamers, By Gamers</h4>
                    <p>We don't just make gaming gear—we use it daily. Our team includes competitive players who test prototypes in real tournament conditions to ensure they meet our standards.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Team section -->
    <section class="team">
        <div class="team-container">
            <div class="section-header">
                <h3>Meet Our Team</h3>
                <p>The passionate minds behind VOLT GEAR</p>
            </div>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <img src="chetan.jpg" alt="Alex Chen">
                    </div>
                    <div class="member-info">
                        <h4 class="member-name">Chetan Gupta</h4>
                        <div class="member-position">Founder & CEO</div>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <img src="sintu.jpg" alt="Sophia Rodriguez">
                    </div>
                    <div class="member-info">
                        <h4 class="member-name">Sintu Kumar</h4>
                        <div class="member-position">Lead Hardware Engineer</div>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <img src="lakshita2.jpg" alt="Marcus Kim">
                    </div>
                    <div class="member-info">
                        <h4 class="member-name">Lakshita Chawla</h4>
                        <div class="member-position">Head of Esports Relations</div>
                        
                    </div>
                </div>
                
               
            </div>
        </div>
    </section>
    
    <!-- Stats section -->
    <section class="stats">
        <div class="stats-container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">7+</div>
                    <div class="stat-label">Years of Innovation</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Countries Served</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number">25</div>
                    <div class="stat-label">Pro Teams Sponsored</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number">500K+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA section -->
    <section class="cta">
        <div class="cta-container">
            <h3>Join the VOLT GEAR Revolution</h3>
            <p>Ready to elevate your gaming experience? Explore our collection of premium gaming peripherals designed to help you perform at your best.</p>
            <a href="index.php" class="cta-button">SHOP NOW</a>
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
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="about.html">Careers</a></li>
                        <li><a href="about.html">Press</a></li>
                        <li><a href="about.html">Blog</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="contact.html">Help Center</a></li>
                        <li><a href="contact.html">Warranty</a></li>
                        <li><a href="contact.html">Returns</a></li>
                        <li><a href="contact.html">Order Status</a></li>
                        <li><a href="contact.html">FAQs</a></li>
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
        const menuToggle = document.getElementById('menu-toggle');
        const mainNav = document.getElementById('main-nav');
        
        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('active');
        });
        
        // Animation for value cards
        const valueCards = document.querySelectorAll('.value-card');
        
        valueCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px)';
                card.style.boxShadow = '0 15px 30px rgba(0, 255, 255, 0.15)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
                card.style.boxShadow = '';
            });
        });
        
        // Team member hover effect
        const teamMembers = document.querySelectorAll('.team-member');
        
        teamMembers.forEach(member => {
            member.addEventListener('mouseenter', () => {
                member.style.transform = 'translateY(-10px)';
                member.style.boxShadow = '0 15px 30px rgba(0, 255, 255, 0.15)';
            });
            
            member.addEventListener('mouseleave', () => {
                member.style.transform = '';
                member.style.boxShadow = '';
            });
        });
    </script>
</body>
</html>