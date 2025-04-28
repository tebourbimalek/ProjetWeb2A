<?php
require_once("../../controlleur/functionpaiments.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de paiement invalide.");
}

$paiment_id = (int)$_GET['id'];
$paiment = getPaimentById($paiment_id);

if (!$paiment) {
    die("Paiement introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Paiement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 60px;
        }
        .facture-container {
            border: 1px solid #ccc;
            padding: 20px;
            width: 600px;
            margin: auto;
        }
        h1 {
            text-align: center;
            color:rgb(88, 4, 4);
        }
        .ligne {
            margin: 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 40px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="facture-container">
        <h1>Facture de Paiement</h1>
        <div class="ligne"><strong>ID Paiement:</strong> <?= htmlspecialchars($paiment['ID']) ?></div>
        <div class="ligne"><strong>Date:</strong> <?= htmlspecialchars($paiment['Date']) ?></div>
        <div class="ligne"><strong>Type d'abonnement:</strong> <?= htmlspecialchars($paiment['Abonnement']) ?></div>
        <div class="ligne"><strong>Méthode de paiement:</strong> <?= htmlspecialchars($paiment['payment_method']) ?></div>
        <div class="ligne"><strong>ID Client:</strong> <?= htmlspecialchars($paiment['user_id']) ?></div>
        <div class="ligne total"><strong>Montant:</strong> <?= number_format($paiment['montant'], 2) ?> TND</div>
    </div>
</body>
</html>