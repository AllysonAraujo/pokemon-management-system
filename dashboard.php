<?php
require_once 'config/database.php';
startSecureSession();
requireAuth();

// Get user's Pokemon collection
$pokemonCollection = [];
$totalPokemon = 0;

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Get total count
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM pokemon_collection WHERE user_id = ?");
    $countStmt->execute([$_SESSION['user_id']]);
    $totalPokemon = $countStmt->fetchColumn();
    
    // Get Pokemon collection
    $stmt = $conn->prepare("
        SELECT * FROM pokemon_collection 
        WHERE user_id = ? 
        ORDER BY caught_date DESC, pokemon_name ASC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $pokemonCollection = $stmt->fetchAll();
    
    $database->closeConnection();
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pokémon Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔥 Pokémon Management System</h1>
            <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
            <div class="nav">
                <div class="nav-links">
                    <button id="addPokemonBtn" class="btn">Add Pokémon</button>
                    <a href="?logout=1" class="btn btn-danger" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="card">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; text-align: center;">
                <div style="background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin: 0; font-size: 2rem;"><?php echo $totalPokemon; ?></h3>
                    <p style="margin: 5px 0 0 0;">Total Pokémon</p>
                </div>
                <div style="background: linear-gradient(135deg, #4834d4, #686de0); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin: 0; font-size: 2rem;"><?php echo $_SESSION['username']; ?></h3>
                    <p style="margin: 5px 0 0 0;">Trainer Name</p>
                </div>
                <div style="background: linear-gradient(135deg, #00d2d3, #54a0ff); color: white; padding: 20px; border-radius: 10px;">
                    <h3 style="margin: 0; font-size: 2rem;"><?php echo date('M Y', $_SESSION['login_time'] ?? time()); ?></h3>
                    <p style="margin: 5px 0 0 0;">Member Since</p>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="card">
            <h2>Your Pokémon Collection</h2>
            <div class="search-container">
                <input type="text" id="collectionSearch" class="search-input" placeholder="Search your collection by name or nickname...">
            </div>
        </div>

        <!-- Pokemon Collection -->
        <?php if (empty($pokemonCollection)): ?>
            <div class="card text-center">
                <h3 style="color: #666; margin-bottom: 20px;">Your collection is empty!</h3>
                <p style="color: #888; margin-bottom: 30px;">Start building your ultimate Pokémon team by adding your first Pokémon.</p>
                <button onclick="document.getElementById('addPokemonBtn').click()" class="btn" style="font-size: 1.1rem; padding: 15px 30px;">
                    Add Your First Pokémon
                </button>
            </div>
        <?php else: ?>
            <div class="pokemon-grid">
                <?php foreach ($pokemonCollection as $pokemon): ?>
                    <div class="pokemon-card">
                        <div class="pokemon-sprite">
                            <img src="<?php echo htmlspecialchars($pokemon['pokemon_sprite'] ?? 'https://via.placeholder.com/96x96?text=?'); ?>" 
                                 alt="<?php echo htmlspecialchars($pokemon['pokemon_name']); ?>" 
                                 onerror="this.src='https://via.placeholder.com/96x96?text=?'">
                        </div>
                        
                        <div class="pokemon-name">
                            <?php echo htmlspecialchars(ucfirst($pokemon['pokemon_name'])); ?>
                            <?php if ($pokemon['nickname']): ?>
                                <div class="pokemon-nickname" style="font-size: 0.9rem; font-weight: normal; opacity: 0.8;">
                                    "<?php echo htmlspecialchars($pokemon['nickname']); ?>"
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pokemon-info">
                            <p><strong>Level:</strong> <?php echo (int)$pokemon['level']; ?></p>
                            <p><strong>Caught:</strong> <?php echo date('M j, Y', strtotime($pokemon['caught_date'])); ?></p>
                            <?php if ($pokemon['notes']): ?>
                                <p><strong>Notes:</strong> <?php echo htmlspecialchars(substr($pokemon['notes'], 0, 50)); ?><?php echo strlen($pokemon['notes']) > 50 ? '...' : ''; ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <?php
                        $types = json_decode($pokemon['pokemon_types'] ?? '[]', true) ?: [];
                        if (!empty($types)):
                        ?>
                            <div class="pokemon-types">
                                <?php foreach ($types as $type): ?>
                                    <span class="type-badge"><?php echo htmlspecialchars($type); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="pokemon-actions">
                            <button class="btn btn-secondary edit-pokemon" data-id="<?php echo $pokemon['id']; ?>">
                                Edit
                            </button>
                            <button class="btn btn-danger delete-pokemon" data-id="<?php echo $pokemon['id']; ?>">
                                Release
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add Pokemon Modal -->
    <div id="addPokemonModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Pokémon</h2>
            <form id="addPokemonForm">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="pokemonSearch">Search Pokémon</label>
                    <input type="text" id="pokemonSearch" placeholder="Type Pokémon name to search..." autocomplete="off">
                    <div id="searchResults" class="hidden" style="background: white; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; max-height: 200px; overflow-y: auto;"></div>
                </div>
                
                <div class="form-group">
                    <label for="pokemon_name">Pokémon Name</label>
                    <input type="text" id="pokemon_name" name="pokemon_name" required placeholder="e.g., pikachu, charizard">
                </div>
                
                <div class="form-group">
                    <label for="nickname">Nickname (Optional)</label>
                    <input type="text" id="nickname" name="nickname" placeholder="Give your Pokémon a nickname">
                </div>
                
                <div class="form-group">
                    <label for="level">Level</label>
                    <input type="number" id="level" name="level" value="1" min="1" max="100" required>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes (Optional)</label>
                    <textarea id="notes" name="notes" placeholder="Any special notes about this Pokémon..."></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn" style="width: 100%;">Add to Collection</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Pokemon Modal -->
    <div id="editPokemonModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Pokémon</h2>
            <form id="editPokemonForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" id="edit_pokemon_id" name="pokemon_id">
                
                <div class="form-group">
                    <label for="edit_nickname">Nickname</label>
                    <input type="text" id="edit_nickname" name="nickname" placeholder="Give your Pokémon a nickname">
                </div>
                
                <div class="form-group">
                    <label for="edit_level">Level</label>
                    <input type="number" id="edit_level" name="level" min="1" max="100" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_notes">Notes</label>
                    <textarea id="edit_notes" name="notes" placeholder="Any special notes about this Pokémon..."></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn" style="width: 100%;">Update Pokémon</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    // Handle logout
    if (isset($_GET['logout'])) {
        logout();
        header('Location: index.php');
        exit();
    }
    ?>

    <script src="assets/js/main.js"></script>
    <script>
        // Add search result styling
        const style = document.createElement('style');
        style.textContent = `
            .search-result {
                display: flex;
                align-items: center;
                padding: 10px;
                cursor: pointer;
                border-bottom: 1px solid #eee;
                transition: background-color 0.2s;
            }
            .search-result:hover {
                background-color: #f8f9fa;
            }
            .search-result img {
                margin-right: 10px;
                border-radius: 8px;
            }
            .search-result:last-child {
                border-bottom: none;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>