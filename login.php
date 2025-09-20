<?php 
// Include database connection
include 'connect.php';

// Initialize error message variable
$error_message = '';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required.";
    } else {
        // Prepare SQL statement to find the user by email
        $sql = "SELECT customerID, customername, email, pass, `type` FROM customer WHERE email = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // Bind parameters and execute
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Check if user exists and verify password
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Verify password using password_verify
                if (password_verify($password, $user['pass'])) {
                    // Password is correct, start a new session
                    session_start();
                    
                    // Store user data in session variables
                    $_SESSION['customerID'] = $user['customerID'];
                    $_SESSION['customername'] = $user['customername'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['type'] = $user['type'];
                    $_SESSION['loggedin'] = true;
                    
                    // Set remember me cookie if checked
                    if ($remember) {
                        // Generate a token and store it in the database (more secure)
                        $token = bin2hex(random_bytes(32));
                        
                        // Set cookie with token - expires in 30 days
                        setcookie("remember_token", $token, time() + (86400 * 30), "/");
                        
                        // Here you would also store this token in your database with the user
                        // This code is simplified - in production, you would add the token to a user_tokens table
                        // UPDATE user_tokens SET token = ? WHERE user_id = ?
                    }
                    
                    // Redirect to home page or dashboard
                    header("Location: index.php");
                    exit();
                } else {
                    // Password is incorrect
                    $error_message = "Invalid email or password.";
                }
            } else {
                // No user found with this email
                $error_message = "Invalid email or password.";
            }
            
            $stmt->close();
        } else {
            $error_message = "Database error. Please try again later.";
        }
    }
    // After preparing the SQL statement
if (!$stmt) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
}

// After execution

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VOLT GEAR</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        
        /* Error message styles */
        .error-message {
            background-color: rgba(255, 0, 0, 0.1);
            border-left: 4px solid #ff3333;
            color: #ff6666;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0 5px 5px 0;
        }
        
        /* Login section */
        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 20px 40px;
        }
        
        .login-container {
            background-color: #121212;
            border-radius: 15px;
            width: 100%;
            max-width: 450px;
            padding: 3rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5), 
                        0 0 30px rgba(0, 255, 255, 0.1),
                        0 0 20px rgba(138, 43, 226, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-header h2 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #cccccc;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #ffffff;
        }
        
        .form-input {
            width: 100%;
            padding: 1rem;
            background-color: #1e1e1e;
            border: 1px solid #333;
            border-radius: 5px;
            color: #ffffff;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-input:focus {
            border-color: #00FFFF;
            outline: none;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.2);
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 0.5rem;
            accent-color: #8A2BE2;
        }
        
        .forgot-password {
            color: #00FFFF;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .forgot-password:hover {
            color: #8A2BE2;
        }
        
        .login-button {
            display: block;
            width: 100%;
            background: linear-gradient(90deg, #00FFFF, #8A2BE2);
            color: white;
            text-decoration: none;
            padding: 1rem;
            border-radius: 5px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            cursor: pointer;
            text-align: center;
            text-transform: uppercase;
        }
        
        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 255, 255, 0.2);
        }
        
        .social-login {
            margin-top: 2rem;
            text-align: center;
        }
        
        .social-login p {
            color: #cccccc;
            margin-bottom: 1rem;
            position: relative;
        }
        
        .social-login p::before,
        .social-login p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 35%;
            height: 1px;
            background-color: #333;
        }
        
        .social-login p::before {
            left: 0;
        }
        
        .social-login p::after {
            right: 0;
        }
        
        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .social-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #1e1e1e;
            border-radius: 50%;
            transition: background-color 0.3s;
            font-size: 1.2rem;
            color: #ffffff;
            text-decoration: none;
        }
        
        .social-button:hover {
            background-color: #8A2BE2;
        }
        
        .signup-link {
            margin-top: 2rem;
            text-align: center;
            color: #cccccc;
        }
        
        .signup-link a {
            color: #00FFFF;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .signup-link a:hover {
            color: #8A2BE2;
        }

        /* Footer */
        footer {
            background-color: #121212;
            padding: 2rem 5%;
            margin-top: auto;
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-bottom {
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
            text-decoration: none;
            color: #ffffff;
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
            
            .login-container {
                padding: 2rem;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
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
        </div>
    </header>
    
    <!-- Login section -->
    <section class="login-section">
        <div class="login-container">
            <div class="login-header">
                <h2>Log In</h2>
                <p>Sign in to your VOLT GEAR account</p>
            </div>
            
            <?php
            // Display error message if there is one
            if (!empty($error_message)) {
                echo '<div class="error-message">' . $error_message . '</div>';
            }
            ?>
            
            <form id="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" <?php echo isset($remember) && $remember ? 'checked' : ''; ?>>
                        <label for="remember">Remember me</label>
                    </div>
                    
                    <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="login-button">Log In</button>
            </form>
            
            <div class="social-login">
                <p>Or login with</p>
                <div class="social-buttons">
                    <a href="#" class="social-button">f</a>
                    <a href="#" class="social-button">G</a>
                    <a href="#" class="social-button">in</a>
                </div>
            </div>
            
            <div class="signup-link">
                Don't have an account? <a href="registration.php">Sign Up</a>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-bottom">
                <div class="copyright">© 2025 VOLT GEAR. All Rights Reserved.</div>
                <div class="social-icons">
                    <a href="#" class="social-icon">f</a>
                    <a href="#" class="social-icon">t</a>
                    <a href="#" class="social-icon">i</a>
                    <a href="#" class="social-icon">y</a>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        // Input focus effects
        const inputs = document.querySelectorAll('.form-input');
        
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.style.borderColor = '#00FFFF';
                input.style.boxShadow = '0 0 10px rgba(0, 255, 255, 0.2)';
            });
            
            input.addEventListener('blur', () => {
                if(!input.value) {
                    input.style.borderColor = '#333';
                    input.style.boxShadow = 'none';
                }
            });
        });
        
        // Button hover animation
        const loginButton = document.querySelector('.login-button');
        
        if(loginButton) {
            loginButton.addEventListener('mouseenter', () => {
                loginButton.style.transform = 'translateY(-3px)';
                loginButton.style.boxShadow = '0 10px 20px rgba(0, 255, 255, 0.2)';
            });
            
            loginButton.addEventListener('mouseleave', () => {
                loginButton.style.transform = '';
                loginButton.style.boxShadow = '';
            });
        }
    </script>
</body>
</html>