<?php
session_start();

require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $song_id = $_POST['song_id'];
    $playlist_id = $_POST['playlist_id'];

    $response = deleteSongFromPlaylist($playlist_id, $song_id);
    
    
    if ($response) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }
    exit;
}
?>
