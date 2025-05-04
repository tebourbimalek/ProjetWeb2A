<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\user.php';
require_once 'C:\xampp\htdocs\projetweb\controller\controller.php';

try {
    $pdo = config::getConnexion();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

$userConnected = getUserInfo($pdo); // Récupère l'utilisateur connecté

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = filter_input(INPUT_POST, 'nom_utilisateur', FILTER_SANITIZE_STRING);

    if (empty($new_username)) {
        $_SESSION['error_message'] = "Le nom d'utilisateur ne peut pas être vide.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom_utilisateur = ? WHERE artiste_id = ?");
            $stmt->execute([
                $new_username,
                $userConnected->getArtisteId()
            ]);

            $_SESSION['success_message'] = "Nom d'utilisateur mis à jour avec succès !";
            header('Location: avec_connexion.php');
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}
?>
