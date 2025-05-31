<?php 
session_start();

require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $song_id = $_POST['song_id'];
    $user_id = $_POST['user_id']; // Corrected key name from 'playlist_id' to 'user_id'

    // Corrected variable name from $songId to $song_id
    $response = deleteSongFromlikedsong($user_id, $song_id);
    
    if ($response) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }
    exit;
}
?>
