<?php
require_once 'config/database.php';

startSecureSession();
requireLogin();

// Get user's Pokemon collection
$pokemon_collection = [];
$total_pokemon = 0;

try {
    $db = getDatabase();
    $conn = $db->getConnection();
    
    // Get collection with counts
    $stmt = $conn->prepare("
        SELECT pc.*, COUNT(*) OVER() as total_count 
        FROM pokemon_collection pc 
        WHERE pc.user_id = ? 
        ORDER BY pc.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $pokemon_collection = $stmt->fetchAll();
    
    if (!empty($pokemon_collection)) {
        $total_pokemon = $pokemon_collection[0]['total_count'];
    }
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Pokémon Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-page">
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png" alt="Logo" class="nav-logo">
                <span>PokéManager</span>
            </div>
            <div class="nav-user">
                <span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" class="btn btn-outline">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">
        <header class="dashboard-header">
            <h1>Sua Coleção de Pokémon</h1>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_pokemon; ?></div>
                    <div class="stat-label">Pokémon Capturados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_unique(array_column($pokemon_collection, 'pokemon_type1'))); ?></div>
                    <div class="stat-label">Tipos Diferentes</div>
                </div>
            </div>
        </header>

        <div class="dashboard-actions">
            <button id="add-pokemon-btn" class="btn btn-primary">
                <span>+</span> Adicionar Novo Pokémon
            </button>
            <div class="search-filter">
                <input type="text" id="search-pokemon" placeholder="Buscar na sua coleção...">
            </div>
        </div>

        <!-- Add Pokemon Modal -->
        <div id="add-pokemon-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Adicionar Pokémon à Coleção</h2>
                    <span class="close">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="search-form">
                        <div class="form-group">
                            <label for="pokemon-search">Buscar Pokémon (nome ou ID):</label>
                            <input type="text" id="pokemon-search" placeholder="ex: pikachu ou 25">
                            <button type="submit" class="btn btn-secondary">Buscar</button>
                        </div>
                    </form>
                    
                    <div id="pokemon-preview" class="pokemon-preview" style="display: none;">
                        <img id="preview-sprite" src="" alt="Pokemon Sprite">
                        <div class="pokemon-info">
                            <h3 id="preview-name"></h3>
                            <div id="preview-types"></div>
                            <div id="preview-abilities"></div>
                        </div>
                    </div>

                    <form id="add-pokemon-form" style="display: none;">
                        <input type="hidden" id="pokemon-id" name="pokemon_id">
                        <input type="hidden" id="pokemon-name" name="pokemon_name">
                        <input type="hidden" id="pokemon-type1" name="pokemon_type1">
                        <input type="hidden" id="pokemon-type2" name="pokemon_type2">
                        <input type="hidden" id="pokemon-sprite" name="pokemon_sprite">
                        <input type="hidden" id="pokemon-abilities" name="pokemon_abilities">
                        
                        <div class="form-group">
                            <label for="nickname">Apelido (opcional):</label>
                            <input type="text" id="nickname" name="nickname">
                        </div>
                        
                        <div class="form-group">
                            <label for="level_caught">Nível Capturado:</label>
                            <input type="number" id="level_caught" name="level_caught" min="1" max="100" value="1">
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Anotações (opcional):</label>
                            <textarea id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Adicionar à Coleção</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Pokemon Modal -->
        <div id="edit-pokemon-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Editar Pokémon</h2>
                    <span class="close">&times;</span>
                </div>
                <div class="modal-body">
                    <form id="edit-pokemon-form">
                        <input type="hidden" id="edit-id" name="id">
                        
                        <div class="form-group">
                            <label for="edit-nickname">Apelido:</label>
                            <input type="text" id="edit-nickname" name="nickname">
                        </div>
                        
                        <div class="form-group">
                            <label for="edit-level">Nível:</label>
                            <input type="number" id="edit-level" name="level_caught" min="1" max="100">
                        </div>
                        
                        <div class="form-group">
                            <label for="edit-notes">Anotações:</label>
                            <textarea id="edit-notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                            <button type="button" id="delete-pokemon-btn" class="btn btn-danger">Excluir</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <main class="pokemon-collection">
            <?php if (empty($pokemon_collection)): ?>
                <div class="empty-collection">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/54.png" alt="Psyduck" class="empty-pokemon">
                    <h3>Sua coleção está vazia!</h3>
                    <p>Comece adicionando seu primeiro Pokémon à coleção.</p>
                </div>
            <?php else: ?>
                <div class="pokemon-grid" id="pokemon-grid">
                    <?php foreach ($pokemon_collection as $pokemon): ?>
                        <div class="pokemon-card" data-pokemon-id="<?php echo $pokemon['pokemon_id']; ?>" data-name="<?php echo strtolower($pokemon['pokemon_name']); ?>">
                            <div class="pokemon-image">
                                <img src="<?php echo htmlspecialchars($pokemon['pokemon_sprite']); ?>" alt="<?php echo htmlspecialchars($pokemon['pokemon_name']); ?>">
                            </div>
                            <div class="pokemon-info">
                                <h3 class="pokemon-name">
                                    <?php echo !empty($pokemon['nickname']) ? htmlspecialchars($pokemon['nickname']) : htmlspecialchars(ucfirst($pokemon['pokemon_name'])); ?>
                                </h3>
                                <?php if (!empty($pokemon['nickname'])): ?>
                                    <p class="pokemon-species"><?php echo htmlspecialchars(ucfirst($pokemon['pokemon_name'])); ?></p>
                                <?php endif; ?>
                                <div class="pokemon-types">
                                    <span class="type type-<?php echo strtolower($pokemon['pokemon_type1']); ?>"><?php echo ucfirst($pokemon['pokemon_type1']); ?></span>
                                    <?php if (!empty($pokemon['pokemon_type2'])): ?>
                                        <span class="type type-<?php echo strtolower($pokemon['pokemon_type2']); ?>"><?php echo ucfirst($pokemon['pokemon_type2']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="pokemon-level">Level <?php echo $pokemon['level_caught']; ?></div>
                                <div class="pokemon-date">Caught: <?php echo date('M j, Y', strtotime($pokemon['date_caught'])); ?></div>
                            </div>
                            <div class="pokemon-actions">
                                <button class="btn btn-sm btn-outline edit-pokemon-btn" data-id="<?php echo $pokemon['id']; ?>">Edit</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>