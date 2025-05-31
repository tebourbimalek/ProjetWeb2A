<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php'; 
require_once 'C:\xampp\htdocs\projetweb\model\user.php'; 
$error = '';
$success = '';
$pdo = config::getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
    $user = User::findByResetToken($pdo, $token);
    
    if (!$user || strtotime($user->reset_expires) < time()) {
        $error = "Lien invalide ou expiré.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $user = User::findByResetToken($pdo, $token);
    
    if (!$user) {
        $error = "Lien invalide ou expiré.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Update password (avec artiste_id)
        User::updatePassword($pdo, $user->artiste_id, $password);

        // Clear reset token (pareil ici)
        User::clearResetToken($pdo, $user->artiste_id);

        $success = "Mot de passe réinitialisé avec succès!";
    }

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="login-container">
        <h2>Nouveau mot de passe</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit">Réinitialiser le mot de passe</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>