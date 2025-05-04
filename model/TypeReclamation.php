<?php
require_once "C:/xampp/htdocs/Tunify/config.php";

class TypeReclamation {
    private $id;
    private $type;
    private $created_at;

    public function __construct($type = null, $id = null, $created_at = null) {
        $this->type = $type;
        $this->id = $id;
        $this->created_at = $created_at ?: date('Y-m-d H:i:s');
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getType() {
        return $this->type;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Database operations
    public static function getAll() {
        $pdo = Config::getConnexion();
        $stmt = $pdo->query("SELECT * FROM types_reclamation ORDER BY type ASC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function getById($id) {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM types_reclamation WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function save() {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO types_reclamation (type) VALUES (?)");
        return $stmt->execute([$this->type]);
    }

    public static function delete($id) {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("DELETE FROM types_reclamation WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function update($id, $type) {
        $pdo = Config::getConnexion();
        $stmt = $pdo->prepare("UPDATE types_reclamation SET type = ? WHERE id = ?");
        return $stmt->execute([$type, $id]);
    }
}
?>
