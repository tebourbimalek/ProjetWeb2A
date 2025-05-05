<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::connect();
    $stmt = $db->query("
        SELECT id_game, nom_jeu, type_jeu 
        FROM jeux 
        WHERE statut = 'actif'
        LIMIT 50
    ");
    
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug output
    file_put_contents('debug.log', print_r($games, true));
    
    echo json_encode($games);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>