<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

$pdo = config::getConnexion();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    if (updateUserSettings()) {
        header("Location: ../backoffice.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour.";
    }
}

?>
