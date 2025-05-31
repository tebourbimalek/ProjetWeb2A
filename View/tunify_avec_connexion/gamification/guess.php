<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\question.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

session_start();

// Get game ID from URL
$game_id = $_GET['id_game'] ?? die("Game ID not specified");

// Initialize game session with dynamic key
$session_key = 'game_' . $game_id;

// Initialize or reset game
if (!isset($_SESSION[$session_key])) {
    $db = config::getConnexion();
    $questionModel = new Question($db);
    
    // Get questions for this specific game only
    $query = "SELECT * FROM questions 
              WHERE id_game = :game_id
              AND question_text IS NOT NULL 
              AND question_text != '' 
              AND correct_answer IS NOT NULL 
              AND correct_answer != ''
              ORDER BY RAND() LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':game_id', $game_id);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

$game = &$_SESSION[$session_key];

// Get current question if not finished
if ($game['current_round'] >= 5) {
    $game['state'] = 'finished';
    $currentQuestion = null;
} elseif ($game['state'] !== 'start') {
    $currentQuestion = $game['questions'][$game['current_round']];
}

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'start':
            $game['state'] = 'countdown';
            break;
            
        case 'check_answer':
            if (isset($_POST['user_answer'])) {
                $game['user_answer'] = trim($_POST['user_answer']);
                $correct_answer = trim(strtolower($currentQuestion['correct_answer']));
                $user_answer = trim(strtolower($game['user_answer']));
                
                if ($user_answer === $correct_answer) {
                    $game['score']++;
                }
                $game['state'] = 'feedback';
            }
            break;

        case 'next':
            $game['current_round']++;
            if ($game['current_round'] >= 5) {
                $game['state'] = 'finished';
            } else {
                $game['state'] = 'countdown';
                $game['user_answer'] = '';
                $game['time_left'] = 30;
            }
            break;

        case 'restart':
            unset($_SESSION[$session_key]);
            header("Location: guess.php?id_game={$game['game_id']}");
            exit;
            
        case 'play':
            $game['state'] = 'playing';
            break;
    }
    header("Location: guess.php?id_game={$game['game_id']}");
    exit;
}

// Fetch game name from database
$db = config::getConnexion();
$stmt = $db->prepare("SELECT nom_jeu FROM jeux WHERE id_game = :game_id");
$stmt->bindParam(':game_id', $game_id);
$stmt->execute();
$game_info = $stmt->fetch(PDO::FETCH_ASSOC);
$game_name = $game_info['nom_jeu'] ?? 'Guess Game';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($game_name) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/projetweb/View/tunify_avec_connexion/gamification/public/assets/css/guess.css">
    
</head>
<body>

<header>
    <div class="header-buttons">
        <button onclick="window.location.href='frontoffice.php'">Back to Menu</button>
    </div>
  <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/logoprojet1.jpg" alt="Tunify Logo" class="logo">
</header>

