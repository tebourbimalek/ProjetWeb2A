<?php
require_once 'C:\xampp\htdocs\projetweb\model\jeu.php';
require_once 'C:\xampp\htdocs\projetweb\model\question.php';
require_once 'C:\xampp\htdocs\projetweb\model\recompense.php';

class JeuxController {
    private $db;
    private $jeuModel;
    private $questionModel;
    private $recompenseModel;
    private $uploadPath;

    public function __construct($db) {
        $this->db = $db;
        $this->jeuModel = new Jeu($db);
        $this->questionModel = new Question($db);
        $this->recompenseModel = new Recompense($db);
        $this->uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/';

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
                case 'load_rewards': // Add this case
                    $this->loadRewards();
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
                case 'heardle':
                    $this->heardle();
                    break;
                case 'add_reward': // Add these new cases
                    $this->addReward();
                    break;
                case 'update_reward':
                    $this->updateReward();
                    break;
                case 'delete_reward':
                    $this->deleteReward();
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
            header('Location: /projetweb/View/backoffice/backoffice.php');
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
            header('Location: /projetweb/View/backoffice/backoffice.php');
            
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

    $questionData = ['id_game' => $id_game];
    $questionData['question_text'] = $_POST['question_text'] ?? '';
    
    // Handle file uploads
    $questionData['image_path'] = $this->handleFileUpload('image');
    $questionData['mp3_path'] = $this->handleFileUpload('mp3');

    // Handle different game types
    $game = $this->jeuModel->getById($id_game);
    switch ($game['type_jeu']) {
        case 'guess':
            $questionData['correct_answer'] = $_POST['correct_answer'] ?? '';
            break;
            
        case 'quizz':
        case 'puzzle':
            $questionData['option_1'] = $_POST['option_1'] ?? '';
            $questionData['option_2'] = $_POST['option_2'] ?? '';
            $questionData['option_3'] = $_POST['option_3'] ?? '';
            $questionData['option_4'] = $_POST['option_4'] ?? '';
            $questionData['correct_option'] = $_POST['correct_option'] ?? 1;
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

    $questionData = [];
    $questionData['question_text'] = $_POST['question_text'] ?? '';
    
    // Handle file uploads
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $questionData['image_path'] = $this->handleFileUpload('image');
    }
    if (isset($_FILES['mp3']) && $_FILES['mp3']['error'] === UPLOAD_ERR_OK) {
        $questionData['mp3_path'] = $this->handleFileUpload('mp3');
    }

    // Handle different game types
    $game = $this->jeuModel->getById($id_game);
    switch ($game['type_jeu']) {
        case 'guess':
            $questionData['correct_answer'] = $_POST['correct_answer'] ?? '';
            break;
            
        case 'quizz':
        case 'puzzle':
            $questionData['option_1'] = $_POST['option_1'] ?? '';
            $questionData['option_2'] = $_POST['option_2'] ?? '';
            $questionData['option_3'] = $_POST['option_3'] ?? '';
            $questionData['option_4'] = $_POST['option_4'] ?? '';
            $questionData['correct_option'] = $_POST['correct_option'] ?? 1;
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
            $file = $_FILES[$fieldName];
            $filename = uniqid() . '_' . basename($file['name']);
            $targetDir = $this->uploadPath;
            
            // Special handling for audio files
            if ($fieldName === 'mp3') {
                $targetDir .= 'audio/';
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                // Validate it's an MP3
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                if ($mime !== 'audio/mpeg') {
                    return $existingPath; // Invalid type, keep existing
                }
            }
            
            $targetPath = $targetDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return ($fieldName === 'mp3') ? $filename : $filename;
            }
        }
        return $existingPath;
    }
    private function handleAudioUpload($existingPath = null) {
        $uploadPath = $this->uploadPath . 'audio/';
        
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
    
        if (isset($_FILES['mp3']) && $_FILES['mp3']['error'] === UPLOAD_ERR_OK) {
            // Validate MP3 file
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['mp3']['tmp_name']);
            
            if ($mime !== 'audio/mpeg') {
                return $existingPath; // Invalid type, keep existing
            }
    
            $filename = uniqid() . '_' . basename($_FILES['mp3']['name']);
            $targetPath = $uploadPath . $filename;
    
            if (move_uploaded_file($_FILES['mp3']['tmp_name'], $targetPath)) {
                return $filename;
            }
        }
        return $existingPath;
    }
    public function heardle() {
        $gameType = 'heardle';
        $game = $this->jeuModel->getByType($gameType);
        
        if (!$game) {
            http_response_code(404);
            echo "Heardle game not configured";
            exit;
        }
        
        $questions = $this->questionModel->getAudioQuestions($game['id_game']);
        
        $_SESSION['heardle_game'] = [
            'game_id' => $game['id_game'],
            'questions' => $questions,
            'current_round' => 0,
            'score' => 0
        ];
        
        header('Location: ../View/tunify_avec_connexion/gamification/heardle.php');
        exit;
    }
    public function arcade() {
        try {
            $gameModel = new JeuModel(); // Assuming jeu.php becomes JeuModel
            $games = $gameModel->getAllGames();
            
            $this->view('games/arcade', [
                'games' => $games
            ]);
        } catch (PDOException $e) {
            // Handle error (could redirect to error page)
            error_log("Database error: " . $e->getMessage());
            $this->view('errors/database');
        }
    }
    public function playGuessGame($game_id) {
    session_start();
    
    // Initialize game session with dynamic key
    $session_key = 'game_' . $game_id;
    
    // Check if game exists and is active
    $game = $this->jeuModel->getActiveGame($game_id);
    if (!$game) {
        die("Game not found or inactive");
    }

    // Initialize or reset game
    if (!isset($_SESSION[$session_key])) {
        $questions = $this->questionModel->getQuestionsByGame($game_id, 5);
        
        if (count($questions) < 5) {
            die("Not enough valid questions available for this game");
        }

        $_SESSION[$session_key] = [
            'questions' => $questions,
            'current_round' => 0,
            'score' => 0,
            'state' => 'start',
            'user_answer' => '',
            'time_left' => 30,
            'game_id' => $game_id
        ];
    }
    
    $gameSession = &$_SESSION[$session_key];
    
    // Handle game actions
    if (isset($_GET['action'])) {
        $this->handleGuessGameAction($gameSession, $_GET['action']);
    }
    
    // Load the view with game data
    require_once __DIR__ . '/../View/tunify_avec_connexion/gamification/guess.php';
}

