<?php
header('Content-Type: application/json');
require_once 'C:\xampp\htdocs\projetweb\controlleur\NewsController.php';

session_start();

// On définit un délai de 24h pour voir les publications récentes
$yesterday = date('Y-m-d H:i:s', strtotime('-24 hours'));

// Si la dernière vérification n'existe pas ou est trop ancienne, on utilise celle d'hier
if (!isset($_SESSION['last_check']) || $_SESSION['last_check'] < $yesterday) {
    $_SESSION['last_check'] = $yesterday;
}

try {
    $controller = new NewsController();
    $notifications = $controller->getNewNotifications($_SESSION['last_check']);
    
    // Mettre à jour la dernière vérification
    $_SESSION['last_check'] = date('Y-m-d H:i:s');

    echo json_encode(['notifications' => $notifications]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 