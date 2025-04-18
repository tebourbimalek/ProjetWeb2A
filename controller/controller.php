<?php
session_start();

function loginUser($pdo, $email, $mot_de_passe) {
    // Nettoyer l'email ou nom d'utilisateur
    $identifiant = trim($email);

    // Préparer et exécuter la requête
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? OR nom_utilisateur = ?");
    $stmt->execute([$identifiant, $identifiant]);
    $user = $stmt->fetch();

    // Vérification du mot de passe
    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        // Connexion réussie
        $_SESSION['user'] = $user['artiste_id']; // ou 'id' selon ta table
        header("Location: ../tunify_avec_connexion/avec_connexion.php");
        exit;
    } else {
        // Échec de connexion
        return "❌ Email ou mot de passe incorrect.";
    }
}
function registerUser($pdo, $data) {
    $nom_utilisateur = $data['nom_utilisateur'];
    $email = $data['email'];
    $mot_de_passe = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
    $prenom = $data['prenom'];
    $nom_famille = $data['nom_famille'];
    $date_naissance = $data['date_naissance'];
    $image_path = 'default.jpg';

    $sql = "INSERT INTO utilisateurs 
            (nom_utilisateur, email, mot_de_passe, prenom, nom_famille, date_naissance, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nom_utilisateur, $email, $mot_de_passe, $prenom, $nom_famille, $date_naissance, $image_path])) {
        return "✅ Inscription réussie ! <a href='login.php'>Connecte-toi ici</a>";
    } else {
        $errorInfo = $stmt->errorInfo();
        return "❌ Erreur lors de l'inscription : " . $errorInfo[2];
    }
}
function getUserInfo($pdo) {
    if (!isset($_SESSION['user'])) {
        echo "You must be logged in to view this page.";
        exit;
    }

    $userId = $_SESSION['user'];

    $query = "SELECT artiste_id, nom_utilisateur, email, type_utilisateur, score, date_creation, image_path 
              FROM utilisateurs 
              WHERE artiste_id = :id";
              
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit;
    }

    return $user;
}
function checkIfAdmin($pdo) {
    if (!isset($_SESSION['user'])) {
        header("Location: /projetweb/pages/tunisfy_sans_conexion/login.php");
        exit;
    }

    $userId = $_SESSION['user'];

    $query = $pdo->prepare("SELECT * FROM utilisateurs WHERE artiste_id = :id");
    $query->bindParam(':id', $userId, PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch();

    if (!$user || $user['type_utilisateur'] !== 'admin') {
        header("Location: /projetweb/pages/tunisfy_sans_conexion/unauthorized.php");
        exit;
    }

    return $user; // Si besoin d'utiliser les infos de l'admin après
}
function getAllUsers($pdo) {
    $query = $pdo->prepare("SELECT * FROM utilisateurs");
    $query->execute();
    return $query->fetchAll();
}
