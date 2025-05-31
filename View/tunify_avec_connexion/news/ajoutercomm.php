<?php 
session_start(); // Start session

require_once 'C:\xampp\htdocs\projetweb\controlleur\functionsnews.php';
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

$pdo = config::getConnexion();
$user = getUserInfo($pdo);
$user_id = $user->getArtisteId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'comment') {

    $id_news = (int)$_POST['id_news'];
    $auteur = trim($_POST['auteur']);
    $contenu = trim($_POST['contenu']);

    if (!empty($auteur) && !empty($contenu) && !empty($id_news) && !empty($user)) {
        
        $query = "SELECT * FROM commentaires WHERE id_news = :id_news AND id_user = :id_user";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id_news', $id_news, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['comment_message'] = "❗ Vous avez déjà ajouté un commentaire pour cette actualité.";
            $_SESSION['comment_type'] = "error";
            header("Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php?id_news=$id_news");  
            exit;
        } else {
            $id = ajouterCommentaire($pdo, $id_news, $auteur, $contenu, $user_id);

            if ($id) {
                $_SESSION['comment_message'] = "✅ Commentaire ajouté avec succès.";
                $_SESSION['comment_type'] = "success";
            } else {
                $_SESSION['comment_message'] = "❌ Erreur lors de l'ajout du commentaire.";
                $_SESSION['comment_type'] = "error";
            }

            header("Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php");  
            exit;
        }
    } else {
        $_SESSION['comment_message'] = "⚠️ Veuillez remplir tous les champs.";
        $_SESSION['comment_type'] = "error";
        header("Location: /projetweb/View/tunify_avec_connexion/avec_connexion.php");  
        exit;
    }
}
?>
