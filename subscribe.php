<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOLT GEAR - Upgrade Your Gaming Experience</title>
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
        
        /* Subscription page specific styles */
        .subscription-hero {
            padding-top: 120px;
            padding-bottom: 4rem;
            text-align: center;
            background: radial-gradient(circle at 50% 50%, rgba(138, 43, 226, 0.2), transparent 70%),
                        radial-gradient(circle at 80% 20%, rgba(0, 255, 255, 0.2), transparent 60%);
        }
        
        .subscription-hero h2 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }
        
        .subscription-hero p {
            max-width: 700px;
            margin: 0 auto 2rem;
            color: #cccccc;
            font-size: 1.2rem;
            line-height: 1.6;
        }
        
        .subscription-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 5%;
        }
        
        .plans-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 4rem;
        }
        
        .plan-card {
            background-color: #121212;
            border-radius: 15px;
            padding: 2.5rem;
            width: 350px;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 255, 255, 0.15);
        }
        
        .popular-badge {
            position: absolute;
            top: -15px;
            right: 20px;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .plan-name {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .plan-price {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .plan-price span {
            font-size: 1rem;
            font-weight: normal;
            opacity: 0.7;
        }
        
        .plan-billing {
            color: #00FFFF;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .plan-features {
            list-style: none;
            margin-bottom: 2rem;
        }
        
        .plan-features li {
            margin-bottom: 1rem;
            padding-left: 1.8rem;
            position: relative;
            line-height: 1.4;
        }
        
        .plan-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #00FFFF;
            font-weight: bold;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            color: white;
            text-decoration: none;
            padding: 1rem 0;
            width: 100%;
            text-align: center;
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
        
        .outlined-button {
            display: inline-block;
            background: transparent;
            color: white;
            text-decoration: none;
            padding: 0.95rem 0;
            width: 100%;
            text-align: center;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
            border: 2px solid #8A2BE2;
            cursor: pointer;
        }
        
        .outlined-button:hover {
            background-color: rgba(138, 43, 226, 0.2);
            transform: translateY(-5px);
        }
        
        /* Benefits section */
        .benefits-section {
            padding: 5rem 0;
            background-color: #0f0f0f;
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
        
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .benefit-card {
            background-color: #121212;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s;
        }
        
        .benefit-card:hover {
            transform: translateY(-5px);
        }
        
        .benefit-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #00FFFF, #8A2BE2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .benefit-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .benefit-description {
            color: #cccccc;
            line-height: 1.6;
        }
        
        /* FAQ section */
        .faq-section {
            padding: 5rem 0;
        }
        
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 1.5rem;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .faq-item {
            background-color: #121212;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .faq-question {
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid #1e1e1e;
        }
        
        .faq-question h4 {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .faq-answer {
            padding: 1.5rem;
            color: #cccccc;
            line-height: 1.6;
        }
        
        /* Testimonials section */
        .testimonials-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, rgba(0, 255, 255, 0.1), rgba(138, 43, 226, 0.1));
        }
        
        .testimonials-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .testimonial-card {
            background-color: #121212;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: 20px;
            font-size: 6rem;
            color: #8A2BE2;
            opacity: 0.5;
            line-height: 1;
        }
        
        .testimonial-text {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            font-style: italic;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #8A2BE2;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .author-info h5 {
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }
        
        .author-info p {
            color: #00FFFF;
            font-size: 0.9rem;
        }
        
        /* CTA section */
        .cta-section {
            padding: 5rem 0;
            text-align: center;
        }
        
        .cta-container {
            max-width: 700px;
            margin: 0 auto;
        }
        
        .cta-section h3 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        
        .cta-section p {
            color: #cccccc;
            margin-bottom: 2rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .toggle-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3rem;
        }
        
        .toggle-option {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            margin: 0 15px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #333;
            transition: .4s;
            border-radius: 34px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: #8A2BE2;
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        .save-tag {
            background-color: #00FFFF;
            color: #121212;
            font-size: 0.8rem;
            font-weight: bold;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            margin-left: 10px;
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
            .faq-grid {
                grid-template-columns: 1fr;
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
            
            .subscription-hero h2 {
                font-size: 2.5rem;
            }
            
            .plan-card {
                width: 100%;
            }
            
            .plans-container {
                flex-direction: column;
                align-items: center;
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
                <a href="index.html" style="text-decoration: none; display: flex; align-items: center; color: inherit;">
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
                    <li><a href="catagories.php">Categories</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="ourservices.php">Our Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            
            <button class="cart-icon">🛒<span class="cart-count">3</span></button>
            
            <button class="mobile-menu-toggle" id="menu-toggle">☰</button>
        </div>
    </header>
    
    <!-- Subscription Hero Section -->
    <section class="subscription-hero">
        <div class="subscription-container">
            <h2>LEVEL UP YOUR <span style="color: #00FFFF;">GAMING</span> <span style="color: #8A2BE2;">EXPERIENCE</span></h2>
            <p>Join VOLT GEAR Elite and unlock exclusive benefits, early access to new products, and special discounts on premium gaming gear.</p>
            
            <!-- Billing Toggle -->
            <div class="toggle-container">
                <span class="toggle-option">Monthly</span>
                <label class="toggle-switch">
                    <input type="checkbox" id="billing-toggle">
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-option">Annual</span>
                <span class="save-tag">SAVE 20%</span>
            </div>
        </div>
    </section>
    
    <!-- Subscription Plans -->
    <section class="subscription-plans">
        <div class="subscription-container">
            <div class="plans-container">
                <!-- Basic Plan -->
                <div class="plan-card">
                    <h3 class="plan-name">VOLT Basic</h3>
                    <div class="plan-price">$9.99 <span>/month</span></div>
                    <div class="plan-billing">Billed monthly</div>
                    <ul class="plan-features">
                        <li>5% discount on all products</li>
                        <li>Free standard shipping</li>
                        <li>Member-only sales</li>
                        <li>Monthly digital gaming guide</li>
                        <li>Basic customer support</li>
                    </ul>
                    <a href="#" class="outlined-button">Choose Plan</a>
                </div>
                
                <!-- Pro Plan -->
                <div class="plan-card">
                    <div class="popular-badge">MOST POPULAR</div>
                    <h3 class="plan-name">VOLT Pro</h3>
                    <div class="plan-price">$19.99 <span>/month</span></div>
                    <div class="plan-billing">Billed monthly</div>
                    <ul class="plan-features">
                        <li>10% discount on all products</li>
                        <li>Free expedited shipping</li>
                        <li>Early access to new releases (48hrs)</li>
                        <li>Exclusive Pro-only products</li>
                        <li>Monthly gear box ($25 value)</li>
                        <li>Priority customer support</li>
                    </ul>
                    <a href="#" class="cta-button">Choose Plan</a>
                </div>
                
                <!-- Elite Plan -->
                <div class="plan-card">
                    <h3 class="plan-name">VOLT Elite</h3>
                    <div class="plan-price">$39.99 <span>/month</span></div>
                    <div class="plan-billing">Billed monthly</div>
                    <ul class="plan-features">
                        <li>15% discount on all products</li>
                        <li>Free premium shipping worldwide</li>
                        <li>Early access to new releases (1 week)</li>
                        <li>Exclusive Elite-only products</li>
                        <li>Premium monthly gear box ($50 value)</li>
                        <li>Personalized gear recommendations</li>
                        <li>Dedicated account manager</li>
                        <li>Quarterly pro gaming workshop access</li>
                    </ul>
                    <a href="#" class="outlined-button">Choose Plan</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="subscription-container">
            <div class="section-header">
                <h3>Membership Benefits</h3>
                <p>Join the VOLT GEAR community and enjoy these exclusive perks.</p>
            </div>
            
            <div class="benefits-grid">
                <!-- Benefit 1 -->
                <div class="benefit-card">
                    <div class="benefit-icon">🎮</div>
                    <h4 class="benefit-title">Early Access</h4>
                    <p class="benefit-description">Be the first to try our latest gaming gear before it's available to the public.</p>
                </div>
                
                <!-- Benefit 2 -->
                <div class="benefit-card">
                    <div class="benefit-icon">🏆</div>
                    <h4 class="benefit-title">Pro Workshops</h4>
                    <p class="benefit-description">Exclusive online workshops with professional esports players and gaming experts.</p>
                </div>
                
                <!-- Benefit 3 -->
                <div class="benefit-card">
                    <div class="benefit-icon">🎁</div>
                    <h4 class="benefit-title">Monthly Gear Box</h4>
                    <p class="benefit-description">Receive a curated box of gaming accessories and gear every month.</p>
                </div>
                
                <!-- Benefit 4 -->
                <div class="benefit-card">
                    <div class="benefit-icon">💰</div>
                    <h4 class="benefit-title">Exclusive Discounts</h4>
                    <p class="benefit-description">Save on every purchase with member-only discounts and special offers.</p>
                </div>
                
                <!-- Benefit 5 -->
                <div class="benefit-card">
                    <div class="benefit-icon">🚚</div>
                    <h4 class="benefit-title">Premium Shipping</h4>
                    <p class="benefit-description">Enjoy free expedited shipping on all your orders.</p>
                </div>
                
                <!-- Benefit 6 -->
                <div class="benefit-card">
                    <div class="benefit-icon">👤</div>
                    <h4 class="benefit-title">Priority Support</h4>
                    <p class="benefit-description">Get fast, personalized customer service from our gaming experts.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="subscription-container">
            <div class="section-header">
                <h3>What Our Members Say</h3>
                <p>Join thousands of gamers who have elevated their gaming experience with VOLT GEAR.</p>
            </div>
            
            <div class="testimonials-container">
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <p class="testimonial-text">The Pro subscription has completely changed my gaming setup. The monthly gear boxes are always filled with premium quality accessories, and the early access to new products means I'm always ahead of my competition.</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">MK</div>
                        <div class="author-info">
                            <h5>Mike Kennedy</h5>
                            <p>Pro Member · 1 Year</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="testimonial-card">
                    <p class="testimonial-text">As a professional streamer, having the latest gear is essential. The Elite membership gives me access to products a week before they launch, and the dedicated account manager helps me find exactly what I need for my setup.</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">SG</div>
                        <div class="author-info">
                            <h5>Sarah Green</h5>
                            <p>Elite Member · 2 Years</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="subscription-container">
            <div class="section-header">
                <h3>Frequently Asked Questions</h3>
                <p>Everything you need to know about VOLT GEAR memberships.</p>
            </div>
            
            <div class="faq-grid">
                <!-- FAQ Item 1 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>How does the monthly gear box work?</h4>
                        <span>+</span>
                    </div>
                    <div class="faq-answer">
                        Each month, our team curates a selection of premium gaming accessories and gear based on your preferences and gaming style. The box is shipped automatically to your door at no additional cost beyond your membership fee.
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>Can I cancel my subscription anytime?</h4>
                        <span>+</span>
                    </div>
                    <div class="faq-answer">
                        Yes, you can cancel your subscription at any time. There are no cancellation fees or hidden charges. Your benefits will continue until the end of your current billing period.
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>How do I redeem my member discounts?</h4>
                        <span>+</span>
                    </div>
                    <div class="faq-answer">
                        Your member discount is automatically applied to all products when you're logged into your account. You'll see the discounted prices displayed on product pages and at checkout.
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>Is the subscription available internationally?</h4>
                        <span>+</span>
                    </div>
                    <div class="faq-answer">
                        Yes, VOLT GEAR subscriptions are available worldwide. International members receive all the same benefits, including free shipping on orders based on your membership tier.
                    </div>
                </div>
                
                <!-- FAQ Item 5 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>What happens if I want to upgrade my plan?</h4>
                        <span>+</span>
                    </div>
                    <div class="faq-answer">
                        You can upgrade your plan at any time through your account settings. The new rate will be prorated for the remainder of your billing cycle, and you'll immediately gain access to all the benefits of your new tier.
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-container">
            <h3>Ready to <span style="color: #00FFFF;">Level Up</span> Your Gaming?</h3>
            <p>Join VOLT GEAR today and start enjoying exclusive member benefits tailored for serious gamers.</p>
            <a href="#" class="cta-button">Become a Member</a>
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
   <script src="AIassistant.js">
        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menu-toggle');
        const mainNav = document.getElementById('main-nav');
        
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
        
        // Billing Toggle
        const billingToggle = document.getElementById('billing-toggle');
        const planPrices = document.querySelectorAll('.plan-price');
        const planBilling = document.querySelectorAll('.plan-billing');
        
        // Original monthly prices
        const monthlyPrices = ['$9.99', '$19.99', '$39.99'];
        // Annual prices (20% discount)
        const annualPrices = ['$95.90', '$191.90', '$383.90'];
        
        billingToggle.addEventListener('change', function() {
            if (this.checked) {
                // Annual billing
                planPrices.forEach((price, index) => {
                    price.innerHTML = annualPrices[index] + ' <span>/year</span>';
                });
                planBilling.forEach(bill => {
                    bill.textContent = 'Billed annually';
                });
            } else {
                // Monthly billing
                planPrices.forEach((price, index) => {
                    price.innerHTML = monthlyPrices[index] + ' <span>/month</span>';
                });
                planBilling.forEach(bill => {
                    bill.textContent = 'Billed monthly';
                });
            }
        });
        
        // FAQ Toggle
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const isOpen = answer.style.display === 'block';
                
                // Close all FAQ answers
                document.querySelectorAll('.faq-answer').forEach(ans => {
                    ans.style.display = 'none';
                });
                
                document.querySelectorAll('.faq-question span').forEach(span => {
                    span.textContent = '+';
                });
                
                // Open current answer if it was not already open
                if (!isOpen) {
                    answer.style.display = 'block';
                    this.querySelector('span').textContent = '-';
                }
            });
        });
        
        // Initialize - hide all FAQ answers by default
        document.querySelectorAll('.faq-answer').forEach(answer => {
            answer.style.display = 'none';
        });
    </script>
</body>
</html>