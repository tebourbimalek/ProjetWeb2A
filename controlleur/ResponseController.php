<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\Response.php';
require_once 'C:\xampp\htdocs\projetweb\model\Reclamation.php';


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
            error_log('Error adding response: ' . $e->getMessage());
            return false;
        }
    }

    public function getResponsesByReclamationId($reclamation_id) {
        try {
            return Response::getByReclamationId($reclamation_id);
        } catch (PDOException $e) {
            error_log('Error getting responses: ' . $e->getMessage());
            return [];
        }
    }

    public function deleteResponse($id) {
        try {
            return Response::delete($id);
        } catch (PDOException $e) {
            error_log('Error deleting response: ' . $e->getMessage());
            return false;
        }
    }

}
?>