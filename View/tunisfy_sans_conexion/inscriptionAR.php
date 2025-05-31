<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'C:\xampp\htdocs\projetweb\vendor\autoload.php';

$message = '';
$pdo = config::getConnexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required = ['nom_utilisateur', 'email', 'mot_de_passe', 'type_utilisateur'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $message = "❌ Tous les champs obligatoires doivent être remplis";
            break;
        }
    }

    if (empty($message)) {
        try {
            $type_utilisateur = $_POST['type_utilisateur']; // Devrait être 'artiste' car en readonly

            $user = new User(
                0,
                $_POST['nom_utilisateur'],
                $_POST['email'],
                password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT),
                $_POST['prenom'] ?? '',
                $_POST['nom_famille'] ?? '',
                $_POST['date_naissance'] ?? '1970-01-01',
                'default.jpg',
                $type_utilisateur,
                0,
                date('Y-m-d H:i:s')
            );

            $message = registerUser($pdo, $user);

            // ✉️ Envoyer email de confirmation si type artiste
            if ($type_utilisateur === 'artiste' && $message === "✅ Inscription réussie ! <a href='login.php'>Connecte-toi ici</a>") {
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'omarkaoubi2002@gmail.com';       // 🔒 Remplace par ton email
                    $mail->Password = 'kvewqnauykuqnodp';          // 🔒 Utilise un mot de passe d'application
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('omarkaoubi2002@gmail.com', 'Tunify');
                    $mail->addAddress($_POST['email'], $_POST['nom_utilisateur']);

                    $mail->isHTML(true);
                    $mail->Subject = 'Confirmation de votre inscription - Tunify';
                    $mail->Body = "
                        <p>Bonjour <strong>{$_POST['nom_utilisateur']}</strong>,</p>
                        <p>Merci pour votre inscription sur <strong>Tunify</strong> en tant qu'artiste.</p>
                        <p>Nous avons bien reçu votre demande d'inscription.</p>
                        <p>Notre équipe va l'examiner et vous répondra dans un délai de <strong>24 heures</strong>.</p>
                        <br>
                        <p>Cordialement,<br>L'équipe Tunify</p>
                    ";
                    $mail->AltBody = "Bonjour {$_POST['nom_utilisateur']},\n\nMerci pour votre inscription sur Tunify en tant qu'artiste. Nous avons bien reçu votre demande et nous vous répondrons dans 24 heures.\n\nCordialement,\nL'équipe Tunify";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Erreur email : {$mail->ErrorInfo}");
                }
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
    <link rel="stylesheet" href="../style2.css">
    <script src="../inscription.js" defer></script>
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

            <!-- Type d'utilisateur forcé à 'artiste' -->
            <label for="type_utilisateur">Type d'utilisateur :</label><br>
            <input type="text" name="type_utilisateur" value="artiste" readonly><br>

            <button type="submit">S'inscrire</button>
        </form>

        <p><?= $message ?></p>
        <p>Vous avez déjà un compte ? <a href="\projetweb\View\pages\backoffice\acces.html">Go Back</a></p>
    </div>
</body>
</html>
