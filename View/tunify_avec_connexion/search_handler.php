<?php
session_start();

require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

$pdo = config::getConnexion();
// Usage of the function
$user = getUserInfo($pdo);


$user_id = $user->getArtisteId();

if (isset($_GET['query'])) {
    $query = $_GET['query'];

    try {
        $results = [
            'utilisateurs_artiste'=> searchUtilisateursartiste($query),
            'utilisateurs_user'   => searchUtilisateursuser($query),
            'chanson'             => searchChansons($query),
            'playlist'            => searchPlaylists($query, $user_id)
        ];

        header('Content-Type: application/json');
        echo json_encode($results);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Query parameter is required']);
}

?>