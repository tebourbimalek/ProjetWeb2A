<?php

require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php'; 
require_once 'C:\xampp\htdocs\projetweb\Model\includes\user.php'; 

$message = '';
$pdo = config::getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    
    // Check if user exists
    $user = User::findByEmail($pdo, $email);
    
    if ($user) {
        // Generate token and expiration (1 hour)
        $token = bin2hex(random_bytes(50));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        
        // Store token in database
        User::createPasswordReset($pdo, $user->artiste_id, $token, $expires);
        
        // Send email
        require 'send_reset_email.php';
        
        $message = "Un email de réinitialisation a été envoyé !";
    } else {
        $message = "Aucun compte trouvé avec cet email.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="login-container">
        <h2>Réinitialiser le mot de passe</h2>
        
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <button type="submit">Envoyer le lien de réinitialisation</button>
        </form>
        
        <p class="register">
            <a href="login.php">Retour à la connexion</a>
        </p>
    </div>
</body>
</html>