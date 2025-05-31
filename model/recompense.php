<?php
class Recompense {
    private $conn;
    private $table = 'recompenses';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all rewards ordered by points required (ascending)
    public function getAll() {
        try {
            $query = 'SELECT * FROM ' . $this->table . ' ORDER BY id_reward DESC';
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . implode(" ", $stmt->errorInfo()));
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Database results: " . print_r($results, true)); // Debug
            return $results;
        } catch (Exception $e) {
            error_log("Model Error: " . $e->getMessage());
            return [];
        }
    }

    // Fetch a single reward by ID
    public function getById($id_reward) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id_reward = :id_reward LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_reward', $id_reward, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new reward with image path
    public function create($nom_reward, $points_requis, $type_reward, $disponibilite, $image_path = null) {
        try {
            $query = "INSERT INTO {$this->table} 
                     (nom_reward, points_requis, type_reward, disponibilite, image_path)
                     VALUES (:nom, :points, :type, :dispo, :image)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':nom', $nom_reward);
            $stmt->bindValue(':points', $points_requis, PDO::PARAM_INT);
            $stmt->bindValue(':type', $type_reward);
            $stmt->bindValue(':dispo', $disponibilite, PDO::PARAM_INT);
            $stmt->bindValue(':image', $image_path, $image_path ? PDO::PARAM_STR : PDO::PARAM_NULL);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    // Update a reward (including optional image update)
    public function update($id_reward, $nom_reward, $points_requis, $type_reward, $disponibilite, $image_path = null) {
        $query = 'UPDATE ' . $this->table . ' SET 
                  nom_reward = :nom_reward, 
                  points_requis = :points_requis, 
                  type_reward = :type_reward, 
                  disponibilite = :disponibilite';
        
        // Only update image_path if provided
        if ($image_path !== null) {
            $query .= ', image_path = :image_path';
        }
        
        $query .= ' WHERE id_reward = :id_reward';
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_reward', $id_reward, PDO::PARAM_INT);
        $stmt->bindParam(':nom_reward', $nom_reward, PDO::PARAM_STR);
        $stmt->bindParam(':points_requis', $points_requis, PDO::PARAM_INT);
        $stmt->bindParam(':type_reward', $type_reward, PDO::PARAM_STR);
        $stmt->bindParam(':disponibilite', $disponibilite, PDO::PARAM_INT);
        
        if ($image_path !== null) {
            $stmt->bindParam(':image_path', $image_path, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    // Delete a reward
    public function delete($id_reward) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id_reward = :id_reward';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_reward', $id_reward, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Get rewards by availability status
    public function getByAvailability($disponibilite = 1) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE disponibilite = :disponibilite ORDER BY points_requis ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':disponibilite', $disponibilite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
?>