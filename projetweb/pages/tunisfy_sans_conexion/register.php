<?php
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';

$message = '';

$pdo = config::getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $prenom = $_POST['prenom'];
    $nom_famille = $_POST['nom_famille'];
    $date_naissance = $_POST['date_naissance'];
    $image_path = 'default.jpg'; 

    $sql = "INSERT INTO utilisateurs 
            (nom_utilisateur, email, mot_de_passe, prenom, nom_famille, date_naissance, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nom_utilisateur, $email, $mot_de_passe, $prenom, $nom_famille, $date_naissance, $image_path])) {
        $message = "✅ Inscription réussie ! <a href='login.php'>Connecte-toi ici</a>";
    } else {
        $errorInfo = $stmt->errorInfo();
        $message = "❌ Erreur lors de l'inscription : " . $errorInfo[2];
    }
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
        <img src="../assets/img/logo.png" alt="Tunify Logo" class="logo-image">
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
