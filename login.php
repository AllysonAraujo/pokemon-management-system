<?php
require_once 'config/database.php';

startSecureSession();

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error_message = 'Por favor, preencha todos os campos.';
    } else {
        try {
            $db = getDatabase();
            $conn = $db->getConnection();
            
            $stmt = $conn->prepare("SELECT id, username, email, password_hash FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && verifyPassword($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = 'Nome de usuário ou senha inválidos.';
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error_message = 'Ocorreu um erro. Tente novamente.';
        }
    }
}

// Check for registration success message
if (isset($_SESSION['registration_success'])) {
    $success_message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Pokémon Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/150.png" alt="Mewtwo" class="auth-pokemon">
                <h1>Bem-vindo de Volta, Treinador!</h1>
                <p>Entre para acessar sua coleção de Pokémon</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label for="username">Nome de Usuário</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Entrar</button>
            </form>

            <div class="auth-links">
                <p>Não tem uma conta? <a href="register.php">Registre-se aqui</a></p>
                <p><a href="index.php">← Voltar ao Início</a></p>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>