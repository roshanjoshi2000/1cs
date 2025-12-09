<?php
// Start session
session_start();

// Database connection - MUST BE THE SAME AS YOUR login.php
$mysqli = new mysqli("localhost", "2402361", "University7623", "db2402361");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// CAPTCHA Functions (same as login)
function generate_captcha() {
    $captcha = rand(1000, 9999);
    $_SESSION['captcha_code'] = $captcha;
    $_SESSION['captcha_time'] = time();
    return $captcha;
}

function verify_captcha($input) {
    if (!isset($_SESSION['captcha_code']) || !isset($_SESSION['captcha_time'])) {
        return false;
    }
    
    // CAPTCHA expires after 5 minutes
    if (time() - $_SESSION['captcha_time'] > 300) {
        unset($_SESSION['captcha_code']);
        unset($_SESSION['captcha_time']);
        return false;
    }
    
    $is_valid = ($_SESSION['captcha_code'] == $input);
    
    // ONE-TIME USE: Clear after verification attempt
    unset($_SESSION['captcha_code']);
    unset($_SESSION['captcha_time']);
    
    return $is_valid;
}

// Handle form submission
$message = "";
$display_captcha = "";
$form_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $captcha_input = $_POST['captcha'] ?? '';
    
    // Store form data for repopulation
    $form_data = [
        'username' => $username,
        'email' => $email
    ];
    
    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($captcha_input)) {
        $message = "All fields are required!";
        $display_captcha = generate_captcha();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
        $display_captcha = generate_captcha();
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters!";
        $display_captcha = generate_captcha();
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $display_captcha = generate_captcha();
    } elseif (strlen($username) < 3 || strlen($username) > 100) {
        $message = "Username must be between 3 and 100 characters!";
        $display_captcha = generate_captcha();
    } else {
        // Verify CAPTCHA first
        if (!verify_captcha($captcha_input)) {
            $message = "Invalid CAPTCHA! Please try again.";
            $display_captcha = generate_captcha();
        } else {
            // Check if email already exists
            $check_email = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
            if ($check_email) {
                $check_email->bind_param("s", $email);
                $check_email->execute();
                $check_email->store_result();
                
                if ($check_email->num_rows > 0) {
                    $message = "Email is already registered!";
                    $display_captcha = generate_captcha();
                } else {
                    // Check if username already exists
                    $check_username = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
                    $check_username->bind_param("s", $username);
                    $check_username->execute();
                    $check_username->store_result();
                    
                    if ($check_username->num_rows > 0) {
                        $message = "Username is already taken!";
                        $display_captcha = generate_captcha();
                    } else {
                        // Hash password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Insert new user - MATCHING YOUR DATABASE STRUCTURE
                        $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                        if ($stmt) {
                            $stmt->bind_param("sss", $username, $email, $hashed_password);
                            
                            if ($stmt->execute()) {
                                // Get the new user ID
                                $new_user_id = $stmt->insert_id;
                                
                                // Auto-login after registration
                                $_SESSION['user_id'] = $new_user_id;
                                $_SESSION['username'] = $username;
                                $_SESSION['logged_in'] = true;
                                
                                // Clear CAPTCHA data
                                unset($_SESSION['captcha_code']);
                                unset($_SESSION['captcha_time']);
                                
                                // Redirect to home page
                                header("Location: Index.php");
                                exit();
                            } else {
                                $message = "Registration failed. Error: " . $stmt->error;
                                $display_captcha = generate_captcha();
                            }
                            $stmt->close();
                        } else {
                            $message = "Database error! " . $mysqli->error;
                            $display_captcha = generate_captcha();
                        }
                    }
                    $check_username->close();
                }
                $check_email->close();
            } else {
                $message = "Database error! " . $mysqli->error;
                $display_captcha = generate_captcha();
            }
        }
    }
} else {
    // First page load - generate CAPTCHA
    $display_captcha = generate_captcha();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Game Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .captcha-display {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 10px;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            color: #333;
            font-family: monospace;
        }
        .btn-register {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px;
            font-size: 18px;
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 2px;
        }
        .strength-weak { background-color: #dc3545; width: 25%; }
        .strength-fair { background-color: #ffc107; width: 50%; }
        .strength-good { background-color: #17a2b8; width: 75%; }
        .strength-strong { background-color: #28a745; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card p-4">
                    <h2 class="text-center mb-4">📝 Create Account</h2>
                    
                    <?php if(!empty($message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="registerForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username *</label>
                                <input type="text" name="username" class="form-control form-control-lg" 
                                       placeholder="Choose a username" required 
                                       value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>"
                                       minlength="3" maxlength="100">
                                <div class="form-text">3-100 characters</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control form-control-lg" 
                                       placeholder="Enter your email" required 
                                       value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control form-control-lg" 
                                       placeholder="Create password" required minlength="6"
                                       id="passwordInput">
                                <div class="password-strength" id="passwordStrength"></div>
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="confirm_password" class="form-control form-control-lg" 
                                       placeholder="Confirm password" required minlength="6"
                                       id="confirmPasswordInput">
                                <div id="passwordMatch" class="form-text"></div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">CAPTCHA Verification *</label>
                            <p class="small text-muted mb-2">Enter the 4-digit code below:</p>
                            
                            <div class="captcha-display mb-3">
                                <?php echo $display_captcha; ?>
                            </div>
                            
                            <input type="text" name="captcha" class="form-control form-control-lg" 
                                   placeholder="Enter CAPTCHA code" required maxlength="4" 
                                   pattern="\d{4}" autocomplete="off" id="captchaInput">
                            <div class="form-text">
                                <small>Security verification. Code refreshes after each attempt.</small>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="termsCheck" required>
                            <label class="form-check-label" for="termsCheck">
                                I agree to the <a href="#" class="text-decoration-none">Terms & Conditions</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-register text-white w-100 mb-3">
                            Create Account
                        </button>
                        
                        <div class="text-center">
                            <p class="mb-0">
                                Already have an account? 
                                <a href="login.php" class="text-decoration-none fw-bold">Login here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('passwordInput');
        const passwordStrength = document.getElementById('passwordStrength');
        const confirmPasswordInput = document.getElementById('confirmPasswordInput');
        const passwordMatch = document.getElementById('passwordMatch');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Length check
            if (password.length >= 6) strength += 1;
            if (password.length >= 8) strength += 1;
            
            // Complexity checks
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Update strength indicator
            passwordStrength.className = 'password-strength';
            if (password.length === 0) {
                passwordStrength.style.width = '0%';
            } else if (strength <= 2) {
                passwordStrength.className += ' strength-weak';
                passwordMatch.textContent = 'Weak password';
                passwordMatch.style.color = '#dc3545';
            } else if (strength === 3) {
                passwordStrength.className += ' strength-fair';
                passwordMatch.textContent = 'Fair password';
                passwordMatch.style.color = '#ffc107';
            } else if (strength === 4) {
                passwordStrength.className += ' strength-good';
                passwordMatch.textContent = 'Good password';
                passwordMatch.style.color = '#17a2b8';
            } else {
                passwordStrength.className += ' strength-strong';
                passwordMatch.textContent = 'Strong password';
                passwordMatch.style.color = '#28a745';
            }
        });
        
        // Password confirmation check
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value === passwordInput.value) {
                passwordMatch.textContent = '✓ Passwords match';
                passwordMatch.style.color = '#28a745';
            } else if (this.value.length > 0) {
                passwordMatch.textContent = '✗ Passwords do not match';
                passwordMatch.style.color = '#dc3545';
            } else {
                passwordMatch.textContent = '';
            }
        });
        
        // CAPTCHA validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const captcha = document.getElementById('captchaInput');
            const captchaValue = captcha.value.trim();
            
            // Check if it's exactly 4 digits
            if (!/^\d{4}$/.test(captchaValue)) {
                e.preventDefault();
                alert('❌ Please enter exactly 4 digits (0-9) for CAPTCHA.');
                captcha.focus();
                captcha.select();
                return false;
            }
            
            // Check password match
            if (passwordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                alert('❌ Passwords do not match!');
                confirmPasswordInput.focus();
                return false;
            }
            
            // Check password length
            if (passwordInput.value.length < 6) {
                e.preventDefault();
                alert('❌ Password must be at least 6 characters!');
                passwordInput.focus();
                return false;
            }
            
            // Check terms agreement
            if (!document.getElementById('termsCheck').checked) {
                e.preventDefault();
                alert('❌ Please agree to the Terms & Conditions.');
                return false;
            }
            
            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = 'Creating Account...';
            submitBtn.disabled = true;
        });
        
        // Auto-focus first input
        document.querySelector('input[name="username"]').focus();
    </script>
</body>
</html>