<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';

class Response {
    private $id;
    private $reclamation_id;
    private $content;
    private $created_at;

    public function __construct($reclamation_id, $content) {
        $this->reclamation_id = $reclamation_id;
        $this->content = $content;
        $this->created_at = date('Y-m-d H:i:s');
    }

    // Getters
    public function getId() { return $this->id; }
    public function getReclamationId() { return $this->reclamation_id; }
    public function getContent() { return $this->content; }
    public function getCreatedAt() { return $this->created_at; }

    // Database operations
    public function save() {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO responses (reclamation_id, content, created_at) 
            VALUES (?, ?, ?)");
        return $stmt->execute([
            $this->reclamation_id,
            $this->content,
            $this->created_at
        ]);
    }

    public static function getByReclamationId($reclamation_id) {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM responses WHERE reclamation_id = ? ORDER BY created_at DESC");
        $stmt->execute([$reclamation_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function getById($id) {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public static function delete($id) {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM responses WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>