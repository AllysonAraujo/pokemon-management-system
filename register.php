<?php
require_once 'config/database.php';

startSecureSession();

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Por favor, preencha todos os campos.';
    } elseif (!validateEmail($email)) {
        $error_message = 'Por favor, insira um endereço de e-mail válido.';
    } elseif (strlen($username) < 3) {
        $error_message = 'O nome de usuário deve ter pelo menos 3 caracteres.';
    } elseif (strlen($password) < 6) {
        $error_message = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'As senhas não coincidem.';
    } else {
        try {
            $db = getDatabase();
            $conn = $db->getConnection();
            
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $error_message = 'Nome de usuário ou e-mail já existe.';
            } else {
                // Create new user
                $password_hash = hashPassword($password);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                
                if ($stmt->execute([$username, $email, $password_hash])) {
                    $_SESSION['registration_success'] = 'Registro realizado com sucesso! Faça o login.';
                    header('Location: login.php');
                    exit();
                } else {
                    $error_message = 'Falha no registro. Tente novamente.';
                }
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error_message = 'Ocorreu um erro. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar - Pokémon Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/151.png" alt="Mew" class="auth-pokemon">
                <h1>Junte-se à Aventura!</h1>
                <p>Crie sua conta para começar a construir sua coleção de Pokémon</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label for="username">Nome de Usuário</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    <small class="form-help">Pelo menos 3 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required>
                    <small class="form-help">Pelo menos 6 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Senha</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Registrar</button>
            </form>

            <div class="auth-links">
                <p>Já tem uma conta? <a href="login.php">Entre aqui</a></p>
                <p><a href="index.php">← Voltar ao Início</a></p>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>