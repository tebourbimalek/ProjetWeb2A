<?php
session_start();

require_once 'C:/xampp/htdocs/projetweb/model/config.php';    
require_once 'C:/xampp/htdocs/projetweb/controlleur/functionsnews.php';
require_once 'C:/xampp/htdocs/projetweb/model/classe.php';
require_once 'C:/xampp/htdocs/projetweb/controlleur/controlleruser.php';

$pdo = config::getConnexion();
$user = getUserInfo($pdo);
$user_id = $user->getArtisteId();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        isset($_POST['news-title'], $_POST['news-content'], $_POST['news-date']) &&
        !empty($_FILES['news_file']['name'])
    ) {
        $newsTitle = htmlspecialchars($_POST['news-title']);
        $newsContent = htmlspecialchars($_POST['news-content']);
        $newsDescription = htmlspecialchars($_POST['news-date']);

        // Emplacement absolu du dossier d'upload
        $uploadDirAbsolute = 'C:/xampp/htdocs/projetweb/View/tunify_avec_connexion/news/uploads/';

        if (!is_dir($uploadDirAbsolute)) {
            mkdir($uploadDirAbsolute, 0777, true);
        }

        // Génération d'un nom unique pour éviter les doublons
        $originalName = basename($_FILES['news_file']['name']);
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $uniqueName = time() . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $fileExtension;

        // Chemin complet pour enregistrer physiquement et dans la base
        $uploadFileAbsolute = $uploadDirAbsolute . $uniqueName;

        $fileSize = $_FILES['news_file']['size'];
        $maxFileSize = 50 * 1024 * 1024; // 50MB
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'png'];

        if (in_array($fileExtension, $allowedTypes) && $fileSize <= $maxFileSize) {
            if (move_uploaded_file($_FILES['news_file']['tmp_name'], $uploadFileAbsolute)) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO news (titre, contenu, description, image, date_publication, id_user) 
                        VALUES (:title, :content, :description, :file_path, NOW(), :id_user)
                    ");
                    $stmt->bindParam(':title', $newsTitle);
                    $stmt->bindParam(':content', $newsContent);
                    $stmt->bindParam(':description', $newsDescription);
                    $stmt->bindParam(':file_path', $uploadFileAbsolute); // 👈 chemin absolu
                    $stmt->bindParam(':id_user', $user_id);

                    if ($stmt->execute()) {
                        echo '✅ News ajoutée avec succès !';
                        header('Location: ../backoffice.php');
                        exit();
                    } else {
                        echo '❌ Erreur lors de l\'ajout !';
                        header('Location: ../backoffice.php');
                        exit();

                    }
                } catch (PDOException $e) {
                    echo '❌ Erreur BDD : ' . $e->getMessage();
                }
            } else {
                echo '❌ Échec du téléchargement du fichier.';
            }
        } else {
            echo '❌ Fichier invalide ou trop volumineux.';
        }
    } else {
        echo '❌ Remplis tous les champs et ajoute un fichier.';
    }
}
?>
