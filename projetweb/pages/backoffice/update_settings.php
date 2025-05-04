<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';
$pdo = config::getConnexion();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $newPassword = $_POST['password'];
    $newBirthdate = $_POST['date_naissance'];

    // Traitement de l'image
    $imagePath = null;
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "uploads/profile_images/";
        $fileName = basename($_FILES["profile_image"]["name"]);
        $targetFilePath = $targetDir . time() . "_" . $fileName;
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath);
        $imagePath = $targetFilePath;
    }

    // Mise à jour de la requête
    $params = [':email' => $email, ':id' => $id];
    $sql = "UPDATE utilisateurs SET email = :email";

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql .= ", mot_de_passe = :password";
        $params[':password'] = $hashedPassword;
    }

    if (!empty($newBirthdate)) {
        $sql .= ", date_naissance = :birthdate";
        $params[':birthdate'] = $newBirthdate;
    }

    if ($imagePath) {
        $sql .= ", image_path = :image";
        $params[':image'] = $imagePath;
    }

    $sql .= " WHERE artiste_id = :id";

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        header("Location: /projetweb/pages/tunify_avec_connexion/avec_connexion.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour.";
    }
}
?>
