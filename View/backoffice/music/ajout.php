<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function.php';
require_once 'C:\xampp\htdocs\projetweb\model\classe.php';

try {
    $pdo = config::getConnexion();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $song_title = $_POST['song-title'] ?? null;
        $album_name = $_POST['album-name'] ?? null;
        $release_date = $_POST['song-release'] ?? null;
        $duree = $_POST['duree'] ?? null;

        $audio_path = null;
        $cover_path = null;
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/projetweb/assets/includes/";

        // Check if song exists
        if (checkSongExists($pdo, $song_title) > 0) {
            $_SESSION['message'] = "Cette chanson existe déjà.";
            header('Location: ../backoffice.php');
            exit;
        }

        // Upload audio
        if (!empty($_FILES['music_input']['name'])) {
            $audio_file = $_FILES['music_input'];
            $audio_path = $upload_dir . basename($audio_file["name"]);
            if (!move_uploaded_file($audio_file["tmp_name"], $audio_path)) {
                $_SESSION['message'] = "Failed to move audio file.";
                header('Location: ../backoffice.php');
                exit;
            }
        } else {
            $_SESSION['message'] = "Audio file not selected.";
            header('Location: ../backoffice.php');
            exit;
        }

        // Upload cover image
        if (!empty($_FILES['image_input']['name'])) {
            $cover_file = $_FILES['image_input'];
            $cover_path = $upload_dir . basename($cover_file["name"]);
            if (!move_uploaded_file($cover_file["tmp_name"], $cover_path)) {
                $_SESSION['message'] = "Failed to move cover image.";
                header('Location: ../backoffice.php');
                exit;
            }
        } else {
            $_SESSION['message'] = "Cover image not selected.";
            header('Location: ../backoffice.php');
            exit;
        }

        // Insert song
        if (insertSong($pdo, $song_title, $album_name, $release_date, $cover_path, $audio_path, $duree)) {
            $_SESSION['message'] = "New song added successfully!";
        } else {
            $_SESSION['message'] = "Error: Could not insert data.";
        }

        header('Location: ../backoffice.php');
        exit;
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>
