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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="landing-page">
    <!-- Header de Navegação -->
    <header class="top-navigation">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png" alt="Logo" class="nav-logo-small">
                    <span class="logo-text">PokéManager</span>
                </div>
                <nav class="nav-links">
                    <a href="login.php" class="nav-link">Entrar</a>
                    <a href="register.php" class="nav-link nav-link-primary">Criar Conta</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <header class="hero-section">
            <div class="hero-content">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png" alt="Pikachu" class="hero-pokemon">
                <h1 class="hero-title">Pokémon Management System</h1>
                <p class="hero-subtitle">Organize e gerencie sua coleção de Pokémon como um verdadeiro treinador!</p>
            </div>
        </header>

        <main class="main-content">
            <div class="features-section">
                <h2>Funcionalidades</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🔐</div>
                        <h3>Autenticação Segura</h3>
                        <p>Sistema de registro e login seguro para usuários</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📱</div>
                        <h3>Coleção de Pokémon</h3>
                        <p>Adicione, edite e gerencie sua coleção pessoal de Pokémon</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔍</div>
                        <h3>PokéAPI Integration</h3>
                        <p>Acesse dados reais de Pokémon, sprites e informações</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h3>Estatísticas da Coleção</h3>
                        <p>Acompanhe o progresso e as estatísticas da sua coleção</p>
                    </div>
                </div>
            </div>

            <div class="cta-section">
                <h2>Comece sua Jornada</h2>
                <p>Junte-se a milhares de treinadores gerenciando suas coleções de Pokémon</p>
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-primary btn-cta">Criar Minha Conta</a>
                    <a href="login.php" class="btn btn-outline btn-cta">Já Tenho Conta</a>
                </div>
                <div class="cta-subtext">
                    <p><small>🎆 Grátis para sempre • 🔒 100% Seguro • ⚡ Acesso Instantâneo</small></p>
                </div>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; 2025 Pokémon Management System. Feito com ❤️ para treinadores do mundo todo.</p>
            <p>Dados de Pokémon fornecidos por <a href="https://pokeapi.co/" target="_blank">PokéAPI</a></p>
        </footer>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>