private function handleGuessGameAction(&$gameSession, $action) {
    switch ($action) {
        case 'start':
            $gameSession['state'] = 'countdown';
            break;
            
        case 'check_answer':
            $this->checkGuessAnswer($gameSession);
            break;

        case 'next':
            $this->nextGuessRound($gameSession);
            break;

        case 'restart':
            unset($_SESSION['game_' . $gameSession['game_id']]);
            header("Location: ".BASE_URL."/jeux/playGuess/".$gameSession['game_id']);
            exit;
            
        case 'play':
            $gameSession['state'] = 'playing';
            break;
    }
    
    header("Location: ".BASE_URL."/jeux/playGuess/".$gameSession['game_id']);
    exit;
}

private function checkGuessAnswer(&$gameSession) {
    if (isset($_POST['user_answer']) && $gameSession['state'] === 'playing') {
        $currentQuestion = $gameSession['questions'][$gameSession['current_round']];
        $gameSession['user_answer'] = trim($_POST['user_answer']);
        $correct_answer = trim(strtolower($currentQuestion['correct_answer']));
        $user_answer = trim(strtolower($gameSession['user_answer']));
        
        if ($user_answer === $correct_answer) {
            $gameSession['score']++;
        }
        $gameSession['state'] = 'feedback';
    }
}

private function nextGuessRound(&$gameSession) {
    $gameSession['current_round']++;
    if ($gameSession['current_round'] >= 5) {
        $gameSession['state'] = 'finished';
    } else {
        $gameSession['state'] = 'countdown';
        $gameSession['user_answer'] = '';
        $gameSession['time_left'] = 30;
    }
}
public function loadRewards() {
    $rewards = $this->recompenseModel->getAll();
    header('Content-Type: application/json');
    echo json_encode($rewards);
    exit;
}

public function addReward() {
    try {
        $nom_reward = $_POST['nom_reward'] ?? '';
        $points_requis = (int)($_POST['points_requis'] ?? 0);
        $type_reward = $_POST['type_reward'] ?? '';
        $disponibilite = (int)($_POST['disponibilite'] ?? 0);
        $image_path = $this->handleFileUpload('image');

        if (empty($nom_reward) || empty($type_reward)) {
            throw new Exception('Required fields are missing');
        }

        $success = $this->recompenseModel->create($nom_reward, $points_requis, $type_reward, $disponibilite, $image_path);
        
        if ($success) {
            echo "success";
        } else {
            throw new Exception('Database operation failed');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo $e->getMessage();
    }
    exit;
}

public function updateReward() {
    $id_reward = $_POST['id_reward'] ?? null;
    $nom_reward = $_POST['nom_reward'] ?? '';
    $points_requis = $_POST['points_requis'] ?? 0;
    $type_reward = $_POST['type_reward'] ?? '';
    $disponibilite = $_POST['disponibilite'] ?? 0;
    $existing_image = $_POST['existing_image'] ?? null;
    $image_path = $this->handleFileUpload('image', $existing_image);

    $success = $this->recompenseModel->update($id_reward, $nom_reward, $points_requis, $type_reward, $disponibilite, $image_path);
    
    if ($success) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Failed to update reward";
    }
    exit;
}

public function deleteReward() {
    $id_reward = $_POST['id_reward'] ?? null;
    if ($id_reward === null) {
        http_response_code(400);
        echo "Missing reward ID";
        exit;
    }

    $success = $this->recompenseModel->delete($id_reward);
    if ($success) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Failed to delete reward";
    }
    exit;
}
public function indexRewards() {
    try {
        $rewards = $this->recompenseModel->getAll();
        error_log("Fetched rewards: " . print_r($rewards, true)); // Debug
        return $rewards;
    } catch (Exception $e) {
        error_log("Error fetching rewards: " . $e->getMessage());
        return [];
    }
}
}