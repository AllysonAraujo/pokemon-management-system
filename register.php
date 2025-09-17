<?php
require_once 'config/database.php';
startSecureSession();

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $error = 'All fields are required.';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $error = 'Username must be between 3 and 50 characters.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            // Check if username or email already exists
            try {
                $database = new Database();
                $conn = $database->getConnection();
                
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $error = 'Username or email already exists.';
                } else {
                    // Create new user
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                    
                    if ($stmt->execute([$username, $email, $password_hash])) {
                        $success = 'Registration successful! You can now login.';
                        // Clear form data
                        $username = $email = '';
                    } else {
                        $error = 'Registration failed. Please try again.';
                    }
                }
                
                $database->closeConnection();
            } catch (Exception $e) {
                $error = 'Database error. Please try again later.';
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Pokémon Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔥 Pokémon Management System</h1>
            <p>Create your account to start your Pokémon journey</p>
            <div class="nav">
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="login.php" class="btn-secondary">Login</a>
                </div>
            </div>
        </div>

        <div class="card" style="max-width: 500px; margin: 0 auto;">
            <h2>Create Your Account</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <br><br>
                    <a href="login.php" class="btn">Go to Login</a>
                </div>
            <?php else: ?>
                <form method="POST" action="" onsubmit="return validateRegistrationForm()">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo htmlspecialchars($username ?? ''); ?>"
                               minlength="3" maxlength="50" 
                               pattern="[a-zA-Z0-9_]+" 
                               title="Username can only contain letters, numbers, and underscores">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required 
                               minlength="6" title="Password must be at least 6 characters long">
                        <small style="color: #666; font-size: 0.8rem;">Minimum 6 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               minlength="6">
                        <small id="passwordMatch" style="font-size: 0.8rem;"></small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn" style="width: 100%; padding: 15px;">
                            Create Account
                        </button>
                    </div>
                </form>
                
                <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <p>Already have an account? <a href="login.php" style="color: #ff6b6b; text-decoration: none; font-weight: 500;">Login here</a></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px; color: rgba(255, 255, 255, 0.8);">
            <p style="font-size: 0.9rem;">By creating an account, you agree to manage your Pokémon collection responsibly 🎮</p>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Real-time password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    matchText.textContent = '✅ Passwords match';
                    matchText.style.color = '#28a745';
                } else {
                    matchText.textContent = '❌ Passwords do not match';
                    matchText.style.color = '#dc3545';
                }
            } else {
                matchText.textContent = '';
            }
        });

        function validateRegistrationForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                alert('Password must be at least 6 characters long!');
                return false;
            }
            
            return true;
        }

        // Username validation feedback
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            const validPattern = /^[a-zA-Z0-9_]+$/;
            
            if (username.length > 0 && !validPattern.test(username)) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#ddd';
            }
        });
    </script>
</body>
</html>