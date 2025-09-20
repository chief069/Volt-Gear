<?php
// Include database connection
include 'connect.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $customerName = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';
    $type = isset($_POST['gamer-type']) ? $_POST['gamer-type'] : '';
    
    // Validate inputs
    $errors = [];
    
    // Validate name
    if (empty($customerName)) {
        $errors[] = "Full name is required";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // Check if passwords match
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    
    // Validate gamer type
    if (empty($type)) {
        $errors[] = "Gamer type is required";
    }
    
    // If no errors, insert the data into the database
    if (empty($errors)) {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and execute the SQL statement
        $sql = "INSERT INTO customer (customername, email, pass, type) VALUES (?, ?, ?, ?)";
        
        // Using prepared statements to prevent SQL injection
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssss", $customerName, $email, $hashedPassword, $type);
            
            if ($stmt->execute()) {
                // Registration successful
                // Redirect to a success page or login page
                header("Location: login.php");
                exit();
            } else {
                // Error occurred
                $errors[] = "Error: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $errors[] = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VOLT GEAR - Sign Up</title>
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

        /* Sign up section */
        .signup-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 20px 60px;
            position: relative;
        }
        
        .signup-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 70% 30%, rgba(138, 43, 226, 0.15), transparent 60%),
                        radial-gradient(circle at 30% 70%, rgba(0, 255, 255, 0.15), transparent 60%);
            z-index: 0;
        }
        
        .signup-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background-color: rgba(18, 18, 18, 0.8);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 40px rgba(0, 255, 255, 0.2);
            z-index: 1;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .signup-image {
            flex: 1;
            background-image: url("/api/placeholder/600/1000");
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .signup-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 255, 255, 0.3), rgba(138, 43, 226, 0.3));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            text-align: center;
        }
        
        .signup-image-overlay h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        
        .signup-image-overlay p {
            font-size: 1.2rem;
            max-width: 400px;
            margin: 0 auto;
            color: #ffffff;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        
        .signup-form-container {
            flex: 1;
            padding: 3rem;
        }
        
        .signup-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .signup-header h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .signup-header p {
            color: #cccccc;
            font-size: 1.1rem;
        }
        
        .signup-form {
            max-width: 450px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #f0f0f0;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid #333;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #00FFFF;
            box-shadow: 0 0 0 2px rgba(0, 255, 255, 0.25);
        }
        
        .form-group select {
            width: 100%;
            padding: 1rem;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid #333;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
        }
        
        .form-group select:focus {
            outline: none;
            border-color: #00FFFF;
            box-shadow: 0 0 0 2px rgba(0, 255, 255, 0.25);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .checkbox-group input {
            margin-right: 10px;
            width: 20px;
            height: 20px;
            accent-color: #8A2BE2;
        }

        .error-container {
            background-color: rgba(18, 18, 18, 0.8);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 0 40px rgba(0, 255, 255, 0.2);
            max-width: 600px;
            width: 90%;
        }
        
        .checkbox-group label {
            color: #cccccc;
            font-size: 0.9rem;
        }
        
        .checkbox-group label a {
            color: #00FFFF;
            text-decoration: none;
        }
        
        .checkbox-group label a:hover {
            text-decoration: underline;
        }
        
        .signup-button {
            display: block;
            width: 100%;
            padding: 1rem;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }
        
        .signup-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 255, 255, 0.3);
        }
        
        .or-divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: #666;
        }
        
        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #333;
        }
        
        .or-divider span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }
        
        .social-signup {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .social-button {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid #333;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .social-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .signup-footer {
            text-align: center;
            margin-top: 2rem;
            color: #cccccc;
        }
        
        .signup-footer a {
            color: #00FFFF;
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-footer a:hover {
            text-decoration: underline;
        }
        
        /* Footer */
        footer {
            background-color: #121212;
            padding: 2rem 5%;
            text-align: center;
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .copyright {
            color: #999;
            font-size: 0.9rem;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .signup-container {
                flex-direction: column;
            }
            
            .signup-image {
                display: none;
            }
            
            .signup-form-container {
                padding: 2rem;
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
            
            .social-signup {
                flex-direction: column;
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
            
            
            <button class="mobile-menu-toggle" id="menu-toggle">☰</button>
        </div>
    </header>
    
    <!-- Sign up section -->
    <section class="signup-section">
        <div class="signup-bg"></div>
        <div class="signup-container">
            <div class="signup-image">
                <div class="signup-image-overlay">
                    <h2>JOIN THE ELITE</h2>
                    <p>Get exclusive access to premium gaming gear, special discounts, and early product releases.</p>
                </div>
            </div>
            <div class="signup-form-container">
                <div class="signup-header">
                    <h3>Create Your Account</h3>
                    <p>Join the VOLT GEAR community and level up your gaming experience.</p>
                </div>

                <?php
    // Display errors if any
    if (!empty($errors) && isset($errors)) {
        echo '<div class="error-container">';
        echo '<h1>Registration Error</h1>';
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
        echo '<a href="registration.php" class="button">Try Again</a>';
        echo '</div>';
    }
    ?>
                
                <form class="signup-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-group">
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required value="<?php echo isset($customerName) ? htmlspecialchars($customerName) : ''; ?>">
    </div>
    
    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
    </div>
    
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required>
    </div>
    
    <div class="form-group">
        <label for="confirm-password">Confirm Password</label>
        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
    </div>
    
    <div class="form-group">
        <label for="gamer-type">I am a...</label>
        <select id="gamer-type" name="gamer-type" required>
            <option value="" disabled selected>Select gamer type</option>
            <option value="casual" <?php echo (isset($type) && $type == 'casual') ? 'selected' : ''; ?>>Casual Gamer</option>
            <option value="competitive" <?php echo (isset($type) && $type == 'competitive') ? 'selected' : ''; ?>>Competitive Gamer</option>
            <option value="professional" <?php echo (isset($type) && $type == 'professional') ? 'selected' : ''; ?>>Professional E-Sports Player</option>
            <option value="content-creator" <?php echo (isset($type) && $type == 'content-creator') ? 'selected' : ''; ?>>Content Creator/Streamer</option>
        </select>
    </div>
    
    <div class="checkbox-group">
        <input type="checkbox" id="newsletter" name="newsletter" checked>
        <label for="newsletter">Subscribe to newsletter for exclusive deals and updates</label>
    </div>
    
    <div class="checkbox-group">
        <input type="checkbox" id="terms" name="terms" required>
        <label for="terms">I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a></label>
    </div>
    
    <button type="submit" class="signup-button">CREATE ACCOUNT</button>
    <div class="or-divider">
                        <span>OR</span>
                    </div>
                    
                    <div class="social-signup">
                        <button type="button" class="social-button">Sign up with Google</button>
                        <button type="button" class="social-button">Sign up with Discord</button>
                    </div>
                    
                    <div class="signup-footer">
                        Already have an account? <a href="login.html">Log In</a>
                    </div>
    <!-- Rest of the form remains the same -->
</form>
                   
               
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="copyright">© 2025 VOLT GEAR. All Rights Reserved.</div>
        </div>
    </footer>
    
    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const mainNav = document.getElementById('main-nav');
        
        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('active');
        });
        
        // Password validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm-password');
        const form = document.querySelector('.signup-form');
        
        // form.addEventListener('submit', function(e) {
        //     if(password.value !== confirmPassword.value) {
        //         e.preventDefault();
        //         confirmPassword.setCustomValidity('Passwords do not match');
        //         confirmPassword.reportValidity();
        //     } else {
        //         confirmPassword.setCustomValidity('');
        //     }
        // });
        
        // Form animation effects
        const formInputs = document.querySelectorAll('.form-group input, .form-group select');
        
        formInputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.style.borderColor = '#00FFFF';
                input.style.boxShadow = '0 0 0 2px rgba(0, 255, 255, 0.25)';
            });
            
            input.addEventListener('blur', () => {
                if(!input.value) {
                    input.style.borderColor = '#333';
                    input.style.boxShadow = 'none';
                }
            });
        });
        
        // Button animation
        const signupButton = document.querySelector('.signup-button');
        
        signupButton.addEventListener('mouseenter', () => {
            signupButton.style.transform = 'translateY(-3px)';
            signupButton.style.boxShadow = '0 10px 20px rgba(0, 255, 255, 0.3)';
        });
        
        signupButton.addEventListener('mouseleave', () => {
            signupButton.style.transform = '';
            signupButton.style.boxShadow = '';
        });
    </script>
</body>
</html>