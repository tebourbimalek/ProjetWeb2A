<?php
session_start();

require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php'; 
require_once 'C:\xampp\htdocs\projetweb\model\config.php'; // your PDO setup
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';




$pdo = config::getConnexion();
$user = getUserInfo($pdo);


$user_id = $user->getArtisteId();
if (isset($_POST['song_idd'])) {
    $songId = $_POST['song_idd'];
    if ($songId !== null) {
        updateStreamStats($songId, $pdo ,$user_id);
    }
}
?>