<div class="game-container">
    <?php if ($game['state'] === 'start'): ?>
        <h1 class="game-title"><?= htmlspecialchars($game_name) ?></h1>
        <p>Test your knowledge with 5 challenging questions!</p>
        <p>You'll have 30 seconds to answer each question.</p>
        <button class="start-button" 
                onclick="window.location.href='guess.php?id_game=<?= $game_id ?>&action=start'">
            Start Game
        </button>
        
    <?php elseif ($game['state'] === 'countdown'): ?>
        <div class="progress">
            Score: <?= $game['score'] ?>/5 | Round <?= $game['current_round'] + 1 ?>/5
        </div>
        
        <h1>Get Ready!</h1>
        <div class="countdown" id="countdown">5</div>
        <script>
            let counter = 3;
            const countdown = setInterval(() => {
                counter--;
                document.getElementById('countdown').textContent = counter;
                if (counter === 0) {
                    clearInterval(countdown);
                    window.location.href = 'guess.php?id_game=<?= $game_id ?>&action=play';
                }
            }, 1000);
        </script>

    <?php elseif ($game['state'] === 'playing' && $currentQuestion): ?>
        <div class="progress">
            Score: <?= $game['score'] ?>/5 | Round <?= $game['current_round'] + 1 ?>/5
        </div>
        
        <h1>Question <?= $game['current_round'] + 1 ?></h1>
        <div class="question-text"><?= $currentQuestion['question_text'] ?></div>
        
        <div class="answer-timer" id="answer-timer">30</div>
        
        <form method="POST" action="guess.php?id_game=<?= $game_id ?>&action=check_answer">
            <input type="text" class="answer-input" name="user_answer" placeholder="Your answer..." autocomplete="off" autofocus>
            <button type="submit" class="check-button">Check Answer</button>
        </form>
        
        <script>
            let answerTime = 30;
            const answerTimer = document.getElementById('answer-timer');
            const answerInterval = setInterval(() => {
                answerTime--;
                answerTimer.textContent = answerTime;
                if (answerTime === 0) {
                    clearInterval(answerInterval);
                    document.querySelector('form').submit();
                }
            }, 1000);
        </script>

    <?php elseif ($game['state'] === 'feedback' && $currentQuestion): ?>
        <div class="progress">
            Score: <?= $game['score'] ?>/5 | Round <?= $game['current_round'] + 1 ?>/5
        </div>
        
        <?php
            $correct_answer = trim(strtolower($currentQuestion['correct_answer']));
            $user_answer = trim(strtolower($game['user_answer']));
            $is_correct = $user_answer === $correct_answer;
        ?>
        
        <h1 style="color: <?= $is_correct ? '#4caf50' : '#f44336' ?>">
            <?= $is_correct ? 'Correct!' : 'Incorrect!' ?>
        </h1>
        
        <div class="question-text"><?= $currentQuestion['question_text'] ?></div>
        
        <?php if (!$is_correct): ?>
            <div class="user-answer">Your answer: <?= htmlspecialchars($game['user_answer']) ?></div>
            <div class="correct-answer">Correct answer: <?= htmlspecialchars($currentQuestion['correct_answer']) ?></div>
        <?php endif; ?>
        
        <?php if ($currentQuestion['image_path']): ?>
            <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/<?= $currentQuestion['image_path'] ?>" 
                 class="feedback-image" alt="Question Image">
        <?php endif; ?>
        
        <?php if ($currentQuestion['mp3_path']): ?>
            <audio autoplay src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/audio/<?= $currentQuestion['mp3_path'] ?>"></audio>
        <?php endif; ?>
        
        <div style="margin-top: 20px;">
            Next round in <span id="nextTimer">10</span> seconds...
        </div>
        
        <script>
            let t = 18;
            const nextTimer = document.getElementById('nextTimer');
            const interval = setInterval(() => {
                t--;
                nextTimer.textContent = t;
                if (t === 0) {
                    clearInterval(interval);
                    window.location.href = 'guess.php?id_game=<?= $game_id ?>&action=next';
                }
            }, 1000);
        </script>

    <?php elseif ($game['state'] === 'finished'):
        $pdo = config::getConnexion();
        $userConnected = getUserInfo($pdo);
        $newScore = $userConnected->getScore() + $game['score'];
    
        // Mettre à jour dans la base de données
        $stmt = $pdo->prepare("UPDATE utilisateurs SET score = ? WHERE artiste_id = ?");
        $stmt->execute([$newScore, $userConnected->getArtisteId()]); ?>
        <h1>Game Completed!</h1>
        <div style="font-size: 2rem; margin: 20px 0;">
            You scored <?= $game['score'] ?> out of 5
        </div>
        <div style="margin: 30px 0;">
            <?= round(($game['score'] / 5) * 100) ?>% correct answers
        </div>
        <button onclick="window.location.href='guess.php?id_game=<?= $game_id ?>&action=restart'" 
                style="padding: 12px 24px; background: #9c27b0; color: white; border: none; border-radius: 6px; cursor: pointer;">
            Play Again
        </button>
        <button onclick="window.location.href='frontoffice.php'" 
                style="padding: 12px 24px; background: #333; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 10px;">
            Back to Menu
        </button>
        <?php $game['score'] = 0; // Reset score for next game ?>

    <?php else: ?>
        <p style="color: red;">
            <a href="guess.php?id_game=<?= $game_id ?>&action=restart">Restart</a> the game.
        </p>
    <?php endif; ?>
</div>
</body>
</html>