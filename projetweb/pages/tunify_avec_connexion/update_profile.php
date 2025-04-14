<?php
// Start the session
session_start();

// Include the config file
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    
    // Initialisation de la variable image
    $imagePath = null;

    // Vérifie si une image est envoyée
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            // Créer un nom unique pour le fichier
            $newFileName = uniqid('img_') . '.' . $fileExtension;
            $uploadDir = 'uploads/profile_images/'; // dossier d'upload
            $destination = $uploadDir . $newFileName;

            // Créer le dossier s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Déplacer le fichier uploadé
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

    // Connexion à la base
    $pdo = config::getConnexion();

    // Si une nouvelle image est uploadée
    if ($imagePath !== null) {
        $query = "UPDATE utilisateurs 
                  SET nom_utilisateur = :name, email = :email, score = :status, image_path = :image 
                  WHERE artiste_id = :id";
    } else {
        $query = "UPDATE utilisateurs 
                  SET nom_utilisateur = :name, email = :email, score = :status 
                  WHERE artiste_id = :id";
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

    if ($imagePath !== null) {
        $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
    }

    if ($stmt->execute()) {
        header("Location: avec_connexion.php"); // redirection après succès
        exit;
    } else {
        echo "Erreur lors de la mise à jour.";
    }
} else {
    echo "Méthode non autorisée.";
}
?>
