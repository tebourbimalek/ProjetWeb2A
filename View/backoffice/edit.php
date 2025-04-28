<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function.php';
require_once 'C:\xampp\htdocs\projetweb\model\classe.php';

try {
    $pdo = config::getConnexion();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $song_id = $_POST['song_id'] ?? null;
        $song_title = $_POST['edit-song-title'] ?? null;
        $album_name = $_POST['edit-song-album'] ?? null;
        $release_date = $_POST['temps'] ?? null;
        $duree = $_POST['edit-duree'] ?? null;

        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/projetweb/assets/includes/";

        // Fetch current song paths
        $song = getSongById($pdo, $song_id);

        if (!$song) {
            $_SESSION['message'] = "Chanson introuvable.";
            header("Location: backoffice.php?error=true");
            exit;
        }

        // Handle new music upload
        if (isset($_FILES['music_input']) && $_FILES['music_input']['error'] === 0) {
            $audio_file = $_FILES['music_input'];
            $new_audio_path = $upload_dir . basename($audio_file["name"]);

            if (!empty($song['music_path']) && file_exists($song['music_path'])) {
                unlink($song['music_path']);
            }

            if (!move_uploaded_file($audio_file["tmp_name"], $new_audio_path)) {
                $new_audio_path = $song['music_path'];
            }
        } else {
            $new_audio_path = $song['music_path'];
        }

        // Handle new image upload
        if (isset($_FILES['image_input']) && $_FILES['image_input']['error'] === 0) {
            $cover_file = $_FILES['image_input'];
            $new_cover_path = $upload_dir . basename($cover_file["name"]);

            if (!empty($song['image_path']) && file_exists($song['image_path'])) {
                unlink($song['image_path']);
            }

            if (!move_uploaded_file($cover_file["tmp_name"], $new_cover_path)) {
                $new_cover_path = $song['image_path'];
            }
        } else {
            $new_cover_path = $song['image_path'];
        }

        // Update song details in the database
        if (updateSong($pdo, $song_id, $song_title, $album_name, $release_date, $duree, $new_audio_path, $new_cover_path)) {
            $_SESSION['message'] = "Chanson mise à jour avec succès !";
            header("Location: backoffice.php?updated=true");
            exit;
        } else {
            $_SESSION['message'] = "Échec de la mise à jour de la chanson.";
            header("Location: backoffice.php?error=true");
            exit;
        }
    }

} catch (Exception $e) {
    $_SESSION['message'] = $e->getMessage();
    header("Location: backoffice.php?error=true");
    exit;
}
?>
