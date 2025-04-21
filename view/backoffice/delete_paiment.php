<?php
session_start();

include_once 'C:\xampp\htdocs\islem\projetweb\model\config.php'; 
include_once __DIR__ . '/../../controlleur/functionpaiments.php';
include_once __DIR__ . '/../../controlleur/function.php';


if (isset($_GET['song_id']) && isset($_GET['type'])) {
    $pdo = config::getConnexion();
    $song_id = intval($_GET['song_id']);
    $type = $_GET['type'];

    $deleted = false;

    if ($type === 'paiments') {
        $deleted = deleteTransaction($pdo, $song_id);
    } elseif ($type === 'paimentc') {
        $deleted = deleteCardPayment($pdo, $song_id);
    } elseif ($type === 'paimentm') {
        $deleted = deleteMobilePayment($pdo, $song_id);
    }

    if ($deleted) {
        $_SESSION['message'] = "Paiement supprimé avec succès !";
        header("Location: backoffice.php?deleted=true");
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression du paiement.";
        header("Location: backoffice.php?error=true");
    }
    exit;
} else {
    $_SESSION['message'] = "Erreur: ID ou type manquant.";
    header("Location: backoffice.php?error=true");
    exit;
}
?>
