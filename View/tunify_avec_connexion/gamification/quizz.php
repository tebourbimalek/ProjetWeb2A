<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\question.php';
session_start();

// Get the game ID from URL
$game_id = $_GET['id_game'] ?? die("Game ID not specified");

// Initialize game session with a unique key based on game_id
$session_key = 'game_' . $game_id;

// Initialize or reset game
if (!isset($_SESSION[$session_key])) {
    $db = config::getConnexion();
    $questionModel = new Question($db);
    
    // Get questions for this specific game with multiple choice options
    $query = "SELECT * FROM questions 
              WHERE id_game = :game_id 
              AND option_1 IS NOT NULL 
              AND option_2 IS NOT NULL
              AND option_3 IS NOT NULL
              AND option_4 IS NOT NULL
              AND correct_option IS NOT NULL
              ORDER BY RAND() LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':game_id', $game_id);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($questions) < 5) {
        die("Not enough valid quiz questions available for this game");
    }

    $_SESSION[$session_key] = [
        'questions' => $questions,
        'current_round' => 0,
        'score' => 0,
        'state' => 'countdown',
        'last_selected' => 0,
        'time_left' => 15 // 15 seconds per question
    ];
}

$game = &$_SESSION[$session_key];

// Get current question if not finished
if ($game['current_round'] >= count($game['questions'])) {
    $game['state'] = 'finished';
    $currentQuestion = null;
} else {
    $currentQuestion = $game['questions'][$game['current_round']];
}

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'start':
            $game['state'] = 'playing';
            $game['start_time'] = time();
            break;
            
        case 'answer':
            if (isset($_POST['answer'])) {
                $answer = (int)$_POST['answer'];
                $game['last_selected'] = $answer;
                
                if ($answer > 0 && $answer === (int)$currentQuestion['correct_option']) {
                    $game['score']++;
                }
                
                $game['state'] = 'feedback';
            }
            break;

        case 'next':
            $game['current_round']++;
            if ($game['current_round'] >= count($game['questions'])) {
                $game['state'] = 'finished';
            } else {
                $game['state'] = 'playing';
                $game['time_left'] = 15;
                $game['start_time'] = time();
            }
            break;

        case 'restart':
            unset($_SESSION[$session_key]);
            header("Location: quiz.php?id_game=$game_id");
            exit;
    }
    header("Location: quiz.php?id_game=$game_id");
    exit;
}

