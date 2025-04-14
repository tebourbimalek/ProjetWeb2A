<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    echo "Vous devez être connecté pour accéder à cette page.";
    exit;
}

try {
    // Connexion à la base de données via la méthode getConnexion() de la classe config
    $pdo = config::getConnexion();

    // Récupérer l'ID de l'utilisateur connecté depuis la session
    $userId = $_SESSION['user'];

    // Préparer la requête SQL pour récupérer les informations de l'utilisateur
    $query = "SELECT artiste_id, nom_utilisateur, email, type_utilisateur, score, date_creation FROM utilisateurs WHERE artiste_id = :id";
    $stmt = $pdo->prepare($query);

    // Lier l'ID utilisateur pour éviter les injections SQL
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

    // Exécuter la requête
    $stmt->execute();

    // Récupérer les données de l'utilisateur
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        echo "Utilisateur non trouvé.";
        exit;
    }

    // Mettre à jour les informations si le formulaire est soumis
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
        $nom_utilisateur = $_POST['nom_utilisateur'];
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];
        $confirm_mot_de_passe = $_POST['confirm_mot_de_passe'];  // Confirmation du mot de passe

        // Vérifier si les mots de passe correspondent
        if ($mot_de_passe != $confirm_mot_de_passe) {
            $error_message = "Les mots de passe ne correspondent pas.";
        } else {
            // Mettre à jour le mot de passe si fourni
            if (!empty($mot_de_passe)) {
                $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE utilisateurs SET nom_utilisateur = ?, email = ?, mot_de_passe = ? WHERE artiste_id = ?");
                $stmt->execute([$nom_utilisateur, $email, $hashed_password, $userId]);
            } else {
                $stmt = $pdo->prepare("UPDATE utilisateurs SET nom_utilisateur = ?, email = ? WHERE artiste_id = ?");
                $stmt->execute([$nom_utilisateur, $email, $userId]);
            }

            // Message de succès
            $success_message = "Les modifications ont été enregistrées avec succès.";
        }

        // Redirection vers la même page après la mise à jour
        header("Location: avec_connexion.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paramètres - Tunify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .modal {
            display: flex;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #181818;
            padding: 30px;
            border-radius: 15px;
            width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
            position: relative;
        }

        .modal-content h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .setting-group {
            margin-bottom: 25px;
        }

        .setting-group h3 {
            margin-bottom: 10px;
        }

        .setting-group input[type="text"],
        .setting-group input[type="email"],
        .setting-group input[type="password"],
        .setting-group select {
            width: 100%;
            padding: 8px;
            background-color: #282828;
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 5px;
        }

        .setting-group label {
            margin-top: 10px;
            display: block;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 22px;
            cursor: pointer;
            color: #aaa;
        }

        .close:hover {
            color: #fff;
        }

        button {
            background: #1db954;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background: #1ed760;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #fff;
            background-color: #28a745; /* Success message color */
        }

        .error-message {
            background-color: #dc3545; /* Error message color */
        }
    </style>
</head>
<body>

<div class="modal" id="modal4">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Paramètres de votre compte</h2>

        <!-- Display success or error message -->
        <?php if (isset($success_message)): ?>
            <div class="message"><?= $success_message ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="message error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="setting-group">
                <h3>👤 Compte</h3>
                <label for="nom_utilisateur">Nom d'utilisateur :</label>
                <input type="text" name="nom_utilisateur" value="<?= htmlspecialchars($user['nom_utilisateur']) ?>" required>

                <label for="email">Email :</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label for="mot_de_passe">Nouveau mot de passe :</label>
                <input type="password" name="mot_de_passe" placeholder="Laisser vide si inchangé">

                <label for="confirm_mot_de_passe">Confirmer le mot de passe :</label>
                <input type="password" name="confirm_mot_de_passe" placeholder="Confirmer le mot de passe">
            </div>

            <div class="setting-group">
                <h3>🌐 Langue</h3>
                <select>
                    <option>Français</option>
                    <option selected>English</option>
                </select>
            </div>

            <div class="setting-group">
                <h3>📱 Affichage</h3>
                <label><input type="checkbox" checked> Afficher le panneau de lecture</label>
                <label><input type="checkbox" checked> Afficher les animations Canvas</label>
            </div>

            <div class="setting-group">
                <h3>👥 Social</h3>
                <label><input type="checkbox" checked> Voir les abonnés</label>
            </div>

            <button type="submit" name="update">Enregistrer les modifications</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("modal4");
        const closeBtn = document.getElementById("closeModal");

        modal.style.display = "flex";

        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
            window.location.href = "avec_connexion.php";
        });

        window.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.style.display = "none";
                window.location.href = "avec_connexion.php";
            }
        });
    });
</script>

</body>
</html>
