<?php
require_once 'config/database.php';
startSecureSession();
requireAuth();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
        throw new Exception('Invalid request method');
    }

    // Handle GET request for retrieving Pokemon data
    if (isset($_GET['action']) && $_GET['action'] === 'get') {
        $pokemonId = (int)($_GET['id'] ?? 0);
        
        if (!$pokemonId) {
            throw new Exception('Invalid Pokemon ID');
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM pokemon_collection WHERE id = ? AND user_id = ?");
        $stmt->execute([$pokemonId, $_SESSION['user_id']]);
        $pokemon = $stmt->fetch();
        
        if (!$pokemon) {
            throw new Exception('Pokemon not found');
        }
        
        $response['success'] = true;
        $response['data'] = $pokemon;
        $database->closeConnection();
        echo json_encode($response);
        exit();
    }

    // Handle POST requests (Add, Edit, Delete)
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Invalid security token');
    }

    $action = $_POST['action'] ?? '';
    $database = new Database();
    $conn = $database->getConnection();

    switch ($action) {
        case 'add':
            $pokemonName = strtolower(trim($_POST['pokemon_name'] ?? ''));
            $nickname = sanitizeInput($_POST['nickname'] ?? '');
            $level = (int)($_POST['level'] ?? 1);
            $notes = sanitizeInput($_POST['notes'] ?? '');
            
            // These will be set by JavaScript after PokeAPI call
            $pokemonId = (int)($_POST['pokemon_id'] ?? 0);
            $pokemonSprite = $_POST['pokemon_sprite'] ?? '';
            $pokemonTypes = $_POST['pokemon_types'] ?? '';
            $pokemonAbilities = $_POST['pokemon_abilities'] ?? '';
            
            // Validation
            if (empty($pokemonName)) {
                throw new Exception('Pokemon name is required');
            }
            
            if ($level < 1 || $level > 100) {
                throw new Exception('Level must be between 1 and 100');
            }
            
            if (!$pokemonId) {
                throw new Exception('Pokemon data not found. Please try again.');
            }
            
            // Check if user already has this Pokemon (allow duplicates but warn)
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM pokemon_collection WHERE user_id = ? AND pokemon_id = ? AND pokemon_name = ?");
            $checkStmt->execute([$_SESSION['user_id'], $pokemonId, $pokemonName]);
            $existingCount = $checkStmt->fetchColumn();
            
            // Insert new Pokemon
            $stmt = $conn->prepare("
                INSERT INTO pokemon_collection (
                    user_id, pokemon_id, pokemon_name, nickname, level, 
                    pokemon_sprite, pokemon_types, pokemon_abilities, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $success = $stmt->execute([
                $_SESSION['user_id'],
                $pokemonId,
                $pokemonName,
                $nickname ?: null,
                $level,
                $pokemonSprite,
                $pokemonTypes,
                $pokemonAbilities,
                $notes ?: null
            ]);
            
            if ($success) {
                $message = 'Pokemon added to your collection!';
                if ($existingCount > 0) {
                    $message .= ' (You now have ' . ($existingCount + 1) . ' of this Pokemon)';
                }
                $response['success'] = true;
                $response['message'] = $message;
            } else {
                throw new Exception('Failed to add Pokemon to collection');
            }
            break;

        case 'edit':
            $pokemonCollectionId = (int)($_POST['pokemon_id'] ?? 0);
            $nickname = sanitizeInput($_POST['nickname'] ?? '');
            $level = (int)($_POST['level'] ?? 1);
            $notes = sanitizeInput($_POST['notes'] ?? '');
            
            if (!$pokemonCollectionId) {
                throw new Exception('Invalid Pokemon ID');
            }
            
            if ($level < 1 || $level > 100) {
                throw new Exception('Level must be between 1 and 100');
            }
            
            // Verify the Pokemon belongs to the current user
            $verifyStmt = $conn->prepare("SELECT id FROM pokemon_collection WHERE id = ? AND user_id = ?");
            $verifyStmt->execute([$pokemonCollectionId, $_SESSION['user_id']]);
            
            if (!$verifyStmt->fetch()) {
                throw new Exception('Pokemon not found or access denied');
            }
            
            // Update Pokemon
            $stmt = $conn->prepare("
                UPDATE pokemon_collection 
                SET nickname = ?, level = ?, notes = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND user_id = ?
            ");
            
            $success = $stmt->execute([
                $nickname ?: null,
                $level,
                $notes ?: null,
                $pokemonCollectionId,
                $_SESSION['user_id']
            ]);
            
            if ($success) {
                $response['success'] = true;
                $response['message'] = 'Pokemon updated successfully!';
            } else {
                throw new Exception('Failed to update Pokemon');
            }
            break;

        case 'delete':
            $pokemonCollectionId = (int)($_POST['pokemon_id'] ?? 0);
            
            if (!$pokemonCollectionId) {
                throw new Exception('Invalid Pokemon ID');
            }
            
            // Verify the Pokemon belongs to the current user and get Pokemon name for confirmation
            $verifyStmt = $conn->prepare("SELECT pokemon_name, nickname FROM pokemon_collection WHERE id = ? AND user_id = ?");
            $verifyStmt->execute([$pokemonCollectionId, $_SESSION['user_id']]);
            $pokemonData = $verifyStmt->fetch();
            
            if (!$pokemonData) {
                throw new Exception('Pokemon not found or access denied');
            }
            
            // Delete Pokemon
            $stmt = $conn->prepare("DELETE FROM pokemon_collection WHERE id = ? AND user_id = ?");
            $success = $stmt->execute([$pokemonCollectionId, $_SESSION['user_id']]);
            
            if ($success) {
                $pokemonDisplayName = $pokemonData['nickname'] ?: ucfirst($pokemonData['pokemon_name']);
                $response['success'] = true;
                $response['message'] = $pokemonDisplayName . ' has been released from your collection.';
            } else {
                throw new Exception('Failed to delete Pokemon');
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
    
    $database->closeConnection();
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Pokemon operations error: " . $e->getMessage());
}

echo json_encode($response);
?>