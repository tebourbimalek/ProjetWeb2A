<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/question.php';
session_start();

// Initialize game
if (!isset($_SESSION['heardle_game'])) {
    $db = Database::connect();
    $questionModel = new Question($db);
    
    // Get only valid audio questions with all required fields
    $query = "SELECT * FROM questions 
              WHERE mp3_path IS NOT NULL 
              AND mp3_path != '' 
              AND option_1 IS NOT NULL AND option_1 != ''
              AND option_2 IS NOT NULL AND option_2 != ''
              AND option_3 IS NOT NULL AND option_3 != ''
              AND option_4 IS NOT NULL AND option_4 != ''
              AND correct_option IS NOT NULL
              ORDER BY RAND() LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($questions) < 10) {
        die("Not enough valid audio questions available");
    }

    $_SESSION['heardle_game'] = [
        'questions' => $questions,
        'current_round' => 0,
        'score' => 0,
        'state' => 'countdown',
        'last_selected' => 0,
        'audio_playing' => false
    ];
}

$game = &$_SESSION['heardle_game'];

// Prevent undefined currentQuestion
if ($game['current_round'] >= 10) {
    $game['state'] = 'finished';
    $currentQuestion = null;
} else {
    $currentQuestion = $game['questions'][$game['current_round']];
}

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'answer':
            if (isset($_POST['answer'])) {
                $answer = (int)$_POST['answer'];
                $game['last_selected'] = $answer;
                if ($answer > 0 && $answer === (int)$currentQuestion['correct_option']) {
                    $game['score']++;
                }
                $game['state'] = 'feedback';
                $game['audio_playing'] = true;
            }
            break;

        case 'next':
            $game['current_round']++;
            if ($game['current_round'] >= 10) {
                $game['state'] = 'finished';
            } else {
                $game['state'] = 'countdown';
                $game['audio_playing'] = false;
            }
            break;

        case 'restart':
            unset($_SESSION['heardle_game']);
            header('Location: heardle.php');
            exit;
            
        case 'play':
            $game['state'] = 'playing';
            $game['audio_playing'] = true;
            break;
    }
    header('Location: heardle.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Heardle Game</title>
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

        /* Countdown Timer */
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

        /* Answer Timer */
        .answer-timer {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 20px auto;
            border: 4px solid #9c27b0;
            border-radius: 50%;
            font-size: 2rem;
            font-weight: bold;
            line-height: 72px;
            color: #fff;
            text-align: center;
            box-shadow: 0 0 10px #9c27b0;
            background-color: #1e1e2e;
        }

        .options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 30px 0;
        }

        .option {
            padding: 15px;
            background-color: #333;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .option:hover {
            background-color: #444;
        }

        .option.correct {
            background-color: #2e7d32;
            border: 2px solid #66bb6a;
        }

        .option.wrong {
            background-color: #b71c1c;
            border: 2px solid #ef5350;
        }

        .question-text {
            font-size: 1.5rem;
            margin: 20px 0;
        }

        .progress {
            margin-top: 20px;
            font-size: 1.2rem;
        }

        .tunify-logo {
            margin: 20px auto;
            display: block;
            width: 150px;
        }

        .feedback-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 12px;
            margin: 20px 0;
            object-fit: contain;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        .tunify-badge {
            width: 200px;        /* or any size you want */
            height: auto;
            display: block;
            margin: 0 auto;      /* center it horizontally */
        }
    </style>
</head>
<body>

<header>
  <div class="header-buttons">
    <button>Se connecter</button>
    <button>Premium</button>
  </div>
  <img src="/tunifiy(gamification)/sources/uploads/logoprojet1.jpg" alt="Tunify Logo" class="logo">
</header>

<div class="game-container">
    <div class="progress">
        Score: <?= $game['score'] ?>/10 | Round <?= $game['current_round'] + 1 ?>/10
    </div>
    
