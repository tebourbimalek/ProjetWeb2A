<?php
require_once "C:/xampp/htdocs/Tunify/config.php";

class GestionReclamationController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    private function reclamationExists($id)
    {
        try {
            $query = $this->pdo->prepare("SELECT COUNT(*) FROM reclamations WHERE id = :id");
            $query->execute([':id' => $id]);
            return $query->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error checking reclamation: " . $e->getMessage());
        }
    }



    public function rejeterReclamation($id)
    {
        try {
            $query = $this->pdo->prepare("UPDATE reclamations SET status = 'rejected' WHERE id = :id");
            return $query->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new Exception("Error rejecting reclamation: " . $e->getMessage());
        }
    }

    public function supprimerReclamation($id)
{
    try {
        // Check if the reclamation exists
        if (!$this->reclamationExists($id)) {
            throw new Exception("No Reclamation found with the ID: $id");
        }

        // Delete responses first
        $query = $this->pdo->prepare("DELETE FROM responses WHERE reclamation_id = :id");
        $query->execute([':id' => $id]);

        // Delete the reclamation
        $query = $this->pdo->prepare("DELETE FROM reclamations WHERE id = :id");
        $query->execute([':id' => $id]);

        // Check if the reclamation was deleted
        if ($query->rowCount() > 0) {
            return true;
        } else {
            throw new Exception("Failed to delete reclamation with ID: $id");
        }
    } catch (PDOException $e) {
        throw new Exception("Error deleting reclamation: " . $e->getMessage());
    }
}

<<<<<<< HEAD
public function repondreReclamation($id, $content)
=======
public function repondreReclamation($id, $response)
>>>>>>> 7661ae56cb2bbf27b7bf8ea7984db269fbca48a9
{
    try {
        $this->pdo->beginTransaction();

        // Update reclamation
        $updateQuery = $this->pdo->prepare(
            "UPDATE reclamations 
             SET response = :response, status = 'resolved', updated_at = NOW() 
             WHERE id = :id"
        );
<<<<<<< HEAD
        $updateQuery->execute([
            ':response' => $content,
            ':id' => $id
        ]);
=======
        $updateQuery->execute([$response, $id]);
>>>>>>> 7661ae56cb2bbf27b7bf8ea7984db269fbca48a9

        // Add to responses table
        $responseQuery = $this->pdo->prepare(
            "INSERT INTO responses (reclamation_id, content, created_at)
             VALUES (:reclamation_id, :content, NOW())"
        );
        $responseQuery->execute([
            ':reclamation_id' => $id,
<<<<<<< HEAD
            ':content' => $content
        ]);

        $this->pdo->commit();
        return true;
=======
            ':content' => $content,
            ':created_at' => NOW(),
        ]);

        $this->pdo->commit();
         return true;
>>>>>>> 7661ae56cb2bbf27b7bf8ea7984db269fbca48a9
    } catch (PDOException $e) {
        $this->pdo->rollBack();
        throw new Exception("Error responding to reclamation: " . $e->getMessage());
    }
}

<<<<<<< HEAD
public function modifierReponse($id, $response)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE reclamations SET response = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$response, $id]);
        } catch (PDOException $e) {
            throw new Exception("Error updating response: " . $e->getMessage());
        }
    }

    public function supprimerReponse($id)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE reclamations SET response = NULL, status = 'pending', updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting response: " . $e->getMessage());
        }
    }

public function countReclamations()
{
    try {
        return [
            'total' => $this->pdo->query("SELECT COUNT(*) FROM reclamations")->fetchColumn(),
            'pending' => $this->pdo->query("SELECT COUNT(*) FROM reclamations WHERE status = 'pending'")->fetchColumn(),
            'resolved' => $this->pdo->query("SELECT COUNT(*) FROM reclamations WHERE status = 'resolved'")->fetchColumn(),
            'rejected' => $this->pdo->query("SELECT COUNT(*) FROM reclamations WHERE status = 'rejected'")->fetchColumn()
        ];
    } catch (PDOException $e) {
        throw new Exception("Error getting stats: " . $e->getMessage());
    }
}

=======
    public function countReclamations()
    {
        try {
            return [
                'total' => $this->pdo->query("SELECT COUNT(*) FROM reclamations")->fetchColumn(),
                'pending' => $this->pdo->query("SELECT COUNT(*) FROM reclamations WHERE status = 'pending'")->fetchColumn(),
                'resolved' => $this->pdo->query("SELECT COUNT(*) FROM reclamations WHERE status = 'resolved'")->fetchColumn()
            ];
        } catch (PDOException $e) {
            throw new Exception("Error getting stats: " . $e->getMessage());
        }
    }
>>>>>>> 7661ae56cb2bbf27b7bf8ea7984db269fbca48a9
}
?>