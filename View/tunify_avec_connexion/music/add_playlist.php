<?php
session_start();

// Include the new file with the SQL functions
require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
// Usage of the function
$pdo = config::getConnexion();

$user = getUserInfo($pdo);


$utilisateur_id = $user->getArtisteId();
$playlist_created = createPlaylist($utilisateur_id);

if ($playlist_created) {
    $_SESSION['message'] = "Ajouter à la bibliothéque!";
    header("Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php?playlist_created=true");  // Pass the flag in the URL
    exit;
} else {
    $_SESSION['error'] = "Erreur : impossible d'ajouter la playlist.";
    header("Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php?error=true");
    exit;
}
?>
