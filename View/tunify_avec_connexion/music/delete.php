<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php'; // Make sure this includes the delete function

$id = $_POST['id_delete'] ?? null; // Get the ID from the POST data

if (deletePlaylist($id)) {
    $_SESSION['message'] = "Playlist supprimée avec succès.";
} else {
    $_SESSION['error'] = "Erreur lors de la suppression de la playlist.";
}

// Correct the redirect path using forward slashes
header('Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php');

exit;
?>
