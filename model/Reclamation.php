<?php 
require_once 'C:\xampp\htdocs\projetweb\model\config.php'; 

class Reclamation { 
    private $id; 
    private $full_name; 
    private $email; 
    private $cause; 
    private $description; 
    private $screenshot; 
    private $status; 
    private $created_at; 
    private $updated_at; 
    private $response;
    private $type_id;

    public function __construct($full_name, $email, $cause, $description, $screenshot = null, $type_id = null) { 
        $this->full_name = $full_name; 
        $this->email = $email; 
        $this->cause = $cause; 
        $this->description = $description; 
        $this->screenshot = $screenshot; 
        $this->status = 'pending'; 
        $this->created_at = date('Y-m-d H:i:s');
        $this->type_id = $type_id;
    } 

    // Getters 
    public function getId() { 
        return $this->id; 
    } 
    
    public function getFullName() { 
        return $this->full_name; 
    } 
    
    public function getEmail() { 
        return $this->email; 
    } 
    
    public function getCause() { 
        return $this->cause; 
    } 
    
    public function getDescription() { 
        return $this->description; 
    } 
    
    public function getScreenshot() { 
        return $this->screenshot; 
    } 
    
    public function getStatus() { 
        return $this->status; 
    } 
    
    public function getCreatedAt() { 
        return $this->created_at; 
    } 
    
    public function getResponse() { 
        return $this->response; 
    }
    
    public function getTypeId() {
        return $this->type_id;
    }

    // Database operations 
    public static function getAll() { 
        $pdo = Config::getConnexion(); 
        $stmt = $pdo->query("SELECT r.*, t.type as type_name FROM reclamations r 
                            LEFT JOIN types_reclamation t ON r.type_id = t.id"); 
        return $stmt->fetchAll(PDO::FETCH_OBJ); 
    } 
    
    public static function getById($id) { 
        $pdo = Config::getConnexion(); 
        $stmt = $pdo->prepare("SELECT r.*, t.type as type_name FROM reclamations r 
                              LEFT JOIN types_reclamation t ON r.type_id = t.id 
                              WHERE r.id = ?"); 
        $stmt->execute([$id]); 
        return $stmt->fetch(PDO::FETCH_OBJ); 
    } 
    
    public static function getByStatus($status) { 
        $pdo = Config::getConnexion(); 
        $stmt = $pdo->prepare("SELECT r.*, t.type as type_name FROM reclamations r 
                              LEFT JOIN types_reclamation t ON r.type_id = t.id 
                              WHERE r.status = ?"); 
        $stmt->execute([$status]); 
        return $stmt->fetchAll(PDO::FETCH_OBJ); 
    } 
    
    public static function getStatistics() { 
        $pdo = Config::getConnexion(); 
        return [ 
            'total' => $pdo->query("SELECT COUNT(*) FROM reclamations")->fetchColumn(), 
            'pending' => $pdo->query("SELECT COUNT(*) FROM reclamations WHERE status = 'pending'")->fetchColumn(), 
            'resolved' => $pdo->query("SELECT COUNT(*) FROM reclamations WHERE status = 'resolved'")->fetchColumn() 
        ]; 
    } 
    
    public function save() { 
        $pdo = Config::getConnexion(); 
        $stmt = $pdo->prepare("INSERT INTO reclamations (full_name, email, cause, description, screenshot, status, created_at, type_id) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)"); 
        return $stmt->execute([ 
            $this->full_name, 
            $this->email, 
            $this->cause, 
            $this->description, 
            $this->screenshot, 
            $this->status, 
            $this->created_at,
            $this->type_id
        ]); 
    } 
    
    public static function addResponse($id, $response) { 
        $pdo = Config::getConnexion(); 
        $stmt = $pdo->prepare("UPDATE reclamations SET response = ?, status = 'resolved', updated_at = NOW() WHERE id = ?"); 
        return $stmt->execute([$response, $id]); 
    } 
    
    public static function delete($id) { 
        $pdo = Config::getConnexion(); 
        $stmt = $pdo->prepare("DELETE FROM reclamations WHERE id = ?"); 
        return $stmt->execute([$id]); 
    } 
    
    public function updateReclamation($idReclamation, $data) { 
        try { 
            $db = Config::getConnexion(); 
            $query = "UPDATE reclamations SET cause = :cause, description = :description";
            
            // Add type_id to update if provided
            if (isset($data['type_id'])) {
                $query .= ", type_id = :type_id";
            }
            
            $query .= " WHERE id = :id"; 
            $statement = $db->prepare($query); 
            $statement->bindParam(':cause', $data['cause']); 
            $statement->bindParam(':description', $data['description']); 
            
            if (isset($data['type_id'])) {
                $statement->bindParam(':type_id', $data['type_id'], PDO::PARAM_INT);
            }
            
            $statement->bindParam(':id', $idReclamation, PDO::PARAM_INT); 
            $result = $statement->execute(); 
            
            error_log("Update result: " . ($result ? "success" : "failure")); 
            error_log("Rows affected: " . $statement->rowCount()); 
            return $result; 
        } catch (Exception $e) { 
            error_log('Database error: ' . $e->getMessage()); 
            return false; 
        } 
    } 
    
    public function updateReclamationWithScreenshot($idReclamation, $data, $screenshotPath) { 
        try { 
            $db = Config::getConnexion(); 
            $query = "UPDATE reclamations SET cause = :cause, description = :description, screenshot = :screenshot";
            
            // Add type_id to update if provided
            if (isset($data['type_id'])) {
                $query .= ", type_id = :type_id";
            }
            
            $query .= " WHERE id = :id"; 
            $statement = $db->prepare($query); 
            $statement->bindParam(':cause', $data['cause']); 
            $statement->bindParam(':description', $data['description']); 
            $statement->bindParam(':screenshot', $screenshotPath); 
            
            if (isset($data['type_id'])) {
                $statement->bindParam(':type_id', $data['type_id'], PDO::PARAM_INT);
            }
            
            $statement->bindParam(':id', $idReclamation, PDO::PARAM_INT); 
            $result = $statement->execute(); 
            
            error_log("Update with screenshot result: " . ($result ? "success" : "failure")); 
            error_log("Rows affected: " . $statement->rowCount()); 
            return $result; 
        } catch (Exception $e) { 
            error_log('Database error: ' . $e->getMessage()); 
            return false; 
        } 
    } 
} 
?>
