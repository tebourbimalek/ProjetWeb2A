<?php
require_once "C:/xampp/htdocs/Tunify/config.php";
<<<<<<< HEAD
require_once "C:/xampp/htdocs/Tunify/model/Response.php";
require_once "C:/xampp/htdocs/Tunify/model/Reclamation.php";

class ResponseController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function addResponse($reclamation_id, $content) {
        try {
            $this->pdo->beginTransaction();

            // Create and save the response
            $response = new Response($reclamation_id, $content);
            $response->save();

            // Update the reclamation status to resolved
            $stmt = $this->pdo->prepare(
                "UPDATE reclamations 
                 SET status = 'resolved', response = ?, updated_at = NOW() 
                 WHERE id = ?"
            );
            $stmt->execute([$content, $reclamation_id]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
=======
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
>>>>>>> 7661ae56cb2bbf27b7bf8ea7984db269fbca48a9
            error_log('Error adding response: ' . $e->getMessage());
            return false;
        }
    }

<<<<<<< HEAD
    public function getResponsesByReclamationId($reclamation_id) {
        try {
            return Response::getByReclamationId($reclamation_id);
=======
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
>>>>>>> 7661ae56cb2bbf27b7bf8ea7984db269fbca48a9
        } catch (PDOException $e) {
            error_log('Error getting responses: ' . $e->getMessage());
            return [];
        }
    }
<<<<<<< HEAD

    public function deleteResponse($id) {
        try {
            return Response::delete($id);
        } catch (PDOException $e) {
            error_log('Error deleting response: ' . $e->getMessage());
            return false;
        }
    }
=======
>>>>>>> 7661ae56cb2bbf27b7bf8ea7984db269fbca48a9
}
?>