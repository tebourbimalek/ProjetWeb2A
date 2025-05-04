<?php
require_once('../model/config.php');

session_start();
// Pour TEST SEULEMENT - Simule un user_id si non connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // À RETIRER EN PRODUCTION
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

try {
    $pdo = config::getConnexion();

    // Pour TEST - Insère une notification si table vide
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->fetchColumn() == 0) {
        $testMessages = [
            "Bienvenue sur Tunify Premium!",
            "Votre abonnement Familial est actif",
            "Paiement reçu - Merci!",
            "Nouveau contenu disponible cette semaine"
        ];
        
        foreach ($testMessages as $message) {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, est_lue) VALUES (?, ?, 0)");
            $stmt->execute([$user_id, $message]);
        }
    }

    if ($action === 'get_notifications') {
        $stmt = $pdo->prepare("
            SELECT *, 
                   DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as formatted_date
            FROM notifications 
            WHERE user_id = ? 
            ORDER BY date_creation DESC 
            LIMIT 15
        ");
        $stmt->execute([$user_id]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM notifications 
            WHERE user_id = ? AND est_lue = 0
        ");
        $stmt->execute([$user_id]);
        $unread_count = $stmt->fetchColumn();

        echo json_encode([
            'notifications' => $notifications,
            'unread_count' => $unread_count
        ]);
    }
    elseif ($action === 'mark_all_read') {
        $stmt = $pdo->prepare("
            UPDATE notifications 
            SET est_lue = 1 
            WHERE user_id = ? AND est_lue = 0
        ");
        $stmt->execute([$user_id]);
        echo json_encode(['status' => 'success', 'count' => $stmt->rowCount()]);
    }
    else {
        echo json_encode(['error' => 'Action non reconnue']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur de base de données: ' . $e->getMessage()]);
}