<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'C:\xampp\htdocs\projetweb\vendor\autoload.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

// Establish database connection
$pdo = config::getConnexion();

// Usage of the function
$user = getUserInfo($pdo);
$user_id = $user->getArtisteId();
$adresse = $user->getEmail();
$nomutil = $user->getNomUtilisateur();

function sendInvoiceMail($recipientEmail, $recipientName, $transactionId) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'omarkaoubi2002@gmail.com'; // Your email address
        $mail->Password   = 'kvewqnauykuqnodp'; // Your email app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // Recipients
        $mail->setFrom('omarkaoubi2002@gmail.com', 'Tunify');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Votre Facture Tunify';
        
        // Get current date for the invoice
        $currentDate = date('d/m/Y');
        
        // Enhanced HTML email template with animations
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunify - Votre Facture</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #8b0000, #ff4500);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .logo {
            margin-bottom: 20px;
            animation: fadeInDown 1s ease-out;
        }
        
        .logo img {
            max-width: 150px;
        }
        
        .title {
            font-size: 28px;
            font-weight: 600;
            margin: 0;
            animation: fadeInUp 1s ease-out 0.3s both;
        }
        
        .content {
            padding: 30px;
            color: #333;
            animation: fadeIn 1s ease-out 0.6s both;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 25px;
        }
        
        .message {
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .invoice-box {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            background-color: #f9f9f9;
            animation: slideInUp 0.8s ease-out 0.9s both;
        }
        
        .invoice-info {
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
        }
        
        .invoice-label {
            font-weight: 600;
            color: #555;
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
            animation: pulse 2s infinite;
        }
        
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #8b0000, #ff4500);
            color: white;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(139, 0, 0, 0.2);
        }
        
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            color: #777;
            font-size: 14px;
            border-top: 1px solid #e0e0e0;
        }
        
        .social-icons {
            margin: 15px 0;
        }
        
        .social-icons a {
            display: inline-block;
            margin: 0 8px;
            color: #8b0000;
            text-decoration: none;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInDown {
            from { 
                opacity: 0;
                transform: translateY(-20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        /* Media Queries for Responsiveness */
        @media only screen and (max-width: 600px) {
            .header {
                padding: 20px;
            }
            
            .content {
                padding: 20px;
            }
            
            .title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <!-- Replace with your actual logo or text -->
                <span style="font-size: 36px; font-weight: 700;">TUNIFY</span>
            </div>
            <h1 class="title">Votre Facture est Prête</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Bonjour <strong>{$recipientName}</strong>,</p>
            
            <div class="message">
                <p>Nous vous remercions pour votre paiement. Votre facture est maintenant disponible.</p>
                <p>Vous trouverez ci-dessous les détails de votre transaction :</p>
            </div>
            
            <div class="invoice-box">
                <div class="invoice-info">
                    <span class="invoice-label">ID Transaction:</span>
                    <span>{$transactionId}</span>
                </div>
                <div class="invoice-info">
                    <span class="invoice-label">Date:</span>
                    <span>{$currentDate}</span>
                </div>
            </div>
            
            <div class="button-container">
                <a href="http://localhost/projetweb/view/tunify_avec_connexion/payment/facture.php?id={$transactionId}" class="button">
                    Voir ma Facture
                </a>
            </div>
            
            <p>Si vous avez des questions concernant votre facture, n'hésitez pas à contacter notre équipe de support.</p>
            
            <p style="margin-top: 30px;">Cordialement,<br>
            <strong>L'équipe Tunify</strong></p>
        </div>
        
        <div class="footer">
            <div class="social-icons">
                <!-- Replace # with your actual social media links -->
                <a href="#">Facebook</a> | 
                <a href="#">Instagram</a> | 
                <a href="#">Twitter</a>
            </div>
            <p>&copy; 2025 Tunify, Tous droits réservés.</p>
            <p style="font-size: 12px; margin-top: 10px;">
                Ce message électronique est confidentiel et destiné uniquement au destinataire mentionné.
            </p>
        </div>
    </div>
</body>
</html>
HTML;

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur d'envoi d'email : " . $mail->ErrorInfo);
        return false;
    }
}
?>
