<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOLT GEAR - Privacy Policy</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            /* Dark theme colors (default) */
            --bg-primary: #0a0a0a;
            --bg-secondary: #121212;
            --bg-tertiary: #1e1e1e;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --text-tertiary: #999999;
            --accent-cyan: #00FFFF;
            --accent-purple: #8A2BE2;
            --accent-purple-hover: #7A1BD2;
            --overlay-gradient: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            --card-shadow: 0 15px 30px rgba(0, 255, 255, 0.15);
            --hero-bg: radial-gradient(circle at 70% 30%, rgba(138, 43, 226, 0.2), transparent 60%),
                       radial-gradient(circle at 30% 70%, rgba(0, 255, 255, 0.2), transparent 60%);
            --header-shadow: 0 0 20px rgba(0, 255, 255, 0.2);
            --accent-cyan-rgb: 0, 255, 255;
            --accent-purple-rgb: 138, 43, 226;
        }
        
        [data-theme="light"] {
            /* Light theme colors */
            --bg-primary: #f5f5f7;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f0f0f0;
            --text-primary: #121212;
            --text-secondary: #666666;
            --text-tertiary: #888888;
            --accent-cyan: #0097B2;
            --accent-purple: #6200B3;
            --accent-purple-hover: #500094;
            --overlay-gradient: linear-gradient(transparent, rgba(255, 255, 255, 0.9));
            --card-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            --hero-bg: radial-gradient(circle at 70% 30%, rgba(138, 43, 226, 0.1), transparent 60%),
                       radial-gradient(circle at 30% 70%, rgba(0, 151, 178, 0.1), transparent 60%);
            --header-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            --accent-cyan-rgb: 0, 151, 178;
            --accent-purple-rgb: 98, 0, 179;
        }
        
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Header styles */
        header {
            background-color: var(--bg-secondary);
            padding: 1rem 5%;
            position: fixed;
            width: 100%;
            z-index: 100;
            box-shadow: var(--header-shadow);
            transition: background-color 0.3s, box-shadow 0.3s;
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
            color: var(--text-primary);
            transition: color 0.3s;
        }
        
        .logo h1 span {
            color: var(--accent-purple);
            transition: color 0.3s;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin: 0 1rem;
        }
        
        nav ul li a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: color 0.3s;
            padding: 0.5rem 0;
            position: relative;
        }
        
        nav ul li a:hover {
            color: var(--accent-cyan);
        }
        
        nav ul li a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-cyan);
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
            color: var(--accent-cyan);
            border: 1px solid var(--accent-cyan);
            background-color: transparent;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }

        .login-button:hover {
            background-color: rgba(0, 255, 255, 0.1);
        }

        .signup-button {
            background-color: var(--accent-purple);
            color: white;
            border: 1px solid transparent;
            transition: background-color 0.3s, transform 0.3s;
        }

        .signup-button:hover {
            background-color: var(--accent-purple-hover);
            transform: translateY(-2px);
        }
        
        .cart-icon {
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
            position: relative;
            transition: color 0.3s;
        }
        
        .cart-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: var(--accent-purple);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: background-color 0.3s;
        }
        
        /* Mobile menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s;
        }

        /* Privacy Policy Content Styles */
        .privacy-policy-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 5% 60px;
        }

        .privacy-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .privacy-header h2 {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
            transition: color 0.3s;
        }

        .privacy-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .privacy-header::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            margin: 1.5rem auto;
            background: linear-gradient(90deg, var(--accent-cyan), var(--accent-purple));
            border-radius: 2px;
        }

        .privacy-section {
            margin-bottom: 2.5rem;
            padding: 2rem;
            background-color: var(--bg-secondary);
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: background-color 0.3s, box-shadow 0.3s, transform 0.3s;
        }
        
        .privacy-section:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow);
        }

        .privacy-section h3 {
            font-size: 1.8rem;
            margin-bottom: 1.2rem;
            color: var(--text-primary);
            transition: color 0.3s;
            position: relative;
            padding-bottom: 0.8rem;
        }

        .privacy-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-cyan);
            transition: background 0.3s;
        }

        .privacy-section p, .privacy-section ul li {
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 1rem;
            font-size: 1.05rem;
            transition: color 0.3s;
        }

        .privacy-section strong {
            color: var(--accent-cyan);
            font-weight: 600;
            transition: color 0.3s;
        }

        .privacy-section ul {
            list-style-position: inside;
            margin-bottom: 1.5rem;
            padding-left: 1rem;
        }

        .privacy-section ul li {
            margin-bottom: 0.8rem;
        }

        .privacy-section h4 {
            font-size: 1.4rem;
            margin: 1.5rem 0 1rem;
            color: var(--text-primary);
            transition: color 0.3s;
        }

        .last-updated {
            font-style: italic;
            text-align: center;
            margin: 2rem 0;
            color: var(--text-tertiary);
            transition: color 0.3s;
        }

        .accent-text {
            color: var(--accent-purple);
            transition: color 0.3s;
        }

        .contact-button {
            display: block;
            width: 200px;
            margin: 3rem auto;
            padding: 1rem 2rem;
            background: linear-gradient(90deg, var(--accent-cyan), var(--accent-purple));
            color: white;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            border-radius: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .contact-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 255, 0.3);
        }

        /* Footer */
        footer {
            background-color: var(--bg-secondary);
            padding: 5rem 5% 2rem;
            transition: background-color 0.3s;
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
            color: var(--text-primary);
            transition: color 0.3s;
        }
        
        .footer-column h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-cyan), var(--accent-purple));
            transition: background 0.3s;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 0.8rem;
        }
        
        .footer-column ul li a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: var(--accent-cyan);
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
            color: var(--text-tertiary);
            font-size: 0.9rem;
            transition: color 0.3s;
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
            background-color: var(--bg-tertiary);
            border-radius: 50%;
            margin-left: 1rem;
            color: var(--text-primary);
            transition: background-color 0.3s, color 0.3s;
        }
        
        .social-icon:hover {
            background-color: var(--accent-purple);
            color: white;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .privacy-header h2 {
                font-size: 2.3rem;
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
                background-color: var(--bg-secondary);
                transition: left 0.3s, background-color 0.3s;
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
            
            .privacy-header h2 {
                font-size: 2rem;
            }
            
            .privacy-section h3 {
                font-size: 1.5rem;
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

            .auth-buttons {
                margin-right: 0.5rem;
            }
            
            .login-button, .signup-button {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body data-theme="dark">
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="index.php" style="text-decoration: none; display: flex; align-items: center; color: inherit;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 60" width="100" height="60">
                        <!-- Lightning bolt element - aqua (with color transition for theme changes) -->
                        <path d="M15 10 L30 10 L25 30 L40 30 L15 50 L20 35 L10 35 Z" fill="currentColor" class="lightning-bolt" style="color: var(--accent-cyan)"/>
                        
                        <!-- Gear element - purple (with color transition for theme changes) -->
                        <g transform="translate(70, 30)">
                            <!-- Outer gear -->
                            <path d="M0 -15 L3 -15 L5 -10 L10 -12 L12 -7 L8 -4 L12 0 L8 4 L12 7 L8 12 L4 10 L2 15 L0 15 L-2 15 L-4 10 L-8 12 L-12 7 L-8 4 L-12 0 L-8 -4 L-12 -7 L-8 -12 L-4 -10 L-3 -15 Z" fill="currentColor" class="gear" style="color: var(--accent-purple)"/>
                            <!-- Inner gear -->
                            <circle cx="0" cy="0" r="5" fill="var(--bg-secondary)"/>
                            <circle cx="0" cy="0" r="2" fill="var(--accent-purple)"/>
                        </g>
                    </svg>
                    <h1>VOLT<span>GEAR</span></h1>
                </a>
            </div>
            
            <nav id="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="catagories.html">Categories</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="ourservices.html">Our Services</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </nav>
            
            <div class="auth-buttons">
                <a href="login.php" class="login-button">Login</a>
                <a href="signup.php" class="signup-button">Sign Up</a>
            </div>
            
            <button class="cart-icon">🛒<span class="cart-count">3</span></button>
            
            <button class="mobile-menu-toggle" id="menu-toggle">☰</button>
        </div>
    </header>
    
    <!-- Privacy Policy Content -->
    <main class="privacy-policy-container">
        <div class="privacy-header">
            <h2>Privacy Policy</h2>
            <p>At VOLT GEAR, we are committed to protecting your privacy and ensuring your personal information is handled with care.</p>
        </div>
        
        <section class="privacy-section">
            <h3>1. Introduction</h3>
            <p>Welcome to VOLT GEAR's Privacy Policy. This policy explains how we collect, use, disclose, and safeguard your information when you visit our website, make purchases, or interact with us in any way.</p>
            <p>We value your privacy and are dedicated to transparency regarding the data we collect and how it is used. By using our services, you consent to the practices described in this policy.</p>
        </section>
        
        <section class="privacy-section">
            <h3>2. Information We Collect</h3>
            <p>We may collect the following types of information:</p>
            
            <h4>2.1 Personal Information</h4>
            <ul>
                <li><strong>Contact Information:</strong> Name, email address, phone number, and shipping/billing address</li>
                <li><strong>Account Information:</strong> Username, password, and account preferences</li>
                <li><strong>Payment Details:</strong> Credit card information, payment history (note that full payment details are processed by our secure payment providers)</li>
                <li><strong>Profile Information:</strong> Gaming preferences, purchase history, wishlists, and reviews you submit</li>
            </ul>
            
            <h4>2.2 Automatically Collected Information</h4>
            <ul>
                <li><strong>Device Information:</strong> IP address, browser type, operating system, and device identifiers</li>
                <li><strong>Usage Data:</strong> Pages visited, time spent, links clicked, products viewed, and search queries</li>
                <li><strong>Cookies and Similar Technologies:</strong> Data collected through cookies, web beacons, and other tracking technologies</li>
                <li><strong>Location Information:</strong> General location based on IP address or more precise location if explicitly permitted</li>
            </ul>
        </section>
        
        <section class="privacy-section">
            <h3>3. How We Use Your Information</h3>
            <p>We use your information for various purposes, including to:</p>
            <ul>
                <li>Process orders and complete transactions</li>
                <li>Create and maintain your VOLT GEAR account</li>
                <li>Provide customer support and respond to inquiries</li>
                <li>Send important updates about your orders and our services</li>
                <li>Personalize your shopping experience and recommend products</li>
                <li>Send marketing communications (with your consent)</li>
                <li>Improve our website, products, and services</li>
                <li>Detect and prevent fraud or security breaches</li>
                <li>Comply with legal obligations</li>
            </ul>
        </section>
        
        <section class="privacy-section">
            <h3>4. Cookies and Tracking Technologies</h3>
            <p>We use cookies and similar tracking technologies to collect information about your browsing activities on our website. These technologies help us:</p>
            <ul>
                <li>Remember your preferences and settings</li>
                <li>Keep track of items in your shopping cart</li>
                <li>Understand how you use our website</li>
                <li>Deliver personalized content and advertisements</li>
                <li>Analyze website performance</li>
            </ul>
            <p>You can manage your cookie preferences through your browser settings. However, disabling certain cookies may limit your ability to use some features of our website.</p>
        </section>
        
        <section class="privacy-section">
            <h3>5. Information Sharing and Disclosure</h3>
            <p>We may share your information with:</p>
            <ul>
                <li><strong>Service Providers:</strong> Companies that help us operate our business (payment processors, shipping companies, customer service providers)</li>
                <li><strong>Business Partners:</strong> When necessary to provide products or services you've requested</li>
                <li><strong>Legal Authorities:</strong> When required by law, court order, or governmental regulation</li>
                <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of business assets</li>
            </ul>
            <p>We do <span class="accent-text">not</span> sell your personal information to third parties for marketing purposes.</p>
        </section>
        
        <section class="privacy-section">
            <h3>6. Data Security</h3>
            <p>We implement appropriate technical and organizational measures to protect your personal information from unauthorized access, disclosure, alteration, and destruction. These measures include:</p>
            <ul>
                <li>Encryption of sensitive data</li>
                <li>Secure network infrastructure</li>
                <li>Regular security assessments</li>
                <li>Employee training on data protection</li>
                <li>Access controls and authentication procedures</li>
            </ul>
            <p>However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
        </section>
        
        <section class="privacy-section">
            <h3>7. Your Rights and Choices</h3>
            <p>Depending on your location, you may have certain rights regarding your personal information:</p>
            <ul>
                <li><strong>Access:</strong> Request a copy of the personal information we hold about you</li>
                <li><strong>Correction:</strong> Update or correct inaccurate information</li>
                <li><strong>Deletion:</strong> Request deletion of your personal information</li>
                <li><strong>Restriction:</strong> Request restriction of processing of your data</li>
                <li><strong>Data Portability:</strong> Request transfer of your information to another service</li>
                <li><strong>Objection:</strong> Object to processing of your personal information</li>
                <li><strong>Withdraw Consent:</strong> Withdraw consent for data processing at any time</li>
            </ul>
            <p>To exercise these rights, please contact us using the information provided in the "Contact Us" section.</p>
        </section>
        
        <section class="privacy-section">
            <h3>8. Marketing Communications</h3>
            <p>With your consent, we may send you marketing communications about our products, services, and promotions. You can opt out of these communications at any time by:</p>
            <ul>
                <li>Clicking the "unsubscribe" link in any marketing email</li>
                <li>Updating your communication preferences in your account settings</li>
                <li>Contacting our customer service team</li>
            </ul>
        </section>
        
        <section class="privacy-section">
            <h3>9. Children's Privacy</h3>
            <p>Our services are not directed to individuals under the age of 13. We do not knowingly collect personal information from children. If you are a parent or guardian and believe your child has provided us with personal information, please contact us, and we will take steps to remove such information.</p>
        </section>
        
        <section class="privacy-section">
            <h3>10. International Data Transfers</h3>
            <p>We may transfer your personal information to countries other than your country of residence for processing and storage. When we do so, we ensure appropriate safeguards are in place to protect your information and comply with applicable data protection laws.</p>
        </section>
        
        <section class="privacy-section">
            <h3>11. Changes to This Privacy Policy</h3>
            <p>We may update this Privacy Policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. The updated version will be indicated by an updated "Last Updated" date, and the updated version will be effective as soon as it is accessible.</p>
            <p>We encourage you to review this Privacy Policy regularly to stay informed about how we are protecting your information.</p>
        </section>
        
        <section class="privacy-section">
            <h3>12. Contact Us</h3>
            <p>If you have any questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us at:</p>
            <p><strong class="accent-text">VOLT GEAR</strong><br>
            Email: privacy@voltgear.com<br>
            Phone: (555) 123-4567<br>
            Address: 123 Tech Boulevard, Gaming District, VG 12345</p>
        </section>
        
        <p class="last-updated">Last Updated: April 7, 2025</p>
        
        <a href="contact.html" class="contact-button">Contact Us</a>
    </main>
    
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="catagories.php">Categories</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="ourservices.php">Our Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
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

    <!-- JavaScript for theme toggle and mobile menu -->
    <script src="volt-assistant.js">
        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        const sunIcon = document.querySelector('.sun-icon');
        const moonIcon = document.querySelector('.moon-icon');
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.body.getAttribute('data-theme');
            if (currentTheme === 'dark') {
                document.body.setAttribute('data-theme', 'light');
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            } else {
                document.body.setAttribute('data-theme', 'dark');
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            }
        });
        
        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const mainNav = document.getElementById('main-nav');
        
        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('active');
        });
    </script>
</body>
</html>