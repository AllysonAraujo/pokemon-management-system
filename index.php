<?php
require_once 'config/database.php';
startSecureSession();

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔥 Pokémon Management System</h1>
            <p>Manage your Pokémon collection with real-time data from PokéAPI</p>
            <div class="nav">
                <div class="nav-links">
                    <a href="login.php">Login</a>
                    <a href="register.php" class="btn-secondary">Register</a>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Welcome to Your Pokémon Journey!</h2>
            <p>Start building and managing your ultimate Pokémon collection. Our system features:</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                <div style="background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin-bottom: 10px;">🔐 Secure Authentication</h3>
                    <p style="margin: 0; font-size: 0.9rem;">Password hashing, session management, and protected routes for your security.</p>
                </div>
                
                <div style="background: linear-gradient(135deg, #4834d4, #686de0); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin-bottom: 10px;">📱 Responsive Design</h3>
                    <p style="margin: 0; font-size: 0.9rem;">Beautiful, mobile-friendly interface with Pokémon-themed styling.</p>
                </div>
                
                <div style="background: linear-gradient(135deg, #00d2d3, #54a0ff); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin-bottom: 10px;">🎯 Real-time Data</h3>
                    <p style="margin: 0; font-size: 0.9rem;">Live integration with PokéAPI for sprites, types, and abilities.</p>
                </div>
                
                <div style="background: linear-gradient(135deg, #5f27cd, #00d2d3); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin-bottom: 10px;">💾 Full CRUD</h3>
                    <p style="margin: 0; font-size: 0.9rem;">Add, view, edit, and delete Pokémon from your collection with ease.</p>
                </div>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <p style="font-size: 1.1rem; margin-bottom: 20px;">Ready to become a Pokémon Master?</p>
                <a href="register.php" class="btn" style="margin-right: 10px; font-size: 1.1rem; padding: 15px 30px;">Start Your Journey</a>
                <a href="login.php" class="btn btn-secondary" style="font-size: 1.1rem; padding: 15px 30px;">I Have an Account</a>
            </div>
        </div>

        <div class="card">
            <h2>System Features</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div>
                    <h3 style="color: #ff6b6b; margin-bottom: 15px;">🎮 Collection Management</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="padding: 8px 0; border-bottom: 1px solid #eee;">✅ Add Pokémon to your collection</li>
                        <li style="padding: 8px 0; border-bottom: 1px solid #eee;">✅ Set custom nicknames and levels</li>
                        <li style="padding: 8px 0; border-bottom: 1px solid #eee;">✅ Add personal notes</li>
                        <li style="padding: 8px 0; border-bottom: 1px solid #eee;">✅ Track catch dates</li>
                        <li style="padding: 8px 0;">✅ Edit and remove Pokémon</li>
                    </ul>
                </div>
                
                <div>
                    <h3 style="color: #4834d4; margin-bottom: 15px;">🔗 API Integration</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="padding: 8px 0; border-bottom: 1px solid #eee;">🎨 Automatic sprite fetching</li>
                        <li style="padding: 8px 0; border-bottom: 1px solid #eee;">🏷️ Type information display</li>
                        <li style="padding: 8px 0; border-bottom: 1px solid #eee;">⚡ Ability listings</li>
                        <li style="padding: 8px 0; border-bottom: 1px solid #eee;">🔍 Real-time search</li>
                        <li style="padding: 8px 0;">📊 Detailed Pokémon stats</li>
                    </ul>
                </div>
            </div>
        </div>

        <footer style="text-align: center; margin-top: 40px; color: rgba(255, 255, 255, 0.8);">
            <p>&copy; 2024 Pokémon Management System. Built with PHP, MySQL, and ❤️</p>
            <p style="font-size: 0.9rem; margin-top: 10px;">Powered by <a href="https://pokeapi.co/" target="_blank" style="color: #ff6b6b;">PokéAPI</a></p>
        </footer>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>