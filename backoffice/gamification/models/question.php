<?php
class Question {
    private $db;
    private $table = 'questions';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getQuestionsByGameId($id_game) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_game = ?");
        $stmt->execute([$id_game]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_question) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id_question = ?");
        $stmt->execute([$id_question]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $allowedFields = [
            'id_game', 'question_text', 'correct_answer', 
            'option_1', 'option_2', 'option_3', 'option_4',
            'correct_option', 'is_true', 'image_path', 'mp3_path'
        ];
        
        $filteredData = array_intersect_key($data, array_flip($allowedFields));
        
        $query = "INSERT INTO {$this->table} SET ";
        $fields = [];
        $params = [];
        
        foreach ($filteredData as $field => $value) {
            $fields[] = "`$field` = :$field";
            $params[":$field"] = $value;
        }
        
        $query .= implode(', ', $fields);
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }
    
    public function update($id_question, $data) {
        $allowedFields = [
            'question_text', 'correct_answer', 
            'option_1', 'option_2', 'option_3', 'option_4',
            'correct_option', 'is_true', 'image_path', 'mp3_path'
        ];
        
        $filteredData = array_intersect_key($data, array_flip($allowedFields));
        
        $query = "UPDATE {$this->table} SET ";
        $fields = [];
        $params = [':id_question' => $id_question];
        
        foreach ($filteredData as $field => $value) {
            $fields[] = "`$field` = :$field";
            $params[":$field"] = $value;
        }
        
        $query .= implode(', ', $fields) . " WHERE id_question = :id_question";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function delete($question_id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_question = ?");
        return $stmt->execute([$question_id]);
    }
    public function getAudioQuestions($limit = 10) {
        // For MariaDB/MySQL, we need to use proper parameter binding for LIMIT
        $sql = "SELECT * FROM questions 
                WHERE mp3_path IS NOT NULL AND mp3_path != ''
                AND option_1 IS NOT NULL AND option_2 IS NOT NULL
                AND option_3 IS NOT NULL AND option_4 IS NOT NULL
                ORDER BY RAND() 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPuzzleQuestions($limit = 5) {
        $sql = "SELECT * FROM questions 
                WHERE image_path IS NOT NULL AND image_path != ''
                AND id_game IN (SELECT id_game FROM jeux WHERE type_jeu = 'puzzle')
                ORDER BY RAND() 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getQuestionsByGame($game_id, $limit = 5) {
        $query = "SELECT * FROM questions WHERE id_game = :game_id ORDER BY RAND() LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Add this method to your existing Question class

public function getValidQuestionsForGame($game_id, $limit = 5) {
    $query = "SELECT * FROM questions 
              WHERE id_game = :game_id
              AND question_text IS NOT NULL 
              AND question_text != '' 
              AND correct_answer IS NOT NULL 
              AND correct_answer != ''
              ORDER BY RAND() LIMIT :limit";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':game_id', $game_id);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
