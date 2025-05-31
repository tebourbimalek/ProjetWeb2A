<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
include_once 'C:\xampp\htdocs\projetweb\controlleur\functionpaiments.php';
include_once 'C:\xampp\htdocs\projetweb\controlleur\function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = config::getConnexion();
    $type_paiment = isset($_POST['type_paimentc_id']) ? trim($_POST['type_paimentc_id']) : null;

    // Handle different types of payments
    if ($type_paiment == 'paimentc') {
        $song_paimentc_id = isset($_POST['song_paimentc_id']) ? trim($_POST['song_paimentc_id']) : null;
        $numero_carte = isset($_POST['edit-paimentc-numero']) ? trim($_POST['edit-paimentc-numero']) : null;
        $type_carte = isset($_POST['edit-paimentc-methode']) ? trim($_POST['edit-paimentc-methode']) : null;
        $date_expiration = isset($_POST['edit-paimentc-title']) ? trim($_POST['edit-paimentc-title']) : null;

        // Validate inputs
        if (empty($song_paimentc_id) || empty($numero_carte) || empty($type_carte) || empty($date_expiration)) {
            echo "All fields are required!";
            exit();
        }

        // Execute update function
        if (updatePaymentCard($pdo, $song_paimentc_id, $numero_carte, $type_carte, $date_expiration)) {
            $_SESSION['message'] = "Transaction updated successfully!";
            header("Location: ../backoffice.php?updated=true");
            exit();
        } else {
            $_SESSION['message'] = "Failed to update the transaction.";
            header("Location:  ../backoffice.php?error=true");
            exit();
        }

    } elseif ($type_paiment == 'paiment_mobile') {
        $id = isset($_POST['song_paimentmobile_id']) ? trim($_POST['song_paimentmobile_id']) : null;
        $numero = isset($_POST['edit-mobile-numero']) ? trim($_POST['edit-mobile-numero']) : null;
        $provider = isset($_POST['edit-mobile-methode']) ? trim($_POST['edit-mobile-methode']) : null;
        $expiration = isset($_POST['edit-mobile']) ? trim($_POST['edit-mobile']) : null;

        // Validate inputs
        if (empty($id) || empty($numero) || empty($provider) || empty($expiration)) {
            echo "All fields are required!";
            exit();
        }

        // Execute update function
        if (updatePaymentMobile($pdo, $id, $numero, $provider, $expiration)) {
            $_SESSION['message'] = "Transaction updated successfully!";
            header("Location:  ../backoffice.php?updated=true");
            exit();
        } else {
            $_SESSION['message'] = "Failed to update the transaction.";
            header("Location:  ../backoffice.php?error=true");
            exit();
        }

    } else {
        $song_id = isset($_POST['song_id']) ? trim($_POST['song_id']) : null;
        $date_paiement = isset($_POST['edit-paiment-title']) ? trim($_POST['edit-paiment-title']) : null;
        $abonnement = isset($_POST['edit-song-album']) ? trim($_POST['edit-song-album']) : null;

        // Validate inputs
        if (empty($song_id) || empty($date_paiement) || empty($abonnement)) {
            $_SESSION['message'] = "All fields are required!";
            header("Location:  ../backoffice.php?error=true");
            exit();
        }

        // Execute update function
        if (updateTransaction($pdo, $song_id, $date_paiement, $abonnement)) {
            $_SESSION['message'] = "Transaction updated successfully!";
            header("Location:  ../backoffice.php?updated=true");
            exit();
        } else {
            $_SESSION['message'] = "Failed to update the transaction.";
            header("Location:  ../backoffice.php?error=true");
            exit();
        }
    }
} else {
    echo "Invalid request method!";
}

?>