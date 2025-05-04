<?php 
require_once 'displaysongs.php';
require_once 'C:\xampp\htdocs\islem\projetweb\controlleur\functionpaiments.php';

$userId = 1; // Change as needed
$unreadCount = countUnreadNotifications($userId);
$paiements = affichagenotication($userId);
markNotificationsAsRead($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tunify</title>
    <link rel="stylesheet" href="css.css">
    <script src="https://kit.fontawesome.com/d4610e21c1.js" crossorigin="anonymous"></script>
    <style>
        .highlight-unread {
            background-color: #444444 !important;
            font-weight: bold;
        }
        .notification-count {
            position: absolute;
            top: -10px;
            right: -8px;
            background-color: red;
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="left-section">
        <a href="/projetweb/pages/tunify_avec_connexion/avec_connexion.php"><i class="fa-solid fa-house" style="color: grey; font-size:20px;"></i></a>
    </div>
    <div class="right-section">
        <a href="#" class="mot">Premium</a>
        <span class="divider">|</span>
        <a href="#" class="mot">S'inscrire</a>
        <a href="notifications.php" class="notification-icon" style="position:relative;">
            <i class="fa-solid fa-circle-user fa-xl" style="font-size:30px;"></i>
            <?php if ($unreadCount > 0): ?>
                <span class="notification-count"><?= $unreadCount ?></span>
            <?php endif; ?>
        </a>
    </div>
</nav>

<div style="background-color:#2a2a2a; margin:15px; padding:10px;">
    <div class="payment-section">
        <h2 style="color:white;">Tous les paiements</h2>
        <table class="payment-table" id="transactions-table" style="width:100%; color:white;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Abonnement</th>
                    <th>Méthode de paiement</th>
                    <th>Date expiration</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; foreach ($paiements as $payment): ?>
                    <tr class="<?= ($payment['est_lue'] == 0) ? 'highlight-unread' : '' ?>">
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($payment['Date']) ?></td>
                        <td><?= htmlspecialchars($payment['Abonnement']) ?></td>
                        <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                        <td>
                            <?php
                                $paymentDate = new DateTime($payment['Date']);
                                if ($payment['Abonnement'] == 'Mini') {
                                    $paymentDate->modify('+1 week');
                                } else {
                                    $paymentDate->modify('+1 month');
                                }
                                echo $paymentDate->format('Y-m-d');
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
