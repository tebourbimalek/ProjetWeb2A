<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/question.php';
session_start();

// Get game ID from URL
$id_game = isset($_GET['id_game']) ? (int)$_GET['id_game'] : 0;

// Validate game
$db = Database::connect();
$stmt = $db->prepare("SELECT * FROM jeux WHERE id_game = ? AND statut = 'actif'");
$stmt->execute([$id_game]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    die("Game not available");
}

// Get game type from the database record, not URL parameter
$game_type = $game['type_jeu'];

// Route to the appropriate game handler based on game type
switch ($game_type) {
    case 'quiz':
        require 'gamification/quiz_game.php';
        break;
    case 'puzzle':
        require 'gamification/puzzle_game.php';
        break;
    case 'guess':
        require 'gamification/guess_game.php';
        break;
    // Add more game types as needed
    default:
        die("Unknown game type: " . htmlspecialchars($game_type));
}