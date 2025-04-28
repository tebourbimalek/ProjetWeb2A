<?php
session_start();

// Include the new file with the SQL functions
require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php';

// Usage of the function
$utilisateur_id = 8;
$playlist_created = createPlaylist($utilisateur_id);

if ($playlist_created) {
    $_SESSION['message'] = "Ajouter à la bibliothéque!";
    header("Location: avec_connexion.php?playlist_created=true");  // Pass the flag in the URL
    exit;
} else {
    $_SESSION['error'] = "Erreur : impossible d'ajouter la playlist.";
    header("Location: avec_connexion.php?error=true");
    exit;
}
?>