<?php if ($game['state'] === 'countdown'): ?>
    <h1>Get Ready!</h1>
    <div class="countdown" id="countdown">5</div>
    <script>
        let counter = 5;
        const countdown = setInterval(() => {
            counter--;
            document.getElementById('countdown').textContent = counter;
            if (counter === 0) {
                clearInterval(countdown);
                window.location.href = 'heardle.php?action=play';
            }
        }, 1000);
    </script>

<?php elseif ($game['state'] === 'playing' && $currentQuestion): ?>
    <h1>Guess the Song!</h1>
    <div class="question-text"><?= $currentQuestion['question_text'] ?></div>
    <audio id="audio-player" src="/tunifiy(gamification)/sources/uploads/audio/<?= $currentQuestion['mp3_path'] ?>" autoplay loop></audio>
    
    <div class="answer-timer" id="answer-timer">13</div>
    
    <div class="options">
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <button class="option" onclick="submitAnswer(<?= $i ?>)">
                <?= $currentQuestion['option_' . $i] ?>
            </button>
        <?php endfor; ?>
    </div>
    <script>
        // Answer timer countdown
        let answerTime = 13;
        const answerTimer = document.getElementById('answer-timer');
        const answerInterval = setInterval(() => {
            answerTime--;
            answerTimer.textContent = answerTime;
            if (answerTime === 0) {
                clearInterval(answerInterval);
                submitAnswer(0); // Auto-submit after 13 seconds
            }
        }, 1000);

        function submitAnswer(answer) {
            clearInterval(answerInterval); // Stop the timer
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'heardle.php?action=answer';
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
    <h1 style="color: <?= $game['last_selected'] == $currentQuestion['correct_option'] ? '#4caf50' : '#f44336' ?>">
        <?= $game['last_selected'] == $currentQuestion['correct_option'] ? 'Correct!' : 'Try again!' ?>
    </h1>
    <div class="question-text"><?= $currentQuestion['question_text'] ?></div>
    <?php if ($currentQuestion['image_path']): ?>
        <img src="<?= '/tunifiy(gamification)/sources/uploads/' . urlencode($currentQuestion['image_path']) ?>" class="feedback-image" alt="Song Image">

    <?php endif; ?>
    <audio autoplay loop src="/tunifiy(gamification)/sources/uploads/audio/<?= $currentQuestion['mp3_path'] ?>"></audio>
    <div class="options">
        <?php for ($i = 1; $i <= 4; $i++):
            $class = '';
            if ($i == $currentQuestion['correct_option']) {
                $class = 'correct';
            } elseif ($i == $game['last_selected']) {
                $class = 'wrong';
            }
        ?>
            <div class="option <?= $class ?>">
                <?= $currentQuestion['option_' . $i] ?>
            </div>
        <?php endfor; ?>
    </div>
    <img src="/tunifiy(gamification)/sources/uploads/ppp.png" alt="Tunify Badge" class="tunify-badge">



    <div style="margin-top: 20px;">
        The next round will start in <span id="nextTimer">3</span> seconds.
    </div>
    <script>
        let t = 3;
        const nextTimer = document.getElementById('nextTimer');
        const interval = setInterval(() => {
            t--;
            nextTimer.textContent = t;
            if (t === 0) {
                clearInterval(interval);
                window.location.href = 'heardle.php?action=next';
            }
        }, 1000);
    </script>

<?php elseif ($game['state'] === 'finished'): ?>
    <h1>Game Over!</h1>
    <div style="font-size: 2rem; margin: 20px 0;">
        You scored <?= $game['score'] ?> out of 10
    </div>
    <div style="margin: 30px 0;">
        <?= round(($game['score'] / 10) * 100) ?>% correct answers
    </div>
    <button onclick="window.location.href='heardle.php?action=restart'" 
            style="padding: 12px 24px; background: #9c27b0; color: white; border: none; border-radius: 6px; cursor: pointer;">
        Play Again
    </button>
    <button onclick="window.location.href='frontoffice.php'" 
            style="padding: 12px 24px; background: #333; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 10px;">
        Back to Menu
    </button>

<?php else: ?>
    <p style="color: red;">Unexpected game state. <a href="heardle.php?action=restart">Restart</a> the game.</p>
<?php endif; ?>
</div>
</body>
</html>