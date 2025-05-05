<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/update_errors.log');

try {
    // 1. Database connection
    require_once __DIR__.'/../config/database.php';
    $db = Database::connect();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Get recent games (jeux table)
    $games = $db->query("
        SELECT 
            'game' AS type,
            id_game AS id,
            nom_jeu AS name,
            type_jeu AS subtype,
            points_attribues AS points,
            statut AS status,
            cover_path AS image,
            DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS created_at
        FROM jeux
        WHERE statut = 'actif'
        ORDER BY id_game DESC
        LIMIT 3
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 3. Get recent questions (questions table)
    $questions = $db->query("
        SELECT 
            'question' AS type,
            id_question AS id,
            SUBSTRING(question_text, 1, 50) AS name,
            'quiz' AS subtype,
            NULL AS points,
            NULL AS status,
            image_path AS image,
            DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS created_at
        FROM questions
        WHERE is_true = 1
        ORDER BY id_question DESC
        LIMIT 4
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 4. Get recent rewards (recompenses table)
    $rewards = $db->query("
        SELECT 
            'reward' AS type,
            id_reward AS id,
            nom_reward AS name,
            type_reward AS subtype,
            points_requis AS points,
            CASE WHEN disponibilite = 1 THEN 'available' ELSE 'unavailable' END AS status,
            image_path AS image,
            DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS created_at
        FROM recompenses
        WHERE disponibilite = 1
        ORDER BY id_reward DESC
        LIMIT 3
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 5. Combine and sort all updates
    $allUpdates = array_merge($games, $questions, $rewards);
    usort($allUpdates, function($a, $b) {
        return $b['id'] - $a['id']; // Sort by ID descending
    });

    // 6. Prepare final response
    $response = [
        'success' => true,
        'data' => array_slice($allUpdates, 0, 10),
        'generated_at' => date('Y-m-d H:i:s')
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => $e->getMessage(),
        'query' => $e->getTrace()[0]['args'][0] ?? null
    ]);
}
?>