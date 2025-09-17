<?php
require_once 'config/database.php';

startSecureSession();

// Redirect to dashboard if already logged in
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
</head>
<body class="landing-page">
    <div class="container">
        <header class="hero-section">
            <div class="hero-content">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png" alt="Pikachu" class="hero-pokemon">
                <h1 class="hero-title">Pokémon Management System</h1>
                <p class="hero-subtitle">Organize and manage your Pokémon collection like a true trainer!</p>
            </div>
        </header>

        <main class="main-content">
            <div class="features-section">
                <h2>Features</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🔐</div>
                        <h3>Secure Authentication</h3>
                        <p>Safe and secure user registration and login system</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📱</div>
                        <h3>Pokémon Collection</h3>
                        <p>Add, edit, and manage your personal Pokémon collection</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔍</div>
                        <h3>PokéAPI Integration</h3>
                        <p>Access real Pokémon data, sprites, and information</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h3>Collection Stats</h3>
                        <p>Track your collection progress and statistics</p>
                    </div>
                </div>
            </div>

            <div class="cta-section">
                <h2>Start Your Journey</h2>
                <p>Join thousands of trainers managing their Pokémon collections</p>
                <div class="cta-buttons">
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                </div>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; 2024 Pokémon Management System. Built with ❤️ for trainers everywhere.</p>
            <p>Pokémon data provided by <a href="https://pokeapi.co/" target="_blank">PokéAPI</a></p>
        </footer>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>