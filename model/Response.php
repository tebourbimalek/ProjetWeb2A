<?php
require_once "C:/xampp/htdocs/Tunify/config.php"; // Inclusion de la configuration de la base de données

class Reponse {
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
        $stmt = $pdo->prepare("INSERT INTO responses (reclamation_id, content, created_at) VALUES (?, ?, ?)");
        return $stmt->execute([$this->reclamation_id, $this->content, $this->created_at]);
    }

    public static function getByReclamation($reclamation_id) {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM responses WHERE reclamation_id = ?");
        $stmt->execute([$reclamation_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>