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
    <title>VOLT GEAR - Contact Us</title>
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
    top: 0;
    left: 0;
    transition: transform 0.3s ease;
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
        
        /* Mobile menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Contact page specific styles */
        .page-header {
            height: 40vh;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('/api/placeholder/1920/600');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 70% 30%, rgba(138, 43, 226, 0.2), transparent 60%),
                        radial-gradient(circle at 30% 70%, rgba(0, 255, 255, 0.2), transparent 60%);
            z-index: 1;
        }
        
        .page-header-content {
            position: relative;
            z-index: 2;
        }
        
        .page-header h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        
        .page-header p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            color: #cccccc;
        }
        
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 5rem 5%;
        }
        
        .contact-wrapper {
            display: flex;
            gap: 3rem;
        }
        
        .contact-info {
            flex: 1;
        }
        
        .contact-form-wrapper {
            flex: 2;
        }
        
        .section-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 0.8rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
        }
        
        .info-card {
            background-color: #121212;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 255, 255, 0.1);
        }
        
        .info-card h3 {
            display: flex;
            align-items: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #00FFFF;
        }
        
        .info-card h3 span {
            margin-right: 1rem;
            font-size: 1.5rem;
        }
        
        .info-card p, .info-card a {
            color: #cccccc;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }
        
        .info-card a {
            text-decoration: none;
            transition: color 0.3s;
            display: block;
        }
        
        .info-card a:hover {
            color: #00FFFF;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #1e1e1e;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
        }
        
        .social-link:hover {
            background-color: #8A2BE2;
            transform: translateY(-3px);
        }
        
        .contact-form {
            background-color: #121212;
            border-radius: 10px;
            padding: 2.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            background-color: #1e1e1e;
            border: 1px solid #333;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-control:focus {
            border-color: #00FFFF;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.2);
            outline: none;
        }
        
        .form-control::placeholder {
            color: #666;
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .form-row {
            display: flex;
            gap: 1.5rem;
        }
        
        .form-col {
            flex: 1;
        }
        
        .submit-btn {
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            cursor: pointer;
            display: inline-block;
        }
        
        .submit-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 255, 0.3);
        }
        
        /* Map section */
        .map-section {
            background-color: #0f0f0f;
            padding: 5rem 0;
        }
        
        .map-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 5%;
        }
        
        .map-wrapper {
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        
        .map {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        /* FAQ Section */
        .faq-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 5rem 5%;
        }
        
        .faq-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .faq-title h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .faq-title p {
            color: #cccccc;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
        }
        
        .faq-item {
            background-color: #121212;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }
        
        .faq-item:hover {
            transform: translateY(-5px);
        }
        
        .faq-question {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #00FFFF;
        }
        
        .faq-answer {
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
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .contact-wrapper {
                flex-direction: column;
            }
            
            .page-header h1 {
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
            
            .page-header h1 {
                font-size: 2.2rem;
            }
            
            .form-row {
                flex-direction: column;
                gap: 1.5rem;
            }
            
            .faq-grid {
                grid-template-columns: 1fr;
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




        /* Success and error messages */
.success-message,
.error-message {
    padding: 1rem;
    border-radius: 5px;
    margin: 1.5rem 0;
    font-weight: 500;
    animation: fadeIn 0.3s ease-in-out;
}

.success-message {
    background-color: rgba(0, 255, 128, 0.1);
    border: 1px solid #00FF80;
    color: #00FF80;
}

.error-message {
    background-color: rgba(255, 0, 0, 0.1);
    border: 1px solid #FF3333;
    color: #FF3333;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/ Loading state for submit button */
.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
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
        <div class="page-header-content">
            <h1>Contact Us</h1>
            <p>We're here to help with any questions about our products or services. Reach out to our team.</p>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="contact-container">
        <div class="contact-wrapper">
            <div class="contact-info">
                <h2 class="section-title">Get In Touch</h2>
                
                <div class="info-card">
                    <h3><span>📍</span> Our Location</h3>
                    <p>123 Gamig Shop</p>
                    <p>V3S Mall</p>
                    <p>East Delhi, India 110051</p>
                </div>
                
                <div class="info-card">
                    <h3><span>📱</span> Contact Info</h3>
                    <p>Phone: <a href="tel:+18005551234">1-800-555-1234</a></p>
                    <p>Email: <a href="mailto:support@voltgear.com">support@voltgear.com</a></p>
                    <p>Sales: <a href="mailto:sales@voltgear.com">sales@voltgear.com</a></p>
                </div>
                
                <div class="info-card">
                    <h3><span>⏰</span> Working Hours</h3>
                    <p>Monday - Friday: 9am - 6pm IST</p>
                    <p>Saturday: 10am - 4pm IST</p>
                    <p>Sunday: Closed</p>
                </div>
                
                <div class="info-card">
                    <h3><span>🌐</span> Connect With Us</h3>
                    <p>Follow us on social media for the latest updates, tips, and promotions.</p>
                    <div class="social-links">
                        <a href="#" class="social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>
                        <a href="#" class="social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                            </svg>
                        </a>
                        <a href="#" class="social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </a>
                        <a href="#" class="social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                                <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-wrapper">
                <h2 class="section-title">Send Us A Message</h2>
                
                <form class="contact-form" id="contactForm">
    <div class="form-row">
        <div class="form-col">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" required>
            </div>
        </div>
        <div class="form-col">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" class="form-control" placeholder="What is this regarding?">
    </div>
    
    <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" class="form-control" placeholder="Tell us how we can help you..." required></textarea>
    </div>
    
    <div class="form-group">
        <label>Reason for Contact</label>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <label style="display: flex; align-items: center;">
                <input type="radio" name="reason" value="support" style="margin-right: 8px;">
                Product Support
            </label>
            <label style="display: flex; align-items: center;">
                <input type="radio" name="reason" value="sales" style="margin-right: 8px;">
                Sales Inquiry
            </label>
            <label style="display: flex; align-items: center;">
                <input type="radio" name="reason" value="feedback" style="margin-right: 8px;">
                Feedback
            </label>
            <label style="display: flex; align-items: center;">
                <input type="radio" name="reason" value="other" style="margin-right: 8px;">
                Other
            </label>
        </div>
    </div>
    
    <!-- Hidden Web3Forms access key -->
    <input type="hidden" name="access_key" value="1120e367-d52a-4cb4-bc24-54258774bf81">
    
    <!-- Optional: Redirect URL after form submission -->
    <input type="hidden" name="redirect" value="">
    
    <!-- Optional: Subject override -->
    <input type="hidden" name="from_name" value="VOLT GEAR Contact Form">
    
    <!-- Optional: To help avoid spam -->
    <input type="checkbox" name="botcheck" style="display: none;">
    
    <div class="form-group">
        <button type="submit" class="submit-btn">Send Message</button>
    </div>
</form>
            </div>
        </div>
    </section>
    
    <!-- Map Section -->
    <section class="map-section">
        <div class="map-container">
            <div class="map-wrapper">
                <iframe class="map" src="Map.png" allowfullscreen></iframe>
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="faq-title">
            <h2>Frequently Asked Questions</h2>
            <p>Find quick answers to common questions about our products and services.</p>
        </div>
        
        <div class="faq-grid">
            <div class="faq-item">
                <h3 class="faq-question">What is your return policy?</h3>
                <p class="faq-answer">We offer a 30-day return policy for all undamaged products in their original packaging. For warranty-related claims, please contact our support team directly.</p>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">How long does shipping take?</h3>
                <p class="faq-answer">Standard shipping within the US takes 3-5 business days. International shipping typically takes 7-14 business days depending on the destination.</p>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">Do you offer technical support for your products?</h3>
                <p class="faq-answer">Yes, we provide technical support for all our products. You can reach our support team via email, phone, or through the contact form on this page.</p>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">Are your products covered by warranty?</h3>
                <p class="faq-answer">All VOLT GEAR products come with a 2-year limited warranty that covers manufacturing defects. Some premium products have extended warranty options available.</p>
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
            <script src="contact-form.js"></script>
    <script src="volt-assistant.js">
        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const mainNav = document.getElementById('main-nav');
        
        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('active');
        });
        
        // Form submission handling
        const contactForm = document.getElementById('contactForm');
        
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Get form values
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            // Normally you would send this data to a server
            // For demonstration, we'll just show a success message
            
            // Clear the form
            contactForm.reset();
            
            // Show success message
            alert(`Thanks for your message, ${name}! We'll get back to you as soon as possible.`);
        });
    </script>
    
</body>
</html>