// Calculate time left if game is playing
if ($game['state'] === 'playing') {
    $elapsed = time() - $game['start_time'];
    $game['time_left'] = max(0, 15 - $elapsed);
    
    if ($game['time_left'] <= 0) {
        $game['state'] = 'feedback';
        $game['last_selected'] = 0; // Mark as unanswered
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quiz Game</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        header {
            background-color: #1f1f1f;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 32px;
            border-bottom: 4px solid #333;
            flex-direction: row-reverse;
        }

        .logo {
            height: 50px;
        }

        .header-buttons {
            display: flex;
            gap: 16px;
        }

        .header-buttons button {
            background-color: #9c27b0;
            border: none;
            color: white;
            padding: 10px 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        .game-container {
            width: 90%;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            text-align: center;
        }

        .countdown {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            border: 6px solid #9c27b0;
            border-radius: 50%;
            font-size: 4rem;
            font-weight: bold;
            line-height: 138px;
            color: #fff;
            text-align: center;
            box-shadow: 0 0 20px #9c27b0, 0 0 40px #9c27b0;
            background-color: #1e1e2e;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 10px #9c27b0; }
            50% { box-shadow: 0 0 25px #9c27b0; }
            100% { box-shadow: 0 0 10px #9c27b0; }
        }

        .timer {
            font-size: 1.5rem;
            color: #9c27b0;
            margin: 20px 0;
        }

        .options {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin: 20px 0;
        }

        .option {
            padding: 15px;
            background-color: #333;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
        }

        .option:hover {
            background-color: #444;
            transform: translateX(5px);
        }

        .option.correct {
            background-color: #2e7d32;
            border-left: 5px solid #66bb6a;
        }

        .option.wrong {
            background-color: #b71c1c;
            border-left: 5px solid #ef5350;
        }

        .question-text {
            font-size: 1.5rem;
            margin: 20px 0;
            padding: 15px;
            background-color: rgba(30, 30, 46, 0.7);
            border-radius: 10px;
        }

        .progress {
            margin: 20px 0;
            font-size: 1.2rem;
        }

        .feedback-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .start-screen {
            text-align: center;
            padding: 40px;
        }

        .start-screen h1 {
            font-size: 2.5rem;
            color: #9c27b0;
            margin-bottom: 30px;
        }

        .start-screen p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .start-button {
            padding: 15px 30px;
            background-color: #9c27b0;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.3rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .start-button:hover {
            background-color: #7b1fa2;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<header>
    <div class="header-buttons">
        <button onclick="window.location.href='frontoffice.php'">Back to Menu</button>
        
        
    </div>
  <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/logoprojet1.jpg" alt="Tunify Logo" class="logo">
</header>

<div class="game-container">
    <div class="progress">
        Score: <?= $game['score'] ?>/<?= count($game['questions']) ?> | 
        Question <?= $game['current_round'] + 1 ?>/<?= count($game['questions']) ?>
    </div>
    
    <?php if ($game['state'] === 'countdown'): ?>
        <div class="start-screen">
            <h1>Quiz Challenge</h1>
            <p>Test your knowledge with <?= count($game['questions']) ?> questions!</p>
            <p>You'll have 15 seconds to answer each question.</p>
            <button class="start-button" onclick="window.location.href='quiz.php?id_game=<?= $game_id ?>&action=start'">
                Start Quiz
            </button>
        </div>
    
    <?php elseif ($game['state'] === 'playing' && $currentQuestion): ?>
        <div class="timer">
            Time Left: <span id="time-display"><?= $game['time_left'] ?></span> seconds
        </div>
        
        <div class="question-text">
            <?= htmlspecialchars($currentQuestion['question_text']) ?>
        </div>
        
        <?php if (!empty($currentQuestion['image_path'])): ?>
            <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/<?= $currentQuestion['image_path'] ?>" 
                 class="feedback-image" alt="Question Image">
        <?php endif; ?>
        
        <div class="options">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <button class="option" onclick="submitAnswer(<?= $i ?>)">
                    <?= htmlspecialchars($currentQuestion['option_' . $i]) ?>
                </button>
            <?php endfor; ?>
        </div>
        
        <script>
            // Timer countdown
            let timeLeft = <?= $game['time_left'] ?>;
            const timerDisplay = document.getElementById('time-display');
            
            const timer = setInterval(() => {
                timeLeft--;
                timerDisplay.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    submitAnswer(0); // Auto-submit when time runs out
                }
            }, 1000);
            
            function submitAnswer(answer) {
                clearInterval(timer);
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'quiz.php?id_game=<?= $game_id ?>&action=answer';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'answer';
                input.value = answer;
                form.appendChild(input);
                
                document.body.appendChild(form);
                form.submit();
            }
        </script>
    
    <?php elseif ($game['state'] === 'feedback' && $currentQuestion): ?>
        <h1 style="color: <?= $game['last_selected'] == $currentQuestion['correct_option'] ? '#4CAF50' : '#F44336' ?>">
            <?= $game['last_selected'] == $currentQuestion['correct_option'] ? 'Correct!' : 'Incorrect!' ?>
        </h1>
        
        <div class="question-text">
            <?= htmlspecialchars($currentQuestion['question_text']) ?>
        </div>
        
        <?php if (!empty($currentQuestion['image_path'])): ?>
            <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/<?= $currentQuestion['image_path'] ?>" 
                 class="feedback-image" alt="Question Image">
        <?php endif; ?>
        
        <div class="options">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <?php
                    $classes = [];
                    if ($i == $currentQuestion['correct_option']) {
                        $classes[] = 'correct';
                    } elseif ($i == $game['last_selected']) {
                        $classes[] = 'wrong';
                    }
                ?>
                <div class="option <?= implode(' ', $classes) ?>">
                    <?= htmlspecialchars($currentQuestion['option_' . $i]) ?>
                </div>
            <?php endfor; ?>
        </div>
        
        <div style="margin-top: 20px;">
            Next question in <span id="next-timer">3</span> seconds...
        </div>
        
        <script>
            let countdown = 3;
            const nextTimer = document.getElementById('next-timer');
            const interval = setInterval(() => {
                countdown--;
                nextTimer.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = 'quiz.php?id_game=<?= $game_id ?>&action=next';
                }
            }, 1000);
        </script>
    
    <?php elseif ($game['state'] === 'finished'): ?>
        <h1>Quiz Completed!</h1>
        <div style="font-size: 2rem; margin: 20px 0;">
            Final Score: <?= $game['score'] ?>/<?= count($game['questions']) ?>
        </div>
        <div style="margin: 30px 0; font-size: 1.5rem;">
            <?= round(($game['score'] / count($game['questions'])) * 100) ?>% Correct
        </div>
        
        <button onclick="window.location.href='quiz.php?id_game=<?= $game_id ?>&action=restart'" 
                style="padding: 12px 24px; background: #9c27b0; color: white; border: none; border-radius: 6px; cursor: pointer;">
            Play Again
        </button>
        <button onclick="window.location.href='frontoffice.php'" 
                style="padding: 12px 24px; background: #333; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 10px;">
            Back to Menu
        </button>
    
    <?php else: ?>
        <p style="color: red;">Error: Invalid game state. <a href="quiz.php?id_game=<?= $game_id ?>&action=restart">Restart quiz</a></p>
    <?php endif; ?>
</div>
</body>
</html>