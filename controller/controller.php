<?php

require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\user.php';

session_start();

function loginUser($pdo, $email, $mot_de_passe) {
    $identifiant = trim($email);

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? OR nom_utilisateur = ?");
    $stmt->execute([$identifiant, $identifiant]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['user'] = $user['artiste_id'];

        // Création d'un objet User
        $userObj = new User(
            $user['artiste_id'],
            $user['nom_utilisateur'],
            $user['email'],
            $user['mot_de_passe'],
            $user['prenom'],
            $user['nom_famille'],
            $user['date_naissance'],
            $user['image_path'],
            $user['type_utilisateur'],
            $user['score'],
            $user['date_creation']
        );

        // Stocker l’objet User si nécessaire
        $_SESSION['userObject'] = serialize($userObj);

        header("Location: ../tunify_avec_connexion/avec_connexion.php");
        exit;
    } else {
        return "❌ Email ou mot de passe incorrect.";
    }
}



function registerUser(PDO $pdo, User $user): string {
    try {
        // Vérification de la duplication d'email/nom d'utilisateur
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs 
                             WHERE email = ? OR nom_utilisateur = ?");
        $stmt->execute([$user->getEmail(), $user->getNomUtilisateur()]);
        
        if ($stmt->fetchColumn() > 0) {
            return "❌ Cet email ou nom d'utilisateur existe déjà";
        }

        // Requête SQL complète avec tous les champs
        $sql = "INSERT INTO utilisateurs (
                nom_utilisateur, 
                email, 
                mot_de_passe, 
                prenom, 
                nom_famille, 
                date_naissance, 
                image_path,
                type_utilisateur,
                score,
                date_creation
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        
        // Récupération des valeurs via les getters
        $success = $stmt->execute([
            $user->getNomUtilisateur(),
            $user->getEmail(),
            $user->getMotDePasse(),
            $user->getPrenom(),
            $user->getNomFamille(),
            $user->getDateNaissance(),
            $user->getImagePath(),
            $user->getTypeUtilisateur(),
            $user->getScore(),
            $user->getDateCreation()
        ]);

        if ($success) {
            // Mise à jour de l'ID généré
            $user->setId($pdo->lastInsertId());
            
            // Journalisation (optionnel)
            error_log("Nouvel utilisateur inscrit: " . $user->getEmail());
            
            return "✅ Inscription réussie ! <a href='login.php'>Connecte-toi ici</a>";
        }
        
        return "❌ Erreur lors de l'inscription";

    } catch (PDOException $e) {
        error_log("Erreur d'inscription: " . $e->getMessage());
        return "❌ Une erreur technique est survenue";
    }
}

function getUserInfo($pdo) {
    if (!isset($_SESSION['user'])) {
        echo "Vous devez être connecté.";
        exit;
    }

    $userId = $_SESSION['user'];
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE artiste_id = ?");
    $stmt->execute([$userId]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return new User(
            $user['artiste_id'],
            $user['nom_utilisateur'],
            $user['email'],
            $user['mot_de_passe'],
            $user['prenom'],
            $user['nom_famille'],
            $user['date_naissance'],
            $user['image_path'],
            $user['type_utilisateur'],
            $user['score'],
            $user['date_creation']
        );
    } else {
        echo "Utilisateur non trouvé.";
        exit;
    }
}
function checkIfAdmin($pdo): User {
    if (!isset($_SESSION['user'])) {
        header("Location: /projetweb/pages/tunisfy_sans_conexion/login.php");
        exit;
    }

    $userId = $_SESSION['user'];

    $query = $pdo->prepare("SELECT * FROM utilisateurs WHERE artiste_id = :id");
    $query->bindParam(':id', $userId, PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['type_utilisateur'] !== 'admin') {
        header("Location: /projetweb/View/pages/tunisfy_sans_conexion/unauthorized.php");
        exit;
    }

    // Retourner un objet User si besoin de le manipuler ensuite
    return new User(
        $user['artiste_id'],
        $user['nom_utilisateur'],
        $user['email'],
        $user['mot_de_passe'],
        $user['prenom'],
        $user['nom_famille'],
        $user['date_naissance'],
        $user['image_path'],
        $user['type_utilisateur'],
        $user['score'],
        $user['date_creation']
    );
}

function getAllUsers($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs");
    $stmt->execute();
    $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $users = [];
    foreach ($usersData as $user) {
        $users[] = new User(
            $user['artiste_id'],
            $user['nom_utilisateur'],
            $user['email'],
            $user['mot_de_passe'],
            $user['prenom'],
            $user['nom_famille'],
            $user['date_naissance'],
            $user['image_path'],
            $user['type_utilisateur'],
            $user['score'],
            $user['date_creation']
        );
    }
    return $users;
}
function deleteUserById($userId) {
    try {
        $pdo = config::getConnexion();
        $query = "DELETE FROM utilisateurs WHERE artiste_id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (Exception $e) {
        echo "Error deleting user: " . $e->getMessage();
        return false;
    }
}

function updateUser() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        $pdo = config::getConnexion();
        $query = "UPDATE utilisateurs SET nom_utilisateur = :name, email = :email, type_utilisateur = :role, score = :status WHERE artiste_id = :id";
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
    return false;
}

function updateUserSettings() {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $pdo = config::getConnexion();
        $id = $_POST['id'];
        $email = $_POST['email'];
        $newPassword = $_POST['password'];
        $newBirthdate = $_POST['date_naissance'];

        $imagePath = null;
        if (!empty($_FILES['profile_image']['name'])) {
            $targetDir = "uploads/profile_images/";
            $fileName = basename($_FILES["profile_image"]["name"]);
            $targetFilePath = $targetDir . time() . "_" . $fileName;
            move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath);
            $imagePath = $targetFilePath;
        }

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
        return $stmt->execute($params);
    }

    return false;
}

function updateUserProfile($userId, $name, $email, $status, $imagePath = null) {
    $pdo = config::getConnexion();

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

    return $stmt->execute();
}