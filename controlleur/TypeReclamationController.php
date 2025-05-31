<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\TypeReclamation.php';

class TypeReclamationController
{
    private $pdo;

    // Constructor to initialize the PDO connection
    public function __construct()
    {
        $this->pdo = Config::getConnexion();
    }

    // List all reclamation types
    public function listeTypes()
    {
        try {
            // Get all types from the database
            $query = $this->pdo->query("SELECT * FROM types_reclamation");
            $types = $query->fetchAll(PDO::FETCH_OBJ);
        
            // Custom sorting: Technical Issue first, Other last, rest alphabetically
            usort($types, function($a, $b) {
                // Technical Issue always first
                if ($a->type === 'Technical Issue') return -1;
                if ($b->type === 'Technical Issue') return 1;
            
                // Other always last
                if ($a->type === 'Other') return 1;
                if ($b->type === 'Other') return -1;
            
                // Sort the rest alphabetically
                return strcmp($a->type, $b->type);
            });
        
            return $types;
        } catch (PDOException $e) {
            error_log('Error: ' . $e->getMessage());
            return [];
        }
    }

    // Get reclamation type percentages
    public function getTypePercentages()
    {
        try {
            $query = $this->pdo->query("
                SELECT tr.type, 
                       COUNT(r.id) AS count, 
                       (COUNT(r.id) * 100 / (SELECT COUNT(*) FROM reclamations)) AS percentage
                FROM types_reclamation tr
                LEFT JOIN reclamations r ON r.type_id = tr.id
                GROUP BY tr.id
            ");
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error: ' . $e->getMessage());
            return [];
        }
    }

    // Add a new reclamation type
    public function addType($type)
    {
        try {
            // Check if type already exists
            $checkQuery = $this->pdo->prepare("SELECT COUNT(*) FROM types_reclamation WHERE type = :type");
            $checkQuery->execute([':type' => $type]);
            
            if ($checkQuery->fetchColumn() > 0) {
                throw new Exception("This reclamation type already exists");
            }
            
            // Create and save the new type
            $typeObj = new TypeReclamation($type);
            return $typeObj->save();
        } catch (PDOException $e) {
            error_log("Error adding type: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        } catch (Exception $e) {
            throw $e; // Re-throw the exception
        }
    }

    // Delete a reclamation type
    public function deleteType($id)
    {
        try {
            return TypeReclamation::delete($id);
        } catch (PDOException $e) {
            error_log("Error deleting type: " . $e->getMessage());
            throw new Exception("Failed to delete type");
        }
    }

    // Update a reclamation type
    public function updateType($id, $type)
    {
        try {
            return TypeReclamation::update($id, $type);
        } catch (PDOException $e) {
            error_log("Error updating type: " . $e->getMessage());
            throw new Exception("Failed to update type");
        }
    }
}
?>
