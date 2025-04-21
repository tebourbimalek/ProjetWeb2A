<?php

require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\Model\controller\controller.php';

$pdo = config::getConnexion();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    if (updateUserSettings()) {
        header("Location: /projetweb/View/pages/tunify_avec_connexion/avec_connexion.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour.";
    }
}

?>
