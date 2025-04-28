<?php
session_start();
require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Gmail SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'omarkaoubi2002@gmail.com';
    $mail->Password   = 'kvewqnauykuqnodp'; // 👈 No spaces!
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // Recipients
    $mail->setFrom('omarkaoubi2002@gmail.com', 'Tunify');
    $mail->addAddress('omarkaoubi2002@gmail.com');

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Réinitialisation de votre mot de passe';

    
    $resetLink = "http://localhost/projetweb/View/pages/tunisfy_sans_conexion/reset_password.php?token=$token";

    $mail->Body = "
        <h1>Réinitialisation du mot de passe</h1>
        <p>Bonjour,</p>
        <p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :</p>
        <p><a href='$resetLink'>$resetLink</a></p>
        <p>Ce lien expirera dans 1 heure.</p>
    ";

    $mail->send();
    echo "✅ Email envoyé avec succès à omarkaoubi2002@gmail.com";
} catch (Exception $e) {
    error_log("Email sending failed: " . $mail->ErrorInfo);
    echo "❌ Erreur lors de l'envoi de l'email.";
}
