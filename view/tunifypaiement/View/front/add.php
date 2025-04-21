<?php
include_once "config.php";


$functionPath = __DIR__ . "../../../../../controlleur/functionpaiments.php";
if (file_exists($functionPath)) {
    include_once $functionPath;
} else {
    echo "Fichier non trouvé: $functionPath";
}


$conn = config::getConnexion();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Abonnement = $_POST['type_abonnement'] ?? null;
    $selectedPaymentMethod = $_POST['payment-method'] ?? '';
    $paymentMethod = ($selectedPaymentMethod == 'card') ? 'Carte Bancaire' : (($selectedPaymentMethod == 'mobile') ? 'Paiement Mobile' : '');

    echo $paymentMethod ? "<p>Mode de paiement sélectionné: $paymentMethod</p>" : "<p>Aucun mode de paiement sélectionné.</p>";

    $userId = 1; // Replace with actual session user ID
    $errorMessage = '';

    if ($paymentMethod) {
        try {
            $conn->beginTransaction();

            // Call the insertTransaction function
            $transactionId = insertTransaction($conn, $userId, $paymentMethod, $Abonnement);

            if ($paymentMethod == 'Carte Bancaire') {
                $cardType = $_POST['card-type'] ?? '';
                $cardNumber = $_POST['card-number'] ?? '';
                $expiryDate = $_POST['expiry-date'] ?? '';
                $formattedExpiryDate = date('Y-m-d', strtotime('+1 month')); // default

                if (!empty($expiryDate)) {
                    $parts = explode('/', $expiryDate);
                    if (count($parts) == 2) {
                        $month = $parts[0];
                        $year = '20' . $parts[1];
                        $lastDay = date('t', strtotime("$year-$month-01"));
                        $formattedExpiryDate = "$year-$month-$lastDay";
                    }
                }

                // Call the insertCardPayment function
                insertCardPayment($conn, $transactionId, $cardType, $cardNumber, $formattedExpiryDate);
            }

            if ($paymentMethod === 'Paiement Mobile') {
                $phoneNumber = $_POST['phone-number'] ?? '';
                $mobileProvider = $_POST['mobile-provider'] ?? '';
                $expirationDate = date('Y-m-d', strtotime('+1 month'));

                if (empty($phoneNumber) || empty($mobileProvider)) {
                    throw new Exception('Phone number and mobile provider are required for mobile payments.');
                }

                // Call the insertMobilePayment function
                insertMobilePayment($conn, $transactionId, $mobileProvider, $phoneNumber, $expirationDate);
            }

            $conn->commit();

            echo "<div style='background-color: #4CAF50; color: white; padding: 15px; border-radius: 5px; margin-top: 20px;'>
                    <h3>Paiement réussi!</h3>
                    <p>Méthode: $paymentMethod</p>
                    <p>Transaction ID: $transactionId</p>
                    <p>Date: " . date('d/m/Y H:i:s') . "</p>
                  </div>
                  <p style='margin-top: 20px;'>
                    <a href='dashboard.html' style='background-color: #4B0082; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;'>
                      <i class='fas fa-home'></i> Retour au tableau de bord
                    </a>
                  </p>";

        } catch (Exception $e) {
            $conn->rollback();
            $errorMessage = "Error processing the payment: " . $e->getMessage();
            echo "<div style='background-color: #f44336; color: white; padding: 15px; border-radius: 5px; margin-top: 20px;'>
                    <h3>Erreur lors du traitement du paiement</h3>
                    <p>$errorMessage</p>
                  </div>
                  <p style='margin-top: 20px;'>
                    <a href='paiement.php' style='background-color: #4B0082; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;'>
                      <i class='fas fa-arrow-left'></i> Réessayer
                    </a>
                  </p>";
        }
    } else {
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