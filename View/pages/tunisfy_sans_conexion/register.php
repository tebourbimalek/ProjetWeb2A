<?php
require_once 'C:\xampp\htdocs\projetweb\controller\controller.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\user.php';

$message = '';
$pdo = config::getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required fields first
    $required = ['nom_utilisateur', 'email', 'mot_de_passe'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $message = "❌ Tous les champs obligatoires doivent être remplis";
            break;
        }
    }

    if (empty($message)) {
        // Create User object with default values
        try {
            $user = new User(
                0, // Temporary ID (will be set by database)
                $_POST['nom_utilisateur'],
                $_POST['email'],
                password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
                $_POST['prenom'] ?? '',
                $_POST['nom_famille'] ?? '',
                $_POST['date_naissance'] ?? '1970-01-01',
                'default.jpg', // Default image
                'user', // Default type
                0, // Default score
                date('Y-m-d H:i:s') // Creation date
            );

            // Call controller function with User object
            $message = registerUser($pdo, $user);
            
            // Optional: Auto-login after registration
            if(strpos($message, '✅') !== false) {
                $_SESSION['user'] = $user->getId();
                header("Location: login.php");
                exit;
            }
        } catch (Exception $e) {
            $message = "❌ Erreur lors de la création du compte: " . $e->getMessage();
        }
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
