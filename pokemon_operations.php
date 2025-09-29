<?php
require_once 'config/database.php';

startSecureSession();
requireLogin();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $db = getDatabase();
    $conn = $db->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add':
                $pokemon_id = (int)$_POST['pokemon_id'];
                $pokemon_name = sanitizeInput($_POST['pokemon_name']);
                $pokemon_type1 = sanitizeInput($_POST['pokemon_type1']);
                $pokemon_type2 = sanitizeInput($_POST['pokemon_type2']);
                $pokemon_sprite = sanitizeInput($_POST['pokemon_sprite']);
                $pokemon_abilities = sanitizeInput($_POST['pokemon_abilities']);
                $nickname = sanitizeInput($_POST['nickname']);
                $level_caught = (int)$_POST['level_caught'];
                $notes = sanitizeInput($_POST['notes']);
                
                // Check if Pokemon already exists in user's collection
                $stmt = $conn->prepare("SELECT id FROM pokemon_collection WHERE user_id = ? AND pokemon_id = ?");
                $stmt->execute([$_SESSION['user_id'], $pokemon_id]);
                
                if ($stmt->fetch()) {
                    $response['message'] = 'Este Pokémon já está na sua coleção!';
                } else {
                    $stmt = $conn->prepare("
                        INSERT INTO pokemon_collection 
                        (user_id, pokemon_id, pokemon_name, pokemon_type1, pokemon_type2, pokemon_sprite, pokemon_abilities, nickname, level_caught, notes) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    if ($stmt->execute([$_SESSION['user_id'], $pokemon_id, $pokemon_name, $pokemon_type1, $pokemon_type2, $pokemon_sprite, $pokemon_abilities, $nickname, $level_caught, $notes])) {
                        $response['success'] = true;
                        $response['message'] = 'Pokémon adicionado à coleção com sucesso!';
                    } else {
                        $response['message'] = 'Falha ao adicionar Pokémon à coleção.';
                    }
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $nickname = sanitizeInput($_POST['nickname']);
                $level_caught = (int)$_POST['level_caught'];
                $notes = sanitizeInput($_POST['notes']);
                
                $stmt = $conn->prepare("
                    UPDATE pokemon_collection 
                    SET nickname = ?, level_caught = ?, notes = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ? AND user_id = ?
                ");
                
                if ($stmt->execute([$nickname, $level_caught, $notes, $id, $_SESSION['user_id']])) {
                    $response['success'] = true;
                    $response['message'] = 'Pokémon atualizado com sucesso!';
                } else {
                    $response['message'] = 'Falha ao atualizar Pokémon.';
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                
                $stmt = $conn->prepare("DELETE FROM pokemon_collection WHERE id = ? AND user_id = ?");
                
                if ($stmt->execute([$id, $_SESSION['user_id']])) {
                    $response['success'] = true;
                    $response['message'] = 'Pokémon removido da coleção.';
                } else {
                    $response['message'] = 'Falha ao remover Pokémon.';
                }
                break;
                
            case 'get':
                $id = (int)$_POST['id'];
                
                $stmt = $conn->prepare("SELECT * FROM pokemon_collection WHERE id = ? AND user_id = ?");
                $stmt->execute([$id, $_SESSION['user_id']]);
                $pokemon = $stmt->fetch();
                
                if ($pokemon) {
                    $response['success'] = true;
                    $response['pokemon'] = $pokemon;
                } else {
                    $response['message'] = 'Pokémon não encontrado.';
                }
                break;
                
            default:
                $response['message'] = 'Ação inválida.';
                break;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'search_pokeapi':
                $query = sanitizeInput($_GET['query']);
                if (empty($query)) {
                    $response['message'] = 'Consulta de busca é obrigatória.';
                    break;
                }
                
                // Fetch from PokeAPI
                $pokeapi_url = 'https://pokeapi.co/api/v2/pokemon/' . strtolower($query);
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'user_agent' => 'Pokemon Management System'
                    ]
                ]);
                
                $pokemon_data = @file_get_contents($pokeapi_url, false, $context);
                
                if ($pokemon_data === false) {
                    $response['message'] = 'Pokémon não encontrado na PokéAPI.';
                } else {
                    $pokemon_json = json_decode($pokemon_data, true);
                    
                    if ($pokemon_json) {
                        // Extract relevant data
                        $pokemon_info = [
                            'id' => $pokemon_json['id'],
                            'name' => $pokemon_json['name'],
                            'sprite' => $pokemon_json['sprites']['other']['official-artwork']['front_default'] ?? $pokemon_json['sprites']['front_default'],
                            'types' => array_map(function($type) {
                                return $type['type']['name'];
                            }, $pokemon_json['types']),
                            'abilities' => array_map(function($ability) {
                                return $ability['ability']['name'];
                            }, $pokemon_json['abilities'])
                        ];
                        
                        $response['success'] = true;
                        $response['pokemon'] = $pokemon_info;
                    } else {
                        $response['message'] = 'Resposta inválida da PokéAPI.';
                    }
                }
                break;
                
            default:
                $response['message'] = 'Ação inválida.';
                break;
        }
    } else {
        $response['message'] = 'Método de requisição inválido.';
    }
    
} catch (Exception $e) {
    error_log("Pokemon operations error: " . $e->getMessage());
    $response['message'] = 'Ocorreu um erro. Tente novamente.';
}

echo json_encode($response);
?>