<?php
require_once 'config/database.php';
startSecureSession();

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = 'Username and password are required.';
        } else {
            try {
                $database = new Database();
                $conn = $database->getConnection();
                
                // Find user by username or email
                $stmt = $conn->prepare("SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password_hash'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['login_time'] = time();
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $error = 'Invalid username or password.';
                    // Add a small delay to prevent timing attacks
                    usleep(500000); // 0.5 seconds
                }
                
                $database->closeConnection();
            } catch (Exception $e) {
                $error = 'Login failed. Please try again later.';
                error_log("Login error: " . $e->getMessage());
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
    <title>Login - Pokémon Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔥 Pokémon Management System</h1>
            <p>Login to access your Pokémon collection</p>
            <div class="nav">
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="register.php" class="btn-secondary">Register</a>
                </div>
            </div>
        </div>

        <div class="card" style="max-width: 500px; margin: 0 auto;">
            <h2>Welcome Back, Trainer!</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" onsubmit="return validateLoginForm()">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>"
                           placeholder="Enter your username or email">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Enter your password">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn" style="width: 100%; padding: 15px;">
                        Login to Dashboard
                    </button>
                </div>
            </form>
            
            <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                <p>Don't have an account? <a href="register.php" style="color: #ff6b6b; text-decoration: none; font-weight: 500;">Register here</a></p>
            </div>
            
            <!-- Demo credentials info -->
            <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-top: 20px;">
                <h3 style="color: #495057; font-size: 1rem; margin-bottom: 10px;">🎮 Demo Account</h3>
                <p style="margin: 0; font-size: 0.9rem; color: #6c757d;">
                    <strong>Username:</strong> admin<br>
                    <strong>Password:</strong> password
                </p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px; color: rgba(255, 255, 255, 0.8);">
            <p style="font-size: 0.9rem;">Secure login with password hashing and session protection 🔐</p>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        function validateLoginForm() {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username) {
                alert('Please enter your username or email.');
                document.getElementById('username').focus();
                return false;
            }
            
            if (!password) {
                alert('Please enter your password.');
                document.getElementById('password').focus();
                return false;
            }
            
            return true;
        }

        // Auto-focus on username field
        document.getElementById('username').focus();

        // Handle Enter key in password field
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });

        // Demo credentials quick fill (for development)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                document.getElementById('username').value = 'admin';
                document.getElementById('password').value = 'password';
                e.preventDefault();
            }
        });
    </script>
</body>
</html>