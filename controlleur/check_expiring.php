<?php
require_once('../model/config.php');

$conn = config::getConnexion();

// Chercher les abonnements expirant dans 3 jours
$stmt = $conn->prepare("
    SELECT t.user_id, t.Abonnement, p.Date_Expiration 
    FROM transactions t
    JOIN paiment_par_card p ON t.ID = p.transaction_id
    WHERE DATEDIFF(p.Date_Expiration, NOW()) BETWEEN 0 AND 3
    UNION
    SELECT t.user_id, t.Abonnement, m.Date_Expiration 
    FROM transactions t
    JOIN paiment_mobile m ON t.ID = m.transaction_id
    WHERE DATEDIFF(m.Date_Expiration, NOW()) BETWEEN 0 AND 3
");

$stmt->execute();
$expiring = $stmt->fetchAll();

foreach ($expiring as $item) {
    $message = "Votre abonnement {$item['Abonnement']} expire le {$item['Date_Expiration']}";
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$item['user_id'], $message]);
}

echo "Notifications d'expiration envoyées: " . count($expiring);