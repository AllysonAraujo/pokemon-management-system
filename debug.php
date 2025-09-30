<?php
require_once 'config/database.php';

startSecureSession();

// Debug complete - check session, database connection, and data
echo "<h2>Debug do Sistema Pokemon</h2>";

// 1. Check session
echo "<h3>1. Informações da Sessão:</h3>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NÃO DEFINIDO') . "\n";
echo "Username: " . ($_SESSION['username'] ?? 'NÃO DEFINIDO') . "\n";
echo "Email: " . ($_SESSION['email'] ?? 'NÃO DEFINIDO') . "\n";
echo "Todas as variáveis de sessão:\n";
print_r($_SESSION);
echo "</pre>";

// 2. Check database connection
echo "<h3>2. Conexão com Banco de Dados:</h3>";
try {
    $db = getDatabase();
    $conn = $db->getConnection();
    echo "<p style='color: green;'>✅ Conexão com banco estabelecida com sucesso!</p>";
    
    // 3. Check if users table exists and has data
    echo "<h3>3. Verificar Tabela Users:</h3>";
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    $userCount = $stmt->fetch();
    echo "<p>Total de usuários na tabela: " . $userCount['total'] . "</p>";
    
    // Show all users
    $stmt = $conn->query("SELECT id, username, email FROM users");
    $users = $stmt->fetchAll();
    echo "<h4>Usuários cadastrados:</h4>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th></tr>";
    foreach ($users as $user) {
        echo "<tr><td>" . $user['id'] . "</td><td>" . $user['username'] . "</td><td>" . $user['email'] . "</td></tr>";
    }
    echo "</table>";
    
    // 4. Check pokemon_collection table
    echo "<h3>4. Verificar Tabela Pokemon Collection:</h3>";
    $stmt = $conn->query("SELECT COUNT(*) as total FROM pokemon_collection");
    $pokemonCount = $stmt->fetch();
    echo "<p>Total de Pokémon na tabela: " . $pokemonCount['total'] . "</p>";
    
    // Show all pokemon with user info
    $stmt = $conn->query("
        SELECT pc.*, u.username 
        FROM pokemon_collection pc 
        JOIN users u ON pc.user_id = u.id 
        ORDER BY pc.user_id, pc.created_at DESC
    ");
    $allPokemon = $stmt->fetchAll();
    
    echo "<h4>Todos os Pokémon cadastrados:</h4>";
    if (empty($allPokemon)) {
        echo "<p style='color: red;'>❌ Nenhum Pokémon encontrado na tabela!</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Username</th><th>Pokemon Name</th><th>Nickname</th><th>Level</th><th>Created At</th></tr>";
        foreach ($allPokemon as $pokemon) {
            echo "<tr>";
            echo "<td>" . $pokemon['id'] . "</td>";
            echo "<td>" . $pokemon['user_id'] . "</td>";
            echo "<td>" . $pokemon['username'] . "</td>";
            echo "<td>" . $pokemon['pokemon_name'] . "</td>";
            echo "<td>" . ($pokemon['nickname'] ?: '(sem nickname)') . "</td>";
            echo "<td>" . $pokemon['level_caught'] . "</td>";
            echo "<td>" . $pokemon['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 5. Check specific user pokemon if logged in
    if (isset($_SESSION['user_id'])) {
        echo "<h3>5. Pokémon do Usuário Logado (ID: " . $_SESSION['user_id'] . "):</h3>";
        
        $stmt = $conn->prepare("
            SELECT pc.*
            FROM pokemon_collection pc 
            WHERE pc.user_id = ? 
            ORDER BY pc.created_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $userPokemon = $stmt->fetchAll();
        
        if (empty($userPokemon)) {
            echo "<p style='color: red;'>❌ Nenhum Pokémon encontrado para este usuário!</p>";
            
            // Debug the query manually
            echo "<h4>Debug da Query:</h4>";
            $stmt = $conn->prepare("SELECT * FROM pokemon_collection WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $debugPokemon = $stmt->fetchAll();
            echo "<pre>";
            echo "Resultado da query direta:\n";
            print_r($debugPokemon);
            echo "</pre>";
        } else {
            echo "<p style='color: green;'>✅ Encontrados " . count($userPokemon) . " Pokémon para este usuário!</p>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Pokemon Name</th><th>Nickname</th><th>Level</th><th>Created At</th></tr>";
            foreach ($userPokemon as $pokemon) {
                echo "<tr>";
                echo "<td>" . $pokemon['id'] . "</td>";
                echo "<td>" . $pokemon['pokemon_name'] . "</td>";
                echo "<td>" . ($pokemon['nickname'] ?: '(sem nickname)') . "</td>";
                echo "<td>" . $pokemon['level_caught'] . "</td>";
                echo "<td>" . $pokemon['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<h3>5. ⚠️ Usuário não está logado - faça login primeiro!</h3>";
        echo "<p><a href='login.php'>Ir para Login</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao conectar com banco: " . $e->getMessage() . "</p>";
}

// 6. Check PHP configuration
echo "<h3>6. Configuração PHP:</h3>";
echo "<p>Session Cookie Lifetime: " . ini_get('session.cookie_lifetime') . "</p>";
echo "<p>Session GC Maxlifetime: " . ini_get('session.gc_maxlifetime') . "</p>";
echo "<p>Session Save Path: " . session_save_path() . "</p>";

echo "<hr>";
echo "<p><a href='dashboard.php'>Voltar ao Dashboard</a> | <a href='logout.php'>Logout</a></p>";
?>