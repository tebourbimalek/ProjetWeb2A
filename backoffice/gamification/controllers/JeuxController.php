<?php
require_once __DIR__ . '/../models/jeu.php';
require_once __DIR__ . '/../models/question.php';

class JeuxController {
    private $db;
    private $jeuModel;
    private $questionModel;
    private $uploadPath;

    public function __construct($db) {
        $this->db = $db;
        $this->jeuModel = new Jeu($db);
        $this->questionModel = new Question($db);
        $this->uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/tunifiy(gamification)/sources/uploads/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }

        $this->handleRequests();
    }

    private function handleRequests() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
            $action = $_GET['action'];
            switch ($action) {
                case 'load_questions':
                    $this->loadQuestions($_GET['id_game']);
                    break;
                case 'get_question':
                    $this->getQuestion();
                    break;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
            $action = $_GET['action'];
            switch ($action) {
                case 'add':
                    $this->add();
                    break;
                case 'update':
                    $this->update();
                    break;
                case 'delete':
                    $this->delete();
                    break;
                case 'add_question':
                    $this->addQuestion();
                    break;
                case 'update_question':
                    $this->updateQuestion();
                    break;
                case 'delete_question':
                    $this->deleteQuestion();
                    break;
            }
        }
    }

    public function index() {
        return $this->jeuModel->getAll();
    }

    private function add() {
        $nom_jeu = $_POST['nom_jeu'] ?? '';
        $type_jeu = $_POST['type_jeu'] ?? '';
        $points = $_POST['points_attribues'] ?? 0;
        $statut = $_POST['statut'] ?? 'inactif';
        $coverPath = $this->handleFileUpload('cover');
    
        $success = $this->jeuModel->create($nom_jeu, $type_jeu, $points, $statut, $coverPath);
        
        if ($success) {
            header('Location: backoffice.php');
            exit;
        } else {
            http_response_code(500);
            echo "Failed to add game";
            exit;
        }
    }
    
    private function update() {
        $id_game = $_POST['id_game'] ?? null;
        $nom_jeu = $_POST['nom_jeu'] ?? '';
        $type_jeu = $_POST['type_jeu'] ?? '';
        $points = $_POST['points_attribues'] ?? 0;
        $statut = $_POST['statut'] ?? 'inactif';
        $existingCover = $_POST['existing_cover_path'] ?? null;
        $coverPath = $this->handleFileUpload('cover', $existingCover);
    
        $success = $this->jeuModel->update($id_game, $nom_jeu, $type_jeu, $points, $statut, $coverPath);
        
        if ($success) {
            header('Location: backoffice.php');
            exit;
        } else {
            http_response_code(500);
            echo "Failed to update game";
            exit;
        }
    }

    private function delete() {
        $id = $_POST['id'] ?? null;
        if ($id === null) {
            http_response_code(400);
            echo "Missing game ID.";
            exit;
        }

        $success = $this->jeuModel->delete($id);

        if ($success) {
            echo "success";
        } else {
            http_response_code(500);
            echo "Failed to delete the game.";
        }
        exit;
    }

    public function loadQuestions($id_game) {
        $game = $this->jeuModel->getById($id_game);
        if (!$game) {
            http_response_code(404);
            echo json_encode(['error' => 'Game not found']);
            exit;
        }

        $questions = $this->questionModel->getQuestionsByGameId($id_game);
        
        // Format questions based on game type
        $formattedQuestions = [];
        foreach ($questions as $question) {
            switch ($game['type_jeu']) {
                case 'guess':
                    $formattedQuestions[] = [
                        'id_question' => $question['id_question'],
                        'question_text' => $question['question_text'],
                        'correct_answer' => $question['correct_answer'],
                        'image_path' => $question['image_path']
                    ];
                    break;
                    
                case 'quizz':
                    $formattedQuestions[] = [
                        'id_question' => $question['id_question'],
                        'question_text' => $question['question_text'],
                        'option_1' => $question['option_1'],
                        'option_2' => $question['option_2'],
                        'option_3' => $question['option_3'],
                        'option_4' => $question['option_4'],
                        'correct_option' => $question['correct_option'],
                        'image_path' => $question['image_path']
                    ];
                    break;
                    
                case 'puzzle':
                    $formattedQuestions[] = [
                        'id_question' => $question['id_question'],
                        'image_path' => $question['image_path'],
                        'is_true' => (bool)$question['is_true']
                    ];
                    break;
            }
        }
        
        echo json_encode($formattedQuestions);
        exit;
    }

    public function addQuestion() {
        $id_game = $_POST['id_game'] ?? null;
        if (!$id_game) {
            http_response_code(400);
            echo "Missing game ID";
            exit;
        }

        $game = $this->jeuModel->getById($id_game);
        if (!$game) {
            http_response_code(404);
            echo "Game not found";
            exit;
        }

        $questionData = ['id_game' => $id_game];
        
        switch ($game['type_jeu']) {
            case 'guess':
                $questionData['question_text'] = $_POST['question_text'] ?? '';
                $questionData['correct_answer'] = $_POST['correct_answer'] ?? '';
                $questionData['image_path'] = $this->handleFileUpload('image');
                break;
                
            case 'quizz':
                $questionData['question_text'] = $_POST['question_text'] ?? '';
                $questionData['option_1'] = $_POST['option_1'] ?? '';
                $questionData['option_2'] = $_POST['option_2'] ?? '';
                $questionData['option_3'] = $_POST['option_3'] ?? '';
                $questionData['option_4'] = $_POST['option_4'] ?? '';
                $questionData['correct_option'] = $_POST['correct_option'] ?? null;
                $questionData['image_path'] = $this->handleFileUpload('image');
                break;
                
            case 'puzzle':
                $questionData['image_path'] = $this->handleFileUpload('image');
                $questionData['is_true'] = isset($_POST['is_true']) ? 1 : 0;
                break;
        }

        if ($this->questionModel->create($questionData)) {
            echo "success";
        } else {
            http_response_code(500);
            echo "Failed to add question";
        }
        exit;
    }

    public function updateQuestion() {
        $id_question = $_POST['id_question'] ?? null;
        $id_game = $_POST['id_game'] ?? null;
        
        if (!$id_question || !$id_game) {
            http_response_code(400);
            echo "Missing question or game ID";
            exit;
        }

        $game = $this->jeuModel->getById($id_game);
        if (!$game) {
            http_response_code(404);
            echo "Game not found";
            exit;
        }

        $questionData = [];
        
        switch ($game['type_jeu']) {
            case 'guess':
                $questionData['question_text'] = $_POST['question_text'] ?? '';
                $questionData['correct_answer'] = $_POST['correct_answer'] ?? '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $questionData['image_path'] = $this->handleFileUpload('image');
                }
                break;
                
            case 'quizz':
                $questionData['question_text'] = $_POST['question_text'] ?? '';
                $questionData['option_1'] = $_POST['option_1'] ?? '';
                $questionData['option_2'] = $_POST['option_2'] ?? '';
                $questionData['option_3'] = $_POST['option_3'] ?? '';
                $questionData['option_4'] = $_POST['option_4'] ?? '';
                $questionData['correct_option'] = $_POST['correct_option'] ?? null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $questionData['image_path'] = $this->handleFileUpload('image');
                }
                break;
                
            case 'puzzle':
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $questionData['image_path'] = $this->handleFileUpload('image');
                }
                $questionData['is_true'] = isset($_POST['is_true']) ? 1 : 0;
                break;
        }

        if ($this->questionModel->update($id_question, $questionData)) {
            echo "success";
        } else {
            http_response_code(500);
            echo "Failed to update question";
        }
        exit;
    }

    public function deleteQuestion() {
        $id_question = $_POST['id_question'] ?? null;
    
        if (!$id_question) {
            http_response_code(400);
            echo "Missing question ID.";
            exit;
        }
    
        if ($this->questionModel->delete($id_question)) {
            echo "success";
        } else {
            http_response_code(500);
            echo "Failed to delete question.";
        }
        exit;
    }

    public function getQuestion() {
        $id_question = $_GET['id'] ?? null;
        if (!$id_question) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Missing question ID']);
            exit;
        }
    
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE id_question = ?");
        $stmt->execute([$id_question]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$question) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Question not found']);
            exit;
        }
    
        header('Content-Type: application/json');
        echo json_encode($question);
        exit;
    }

    private function handleFileUpload($fieldName, $existingPath = null) {
        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $filename = uniqid() . '_' . basename($_FILES[$fieldName]['name']);
            $targetPath = $this->uploadPath . $filename;
    
            if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetPath)) {
                // Return relative path for database storage
                return $filename;
            }
        }
        return $existingPath;
    }
}