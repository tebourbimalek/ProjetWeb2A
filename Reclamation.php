<?php
require_once "C:/xampp/htdocs/Tunify/config.php"; // Inclusion de la configuration de la base de données

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

    public function __construct($full_name, $email, $cause, $description, $screenshot = null) {
        $this->full_name = $full_name;
        $this->email = $email;
        $this->cause = $cause;
        $this->description = $description;
        $this->screenshot = $screenshot;
        $this->status = 'pending';
        $this->created_at = date('Y-m-d H:i:s');
    }

    // Getters
    public function getId() { return $this->id; }
    public function getFullName() { return $this->full_name; }
    public function getEmail() { return $this->email; }
    public function getCause() { return $this->cause; }
    public function getDescription() { return $this->description; }
    public function getScreenshot() { return $this->screenshot; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->created_at; }
    public function getResponse() { return $this->response; }

    // Database operations
    public static function getAll() {
        $pdo = Config::getConnexion();
        $stmt = $pdo->query("SELECT * FROM reclamations");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function getById($id) {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM reclamations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public static function getByStatus($status) {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM reclamations WHERE status = ?");
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
        $stmt = $pdo->prepare("INSERT INTO reclamations (full_name, email, cause, description, screenshot, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $this->full_name,
            $this->email,
            $this->cause,
            $this->description,
            $this->screenshot,
            $this->status,
            $this->created_at
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
<<<<<<< HEAD

    public function updateReclamation($idReclamation, $data) {
        try {
            $db = Config::getConnexion();
            
            // Fixed table name from "reclamation" to "reclamations"
            $query = "UPDATE reclamations SET 
                      cause = :cause, 
                      description = :description 
                      WHERE id = :id";
            
            $statement = $db->prepare($query);
            $statement->bindParam(':cause', $data['cause']);
            $statement->bindParam(':description', $data['description']);
            $statement->bindParam(':id', $idReclamation, PDO::PARAM_INT);
            
            $result = $statement->execute();
            
            // Log the result
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
            
            $query = "UPDATE reclamations SET 
                      cause = :cause, 
                      description = :description,
                      screenshot = :screenshot
                      WHERE id = :id";
            
            $statement = $db->prepare($query);
            $statement->bindParam(':cause', $data['cause']);
            $statement->bindParam(':description', $data['description']);
            $statement->bindParam(':screenshot', $screenshotPath);
            $statement->bindParam(':id', $idReclamation, PDO::PARAM_INT);
            
            $result = $statement->execute();
            
            // Log the result
            error_log("Update with screenshot result: " . ($result ? "success" : "failure"));
            error_log("Rows affected: " . $statement->rowCount());
            
            return $result;
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }
=======
>>>>>>> 7661ae56cb2bbf27b7bf8ea7984db269fbca48a9
}
?>