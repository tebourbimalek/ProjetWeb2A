<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';

$message = '';


$pdo = config::getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifiant = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? OR nom_utilisateur = ?");
    $stmt->execute([$identifiant, $identifiant]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['user'] = $user['artiste_id'];
        header("Location: ../tunify_avec_connexion/avec_connexion.php");
        exit;
    } else {
        $message = "❌ Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Tunify</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>

    <div class="login-container">
        <img src="../assets/img/logo.png" alt="Tunify Logo" class="logo-image">
        <h1 class="logo">Tunify</h1>
        <h2>Se connecter à Tunify</h2>

        <?php if (!empty($message)): ?>
            <p style="color: red;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Adresse e-mail ou nom d'utilisateur</label>
            <input type="text" id="email" name="email" required>

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <div class="options">
                <label><input type="checkbox"> Se souvenir de moi</label>
                <a href="#">Mot de passe oublié ?</a>
            </div>

            <button type="submit">Se connecter</button>

            <p class="divider">OU</p>

            <button class="social-login" type="button">Continuer avec Google</button>
            <button class="social-login" type="button">Continuer avec Facebook</button>

            <p class="register">
                Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous</a>
            </p>
        </form>
    </div>

</body>
</html>
