<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'];

    $imagePath = null;

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = uniqid('img_') . '.' . $fileExtension;
            $uploadDir = 'uploads/profile_images/';
            $destination = $uploadDir . $newFileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($fileTmpPath, $destination)) {
                $imagePath = $destination;
            } else {
                echo "Erreur lors du téléchargement de l'image.";
                exit;
            }
        } else {
            echo "Extension de fichier non autorisée.";
            exit;
        }
    }

    $pdo = config::getConnexion();

    if ($imagePath !== null) {
        $query = "UPDATE utilisateurs 
                  SET image_path = :image 
                  WHERE artiste_id = :id";
    } else {
        echo "Aucune image sélectionnée.";
        exit;
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

    if ($imagePath !== null) {
        $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
    }

    if ($stmt->execute()) {
        header("Location: avec_connexion.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour.";
    }
} else {
    echo "Méthode non autorisée.";
}
?>
