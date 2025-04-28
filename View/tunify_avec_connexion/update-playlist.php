<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php';

$playlistId = $_POST['id_playlist'] ?? null;
$nom_playlist = $_POST['nom_playlist'] ?? '';
$description_playlist = $_POST['description_playlist'] ?? '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Vérifie si un fichier est envoyé
        $fileArray = isset($_FILES['cover_image']) ? $_FILES['cover_image'] : null;

        // Appel de la fonction mise à jour avec image, nom et description
        $success = updatePlaylistImage($playlistId, $fileArray, $nom_playlist, $description_playlist);

        if ($success) {
            $_SESSION['message'] = "Playlist mise à jour avec succès !";
        } else {
            $_SESSION['error'] = "Échec de la mise à jour de la playlist.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
    }

    header('Location: avec_connexion.php');
    exit;
}
?>
