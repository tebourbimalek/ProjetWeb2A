<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function.php';
require_once 'C:\xampp\htdocs\projetweb\model\classe.php';

if (isset($_GET['song_id'])) {
    $pdo = config::getConnexion();
    $song_id = $_GET['song_id'];

    try {
        $song = getMusicPathById($pdo,$song_id);

        if ($song) {
            $file_path = $song['music_path'];
            if (file_exists($file_path)) {
                header('Content-Description: File Transfer');
                header('Content-Type: audio/mpeg');
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                header('Content-Length: ' . filesize($file_path));
                header('Pragma: no-cache');
                header('Expires: 0');

                ob_clean();
                flush();
                readfile($file_path);
                exit;
            } else {
                echo "The requested music file does not exist.";
            }
        } else {
            echo "Song not found.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No song ID specified.";
}
?>
