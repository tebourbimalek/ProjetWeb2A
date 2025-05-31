<?php

session_start();

// Correct path to the config file
include_once 'C:\xampp\htdocs\projetweb\model\config.php';
include_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php';

include_once 'C:\xampp\htdocs\projetweb\controlleur\functionpaiments.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';


// Inclusion du script d'envoi de mail
$sendMailPath = __DIR__ . "/sendMail.php";
if (file_exists($sendMailPath)) {
    include_once $sendMailPath;
} else {
    echo "Fichier non trouvé: $sendMailPath";
    exit;
}

// Establish the connection
$conn = config::getConnexion();

// Usage of the function


$user = getUserInfo($conn);


$userId = $user->getArtisteId();
$user_id = $user->getArtisteId();
$adresse = $user->getEmail();
$nomutil = $user->getNomUtilisateur();



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve POST data
    $Abonnement = $_POST['type_abonnement'] ?? null;
    $selectedPaymentMethod = $_POST['payment-method'] ?? '';
    $paymentMethod = ($selectedPaymentMethod == 'card') ? 'Carte Bancaire' : (($selectedPaymentMethod == 'mobile') ? 'Paiement Mobile' : '');

    echo $paymentMethod ? "<p>Mode de paiement sélectionné: $paymentMethod</p>" : "<p>Aucun mode de paiement sélectionné.</p>";

    $errorMessage = '';

    if ($paymentMethod) {
        try {
            // Start the transaction
            if (!$conn->beginTransaction()) {
                throw new Exception("Erreur de début de transaction.");
            }

            // Insertion de la transaction
            $transactionId = insertTransaction($conn, $userId, $paymentMethod, $Abonnement);

            // Carte Bancaire payment logic
            if ($paymentMethod == 'Carte Bancaire') {
                $cardType = $_POST['card-type'] ?? '';
                $cardNumber = $_POST['card-number'] ?? '';
                $expiryDate = $_POST['expiry-date'] ?? '';
                $formattedExpiryDate = date('Y-m-d', strtotime('+1 month'));

                if (!empty($expiryDate)) {
                    $parts = explode('/', $expiryDate);
                    if (count($parts) == 2) {
                        $month = $parts[0];
                        $year = '20' . $parts[1];
                        $lastDay = date('t', strtotime("$year-$month-01"));
                        $formattedExpiryDate = "$year-$month-$lastDay";
                    }
                }

                insertCardPayment($conn, $transactionId, $cardType, $cardNumber, $formattedExpiryDate);
            }

            // Paiement Mobile payment logic
            if ($paymentMethod === 'Paiement Mobile') {
                $phoneNumber = $_POST['phone-number'] ?? '';
                $mobileProvider = $_POST['mobile-provider'] ?? '';
                $expirationDate = date('Y-m-d', strtotime('+1 month'));

                if (empty($phoneNumber) || empty($mobileProvider)) {
                    throw new Exception('Phone number and mobile provider are required for mobile payments.');
                }

                insertMobilePayment($conn, $transactionId, $mobileProvider, $phoneNumber, $expirationDate);
            }

            // Commit transaction
            $conn->commit();

            // After commit, insert a notification for the user
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, id_trans) VALUES (?, ?)");
            $stmt->execute([$userId, $transactionId]);

            // Send the invoice email (For testing, sending to a fixed email)
            sendInvoiceMail($adresse, $nomutil, $transactionId);

            // Payment success message
            header("Location: ../tunify_avec_connexion/avec_connexion.php");
            exit();
        } catch (Exception $e) {
            // Rollback if something goes wrong
            $conn->rollback();
            $errorMessage = "Erreur lors du paiement : " . $e->getMessage();
            echo "<div style='background-color: #f44336; color: white; padding: 15px; border-radius: 5px; margin-top: 20px;'>
                    <h3>Erreur</h3>
                    <p>$errorMessage</p>
                  </div>
                  <p style='margin-top: 20px;'>
                    <a href='paiement.php' style='background-color: #4B0082; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;'>
                      <i class='fas fa-arrow-left'></i> Réessayer
                    </a>
                  </p>";
        }
    } else {
        // Error if no payment method selected
        echo "<div style='background-color: #f44336; color: white; padding: 15px; border-radius: 5px; margin-top: 20px;'>
                <h3>Erreur</h3>
                <p>Aucun mode de paiement sélectionné. Veuillez choisir un mode de paiement.</p>
              </div>
              <p style='margin-top: 20px;'>
                <a href='paiement.php' style='background-color: #4B0082; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;'>
                  <i class='fas fa-arrow-left'></i> Retour au formulaire de paiement
                </a>
              </p>";
    }
}
?>
