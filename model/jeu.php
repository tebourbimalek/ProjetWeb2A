<?php
// backoffice/models/jeu.php

class Jeu {
    private $conn;
    private $table = 'jeux';  // Change 'games' to 'jeux'

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all games
    public function getAll() {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->query($query);
        $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $games;
    }

    // Fetch a single game by ID
    public function getById($id_game) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id_game = :id_game';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_game', $id_game);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new game
    public function create($nom_jeu, $type_jeu, $points_attribues, $statut, $cover_path) {
        $query = 'INSERT INTO ' . $this->table . ' (nom_jeu, type_jeu, points_attribues, statut, cover_path)
                  VALUES (:nom_jeu, :type_jeu, :points_attribues, :statut, :cover_path)';
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':nom_jeu', $nom_jeu);
        $stmt->bindParam(':type_jeu', $type_jeu);
        $stmt->bindParam(':points_attribues', $points_attribues);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':cover_path', $cover_path);

        // Execute and return success status
        return $stmt->execute();
    }

    // Update a game
    public function update($id_game, $nom_jeu, $type_jeu, $points_attribues, $statut, $cover_path) {
        $query = 'UPDATE ' . $this->table . ' SET nom_jeu = :nom_jeu, type_jeu = :type_jeu, 
                  points_attribues = :points_attribues, statut = :statut, cover_path = :cover_path 
                  WHERE id_game = :id_game';
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id_game', $id_game);
        $stmt->bindParam(':nom_jeu', $nom_jeu);
        $stmt->bindParam(':type_jeu', $type_jeu);
        $stmt->bindParam(':points_attribues', $points_attribues);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':cover_path', $cover_path);

        // Execute and return success status
        return $stmt->execute();
    }

    // Delete a game
    public function delete($id_game) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id_game = :id_game';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_game', $id_game, PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    public function getActiveGame($id_game) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id_game = :id_game AND statut = "actif"';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_game', $id_game);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
