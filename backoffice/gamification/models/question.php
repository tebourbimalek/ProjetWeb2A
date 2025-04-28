<?php
class Question {
    private $db;
    private $table = 'questions';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getQuestionsByGameId($id_game) {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE id_game = ?");
        $stmt->execute([$id_game]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO questions SET ";
        $fields = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if ($value !== null) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $value;
            }
        }
        
        $query .= implode(', ', $fields);
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function update($id_question, $data) {
        $query = "UPDATE questions SET ";
        $fields = [];
        $params = [':id_question' => $id_question];
        
        foreach ($data as $field => $value) {
            if ($value !== null) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $value;
            }
        }
        
        $query .= implode(', ', $fields) . " WHERE id_question = :id_question";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function delete($question_id) {
        $stmt = $this->db->prepare("DELETE FROM questions WHERE id_question = ?");
        return $stmt->execute([$question_id]);
    }
}