<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\user.php';

$message = '';
$pdo = config::getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required fields first
    $required = ['nom_utilisateur', 'email', 'mot_de_passe', 'type_utilisateur'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $message = "❌ Tous les champs obligatoires doivent être remplis";
            break;
        }
    }

    if (empty($message)) {
        // Create User object with default values
        try {
            // Determine the user type (artist or regular user)
            $type_utilisateur = $_POST['type_utilisateur'] == 'artiste' ? 'artiste' : 'user';

            // Create User object
            $user = new User(
                0, // Temporary ID (will be set by database)
                $_POST['nom_utilisateur'],
                $_POST['email'],
                password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
                $_POST['prenom'] ?? '',
                $_POST['nom_famille'] ?? '',
                $_POST['date_naissance'] ?? '1970-01-01',
                'default.jpg', // Default profile picture (no upload)
                $type_utilisateur, // User type (either 'user' or 'artiste')
                0, // Default score
                date('Y-m-d H:i:s') // Creation date
            );

            // Call controller function with User object
            $message = registerUser($pdo, $user);
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

            <!-- Type of User -->
            <label for="type_utilisateur">Type d'utilisateur :</label><br>
            <select name="type_utilisateur" required>
                <option value="user">Utilisateur</option>
                <option value="artiste">Artiste</option>
            </select><br>
            <style>
                /* Specific styling for select dropdown */
form select {
  width: 100%;
  padding: 12px 15px;
  margin-bottom: 25px;
  border: none;
  background-color: var(--spotify-light-gray);
  border-radius: 4px;
  color: var(--spotify-white);
  font-size: 16px;
  transition: all var(--transition-speed);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.1);
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  cursor: pointer;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="%23FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>');
  background-repeat: no-repeat;
  background-position: right 15px center;
  padding-right: 40px;
}

form select::-ms-expand {
  display: none;
}

form select option {
  background-color: var(--spotify-dark-gray);
  color: var(--spotify-white);
  padding: 12px;
}

form select:focus {
  outline: none;
  box-shadow: inset 0 0 0 2px var(--spotify-green);
  background-color: #333;
}
            </style>

            <button type="submit">S'inscrire</button>
        </form>

        <p><?= $message ?></p>
        <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
    </div>

</body>
</html>
