<?php
require_once 'C:\xampp\htdocs\projetweb\controller\controller.php';
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';

$message = '';
$pdo = config::getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = registerUser($pdo, $_POST);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Tunify</title>
    <link rel="stylesheet" href="style2.css">
    <script src="inscription.js" defer></script>
</head>
<body>

    <div class="login-container">
        <img src="/projetweb/assets/img/logo.png" alt="Tunify Logo" class="logo-image">
        <h1 class="logo">Tunify</h1>
        <h2>Créer un compte Tunify</h2>

        <form method="POST">
            <input type="text" name="nom_utilisateur" placeholder="Nom d'utilisateur" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br>
            <input type="text" name="prenom" placeholder="Prénom"><br>
            <input type="text" name="nom_famille" placeholder="Nom de famille"><br>
            <input type="date" name="date_naissance"><br>
            <button type="submit">S'inscrire</button>
        </form>

        <p><?= $message ?></p>
        <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
    </div>

</body>
</html>
