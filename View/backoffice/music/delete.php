<?php 
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function.php';
require_once 'C:\xampp\htdocs\projetweb\model\classe.php';

$pdo = config::getConnexion();

if (isset($_GET['song_id'])) {
    $songId = $_GET['song_id'];
    $song = getSongPaths($pdo, $songId);

    if ($song) {
        // Delete music file
        if (!empty($song['music_path']) && file_exists($song['music_path'])) {
            unlink($song['music_path']);
        }
        // Delete image file
        if (!empty($song['image_path']) && file_exists($song['image_path'])) {
            unlink($song['image_path']);
        }

        // Delete song from database
        if (deleteSong($pdo, $songId)) {
            $_SESSION['message'] = "Chanson supprimée avec succès !";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression.";
        }
    } else {
        $_SESSION['message'] = "Chanson introuvable.";
    }

    // Redirect to backoffice
    header("Location: ../backoffice.php");
    exit;
}
?>
