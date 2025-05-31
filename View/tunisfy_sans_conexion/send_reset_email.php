<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

$adresse = $_POST['email'];

// Génération du token sécurisé
$token = bin2hex(random_bytes(32));

// 👉 Sauvegarde du token dans la base de données avec expiration (à faire toi-même ici)

// Exemple : stocke ce token avec un timestamp dans une table `password_resets`

try {
    // Gmail SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'omarkaoubi2002@gmail.com';
    $mail->Password   = 'kvewqnauykuqnodp'; // Attention : privilégie les variables d'environnement
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // Recipients
    $mail->setFrom('omarkaoubi2002@gmail.com', 'Tunify');
    $mail->addAddress($adresse);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Réinitialisation de votre mot de passe';

    $resetLink = "http://localhost/projetweb/View/tunisfy_sans_conexion/reset_password.php?token=$token";

    $mail->Body = "
    <div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto;'>
        <h2 style='color: #333;'>🔐 Réinitialisation de votre mot de passe</h2>
        <p>Bonjour,</p>
        <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour procéder :</p>
        <p style='text-align: center;'>
            <a href='$resetLink' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Réinitialiser le mot de passe</a>
        </p>
        <p>Si le bouton ne fonctionne pas, copiez et collez le lien suivant dans votre navigateur :</p>
        <p style='word-wrap: break-word; background-color: #eee; padding: 10px; border-radius: 5px;'>$resetLink</p>
        <p style='color: #888; font-size: 12px;'>⚠️ Ce lien expirera dans 1 heure pour des raisons de sécurité.</p>
        <hr style='margin: 20px 0;'>
        <p style='font-size: 12px; color: #aaa;'>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet e-mail.</p>
    </div>
    ";


    $mail->send();
    echo "✅ Email envoyé avec succès à $adresse";
    header("Location: login.php");
    exit();
} catch (Exception $e) {
    error_log("Email sending failed: " . $mail->ErrorInfo);
    echo "❌ Erreur lors de l'envoi de l'email.";
}
