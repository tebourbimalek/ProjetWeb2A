<?php
header('Content-Type: application/json');
require_once 'C:\xampp\htdocs\projetweb\controlleur\CommentsController.php';
require_once 'C:\xampp\htdocs\projetweb\model\Comments.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

try {
    $id = filter_input(INPUT_POST, 'id_commentaire', FILTER_VALIDATE_INT);
    $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);

    if (!$id || !$contenu) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit();
    }

    $controller = new CommentsController();
    
    // Récupérer le commentaire existant
    $existingComment = $controller->showComment($id);
    if (!$existingComment) {
        echo json_encode(['success' => false, 'message' => 'Commentaire non trouvé']);
        exit();
    }

    // Créer un nouveau commentaire avec les données mises à jour
    $comment = new Comments(
        $id,
        $existingComment->getId_News(),
        $existingComment->getAuteur(),
        $contenu,
        $existingComment->getDate_Commentaire()
    );

    $controller->updateComment($comment, $id);
    echo json_encode(['success' => true, 'message' => 'Commentaire mis à jour avec succès']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
