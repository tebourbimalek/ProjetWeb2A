<?php
require_once "C:/xampp/htdocs/Tunify/config.php";
require_once "C:/xampp/htdocs/Tunify/model/Reclamation.php";

class ReponseController
{
    public function addResponse($reclamationId, $response)
    {
        $pdo = Config::getConnexion();
        try {
            $stmt = $pdo->prepare(
                "UPDATE reclamations 
                 SET response = ?, status = 'resolved', updated_at = NOW() 
                 WHERE id = ?"
            );
            return $stmt->execute([$response, $reclamationId]);
        } catch (PDOException $e) {
            error_log('Error adding response: ' . $e->getMessage());
            return false;
        }
    }

    public function getResponses($reclamationId)
    {
        $pdo = Config::getConnexion();
        try {
            $stmt = $pdo->prepare(
                "SELECT response, created_at 
                 FROM reclamations 
                 WHERE id = ? AND response IS NOT NULL"
            );
            $stmt->execute([$reclamationId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Error getting responses: ' . $e->getMessage());
            return [];
        }
    }
}
?>