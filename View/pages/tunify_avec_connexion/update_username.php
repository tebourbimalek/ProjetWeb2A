<?php
// Start the session
session_start();

// Include the config file
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';


// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
        exit;
    }

    $newUsername = trim($_POST['username']);

    if (empty($newUsername)) {
        echo json_encode(['success' => false, 'message' => 'Le nom d\'utilisateur ne peut pas être vide.']);
        exit;
    }

    // Récupération de l'ID artiste proprement
    $artiste_id = null;
    if (isset($_SESSION['user']['artiste_id'])) {
        $artiste_id = $_SESSION['user']['artiste_id'];
    }

    if (!$artiste_id) {
        echo json_encode(['success' => false, 'message' => 'Identifiant artiste introuvable.']);
        exit;
    }

    $pdo = config::getConnexion();

    $query = "UPDATE utilisateurs SET nom_utilisateur = :name WHERE artiste_id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $newUsername, PDO::PARAM_STR);
    $stmt->bindParam(':id', $artiste_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['user']['nom_utilisateur'] = $newUsername;

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du nom d\'utilisateur.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
}
?>
