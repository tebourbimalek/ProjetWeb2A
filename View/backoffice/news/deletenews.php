<?php
session_start();

require_once 'C:/xampp/htdocs/projetweb/model/config.php';
require_once 'C:/xampp/htdocs/projetweb/controlleur/functionsnews.php';

// Use the correct parameter name from the URL: 'news_id'
if (isset($_GET['news_id']) && is_numeric($_GET['news_id'])) {
    $id_news = (int)$_GET['news_id'];

    try {
        $pdo = config::getConnexion();

        if (deleteNewsById($pdo, $id_news)) {
            $_SESSION['message'] = "🗑️ News supprimée avec succès.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "❌ Erreur lors de la suppression de la news.";
            $_SESSION['message_type'] = "error";
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = "❌ Erreur : " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

} else {
    $_SESSION['message'] = "⚠️ ID de news invalide.";
    $_SESSION['message_type'] = "error";
}

// Redirect back to the news management page
header("Location: /projetweb/View/backoffice/backoffice.php"); 
exit;
