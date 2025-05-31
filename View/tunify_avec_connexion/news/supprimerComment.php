<?php
session_start();  // Start the session so you can use $_SESSION variables

// Include the necessary controller
require_once 'C:/xampp/htdocs/projetweb/controlleur/CommentsController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['comment_message_delete'] = "Erreur: Méthode non autorisée";
    $_SESSION['comment_type_delete'] = "error";
    header('Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php');
    exit();
}

try {
    // Validate the comment ID
    $id = filter_input(INPUT_POST, 'id_commentaire', FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['comment_message_delete'] = "Erreur: ID de commentaire invalide";
        $_SESSION['comment_type_delete'] = "error";
        header('Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php');
        exit();
    }

    // Create an instance of the controller and delete the comment
    $controller = new CommentsController();
    $controller->deleteComment($id);

    // Set session variables for success message (can be used in the client-side for notifications)
    $_SESSION['comment_message_delete'] = "✅ Commentaire supprimé avec succès.";
    $_SESSION['comment_type_delete'] = "success";

    // Redirect to a page with a success message
    header('Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php');
    exit();

} catch (Exception $e) {
    // Handle exceptions by setting the error message in session and redirecting
    $_SESSION['comment_message_delete'] = "Erreur: " . $e->getMessage();
    $_SESSION['comment_type_delete'] = "error";
    header('Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php');
    exit();
}
?>
