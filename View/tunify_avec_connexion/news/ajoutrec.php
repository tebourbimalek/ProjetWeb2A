<?php 
session_start(); // Start session

// Include necessary files
require_once 'C:\xampp\htdocs\projetweb\controlleur\functionsnews.php';
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

// Database connection
$pdo = config::getConnexion();

// Get user information
$user = getUserInfo($pdo);
$user_id = $user->getArtisteId(); // Assuming the user has an 'ArtisteId'

// Check if form was submitted and 'action' is set as 'reaction'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reaction') {

    // Sanitize and retrieve form data
    $id_news = (int)$_POST['id_news']; 

    // Check if the user has already reacted to this news
    $query = "SELECT * FROM reactions WHERE id_news = :id_news AND id_user = :id_user";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_news', $id_news, PDO::PARAM_INT);
    $stmt->bindParam(':id_user', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // If the user has already reacted to this news item
    if ($stmt->rowCount() > 0) {
        $_SESSION['reaction_message'] = "❗ Vous avez déjà réagi à cette actualité.";
        $_SESSION['reaction_type'] = "error";
        header("Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php?id_news=$id_news");  
        exit;
    } else {
        // Add new reaction (simply mark as reacted)
        $id = ajouterReaction($pdo, $id_news, $user_id);

        if ($id) {
            // Success
            $_SESSION['reaction_message'] = "✅ Réaction ajoutée avec succès.";
            $_SESSION['reaction_type'] = "success";
        } else {
            // Error adding reaction
            $_SESSION['reaction_message'] = "❌ Erreur lors de l'ajout de la réaction.";
            $_SESSION['reaction_type'] = "error";
        }

        // Redirect to the news page with the reaction results
        header("Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php?id_news=$id_news");  
        exit;
    }
}
?>
