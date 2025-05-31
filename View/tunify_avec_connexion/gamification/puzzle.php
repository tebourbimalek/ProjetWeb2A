<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\question.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
session_start();

// Initialize or reset game
if (!isset($_SESSION['puzzle_game']) || isset($_GET['reset'])) {
    $db = config::getConnexion();
    $questionModel = new Question($db);
    $questions = $questionModel->getPuzzleQuestions(5);

    if (count($questions) < 5) {
        die("Not enough puzzle questions available");
    }

    $_SESSION['puzzle_game'] = [
        'questions' => $questions,
        'current_round' => 0,
        'score' => 0,
        'state' => 'not_started',
        'start_time' => 0,
        'time_limit' => 300, // 5 minutes in seconds
        'completed_rounds' => [] // Initialize as empty array
    ];
}

$game = &$_SESSION['puzzle_game'];

// Ensure all required keys exist
if (!isset($game['completed_rounds'])) {
    $game['completed_rounds'] = [];
}

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'start':
            $game['state'] = 'playing';
            $game['start_time'] = time();
            header('Location: puzzle.php');
            exit;

        case 'check':
            if (isset($_POST['is_correct'])) {
                $isCorrect = (bool)$_POST['is_correct'];
                $currentQuestion = $game['questions'][$game['current_round']];
                
                // Record completed round
                $game['completed_rounds'][] = [
                    'question' => $currentQuestion,
                    'is_correct' => $isCorrect,
                    'time' => time() - $game['start_time']
                ];
                
                if ($isCorrect) {
                    $game['score']++;
                }
                
                $game['current_round']++;
                if ($game['current_round'] >= count($game['questions'])) {
                    $game['state'] = 'finished';
                } else {
                    $game['start_time'] = time();
                }
            }
            header('Location: puzzle.php');
            exit;
            
        case 'restart':
            unset($_SESSION['puzzle_game']);
            header('Location: puzzle.php');
            exit;
    }
}

// Calculate time left if game is playing
$timeLeft = 0;
if ($game['state'] === 'playing') {
    $timeLeft = $game['time_limit'] - (time() - $game['start_time']);
    if ($timeLeft <= 0) {
        $game['state'] = 'finished';
    }
}

// Get current question if game is in progress
$currentQuestion = null;
if ($game['state'] === 'playing' && isset($game['questions'][$game['current_round']])) {
    $currentQuestion = $game['questions'][$game['current_round']];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Puzzle Game</title>
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
            padding: 12px 32px;
            border-bottom: 4px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            text-align: center;
        }

        .timer {
            font-size: 1.5rem;
            color: #9c27b0;
            margin: 20px 0;
        }

        .puzzle-game-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .puzzle-board {
            position: relative;
            width: 600px;
            height: 600px;
            margin: 20px auto;
            border: 2px solid #333;
        }

        .puzzle-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            width: 100%;
            height: 100%;
        }

        .puzzle-slot {
            border: 1px dashed #444;
            position: relative;
            transition: all 0.2s;
        }

        .puzzle-slot.slot-hover {
            background-color: rgba(156, 39, 176, 0.2);
        }

        .puzzle-pieces {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .puzzle-piece {
            position: absolute;
            width: 200px;
            height: 200px;
            border: 1px solid #444;
            cursor: move;
            transition: transform 0.2s, left 0.3s ease, top 0.3s ease;
            pointer-events: auto;
            z-index: 1;
        }

        .puzzle-piece:hover {
            transform: scale(1.02);
            z-index: 10;
            box-shadow: 0 0 10px rgba(156, 39, 176, 0.8);
        }

        .puzzle-piece.dragging {
            z-index: 100;
            opacity: 0.8;
        }

        .controls {
            margin: 20px 0;
        }

        button {
            background-color: #9c27b0;
            border: none;
            color: white;
            padding: 12px 24px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
        }

        button:hover {
            background-color: #ba68c8;
        }

        .progress {
            font-size: 1.2rem;
            margin: 20px 0;
        }

        .feedback-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .question-text {
            font-size: 1.8rem;
            margin: 20px 0;
            color: #9c27b0;
            text-align: center;
            animation: shine 2s infinite alternate;
            text-shadow: 0 0 10px rgba(156, 39, 176, 0.7);
            padding: 15px;
            background-color: rgba(30, 30, 46, 0.7);
            border-radius: 10px;
            width: 90%;
            margin-left: auto;
            margin-right: auto;
        }
        
        @keyframes shine {
            0% {
                text-shadow: 0 0 10px rgba(156, 39, 176, 0.7);
            }
            100% {
                text-shadow: 0 0 20px rgba(156, 39, 176, 1), 
                             0 0 30px rgba(156, 39, 176, 0.8);
            }
        }
        
        .audio-player {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            background-color: rgba(30, 30, 46, 0.7);
            padding: 15px;
            border-radius: 10px;
        }
        
        .audio-player h3 {
            color: #9c27b0;
            margin-bottom: 10px;
            text-align: center;
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
        
        .round-history {
            margin-top: 30px;
            text-align: left;
        }
        
        .round-item {
            background-color: rgba(30, 30, 46, 0.7);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .round-item.correct {
            border-left: 4px solid #4CAF50;
        }
        
        .round-item.incorrect {
            border-left: 4px solid #F44336;
        }
        
        .error-message {
            color: #F44336;
            padding: 20px;
            background-color: rgba(244, 67, 54, 0.1);
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<header>
    <div class="header-buttons">
        <button onclick="window.location.href='frontoffice.php'">Back to Menu</button>
        <button onclick="window.location.href='puzzle.php?action=restart'">Restart Game</button>
    </div>
  <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/logoprojet1.jpg" alt="Tunify Logo" class="logo">
</header>

<div class="game-container">
    <?php if ($game['state'] === 'not_started'): ?>
        <div class="start-screen">
            <h1>Puzzle Challenge</h1>
            <p>Complete 5 puzzle rounds within 5 minutes. Each correct solution gives you 1 point.</p>
            <button onclick="window.location.href='puzzle.php?action=start'">Start Game</button>
        </div>
        
    <?php elseif ($game['state'] === 'playing' && $timeLeft > 0): ?>
        <?php if ($currentQuestion): ?>
            <div class="progress">
                Round <?= $game['current_round'] + 1 ?> of 5 | 
                Score: <?= $game['score'] ?>
            </div>
            
            <div class="timer" id="timer">
                Time Left: <span id="time-display"><?= gmdate("i:s", $timeLeft) ?></span>
            </div>
            
            <div class="question-text">
                <?= htmlspecialchars($currentQuestion['question_text'] ?? 'Solve the puzzle!') ?>
            </div>
            
            <?php if (!empty($currentQuestion['mp3_path'])): ?>
                <div class="audio-player">
                    <h3>Listen to the clue:</h3>
                    <audio autoplay>
                        <source src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/audio/<?= $currentQuestion['mp3_path'] ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($currentQuestion['image_path'])): ?>
                <div class="puzzle-game-container">
                    <div class="puzzle-board" id="puzzle-board">
                        <div class="puzzle-grid">
                            <?php for ($row = 0; $row < 3; $row++): ?>
                                <?php for ($col = 0; $col < 3; $col++): ?>
                                    <div class="puzzle-slot" 
                                         data-row="<?= $row ?>" 
                                         data-col="<?= $col ?>"
                                         style="background-position: -<?= $col * 200 ?>px -<?= $row * 200 ?>px;">
                                    </div>
                                <?php endfor; ?>
                            <?php endfor; ?>
                        </div>
                        
                        <div class="puzzle-pieces" id="puzzle-pieces"></div>
                    </div>
                </div>
                
                <div class="controls">
                    <button onclick="checkSolution()">Check Solution</button>
                    <button onclick="shufflePieces()">Shuffle</button>
                </div>
                
                <script>
                    // Initialize puzzle pieces
                    const container = document.getElementById('puzzle-board');
                    const piecesContainer = document.getElementById('puzzle-pieces');
                    const slots = document.querySelectorAll('.puzzle-slot');
                    const pieceSize = 200;
                    let pieces = [];
                    
                    // Create puzzle pieces
                    function createPieces() {
                        piecesContainer.innerHTML = '';
                        pieces = [];
                        
                        for (let row = 0; row < 3; row++) {
                            for (let col = 0; col < 3; col++) {
                                const piece = document.createElement('div');
                                piece.className = 'puzzle-piece';
                                piece.style.backgroundImage = `url('/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/<?= $currentQuestion['image_path'] ?>')`;
                                piece.style.backgroundSize = '600px 600px';
                                piece.style.backgroundPosition = `-${col * pieceSize}px -${row * pieceSize}px`;
                                piece.dataset.correctRow = row;
                                piece.dataset.correctCol = col;
                                piece.dataset.currentRow = row;
                                piece.dataset.currentCol = col;
                                
                                // Position randomly initially
                                piece.style.left = `${50 + Math.random() * 300}px`;
                                piece.style.top = `${50 + Math.random() * 300}px`;
                                
                                // Make draggable
                                piece.draggable = true;
                                piece.addEventListener('dragstart', dragStart);
                                piece.addEventListener('dragend', dragEnd);
                                
                                piecesContainer.appendChild(piece);
                                pieces.push(piece);
                            }
                        }
                    }
                    
                    // Make slots accept drops
                    slots.forEach(slot => {
                        slot.addEventListener('dragover', dragOver);
                        slot.addEventListener('drop', drop);
                        slot.addEventListener('dragenter', dragEnter);
                        slot.addEventListener('dragleave', dragLeave);
                    });
                    
                    // Drag and drop variables
                    let draggedPiece = null;
                    let draggedPieceOriginalPosition = null;
                    let draggedPieceOriginalSlot = null;
                    
                    function dragStart(e) {
                        draggedPiece = e.target;
                        draggedPiece.classList.add('dragging');
                        draggedPieceOriginalPosition = {
                            left: draggedPiece.style.left,
                            top: draggedPiece.style.top
                        };
                        
                        // Find which slot this piece was in
                        const pieceRect = draggedPiece.getBoundingClientRect();
                        slots.forEach(slot => {
                            const slotRect = slot.getBoundingClientRect();
                            if (isOverlapping(pieceRect, slotRect)) {
                                draggedPieceOriginalSlot = slot;
                            }
                        });
                        
                        e.dataTransfer.setData('text/plain', e.target.id);
                        setTimeout(() => e.target.style.opacity = '0.5', 0);
                    }
                    
                    function dragEnd() {
                        if (draggedPiece) {
                            draggedPiece.classList.remove('dragging');
                            draggedPiece.style.opacity = '1';
                        }
                    }
                    
                    function dragOver(e) {
                        e.preventDefault();
                    }
                    
                    function dragEnter(e) {
                        e.preventDefault();
                        this.classList.add('slot-hover');
                    }
                    
                    function dragLeave() {
                        this.classList.remove('slot-hover');
                    }
                    
                    function drop(e) {
                        e.preventDefault();
                        this.classList.remove('slot-hover');
                        
                        if (draggedPiece) {
                            // If there's already a piece in this slot, swap them
                            const existingPiece = findPieceInSlot(this);
                            
                            if (existingPiece && existingPiece !== draggedPiece) {
                                // Swap positions
                                if (draggedPieceOriginalSlot) {
                                    existingPiece.style.left = draggedPieceOriginalPosition.left;
                                    existingPiece.style.top = draggedPieceOriginalPosition.top;
                                    existingPiece.dataset.currentRow = draggedPieceOriginalSlot.dataset.row;
                                    existingPiece.dataset.currentCol = draggedPieceOriginalSlot.dataset.col;
                                }
                            }
                            
                            // Snap to the slot position
                            const slotRect = this.getBoundingClientRect();
                            const containerRect = container.getBoundingClientRect();
                            
                            draggedPiece.style.left = `${slotRect.left - containerRect.left}px`;
                            draggedPiece.style.top = `${slotRect.top - containerRect.top}px`;
                            draggedPiece.dataset.currentRow = this.dataset.row;
                            draggedPiece.dataset.currentCol = this.dataset.col;
                        }
                    }
                    
                    function findPieceInSlot(slot) {
                        const slotRect = slot.getBoundingClientRect();
                        for (const piece of pieces) {
                            const pieceRect = piece.getBoundingClientRect();
                            if (isOverlapping(pieceRect, slotRect)) {
                                return piece;
                            }
                        }
                        return null;
                    }
                    
                    function isOverlapping(rect1, rect2) {
                        return !(
                            rect1.right < rect2.left || 
                            rect1.left > rect2.right || 
                            rect1.bottom < rect2.top || 
                            rect1.top > rect2.bottom
                        );
                    }
                    
                    // Shuffle function
                    function shufflePieces() {
                        pieces.forEach(piece => {
                            piece.style.left = `${50 + Math.random() * 300}px`;
                            piece.style.top = `${50 + Math.random() * 300}px`;
                            delete piece.dataset.currentRow;
                            delete piece.dataset.currentCol;
                        });
                    }
                    
                    // Check solution
                    // Check solution
function checkSolution() {
    let correctCount = 0;
    const tolerance = 5; // Pixel tolerance for position matching
    
    pieces.forEach(piece => {
        const pieceRect = piece.getBoundingClientRect();
        const correctRow = parseInt(piece.dataset.correctRow);
        const correctCol = parseInt(piece.dataset.correctCol);
        
        // Calculate where this piece should be
        const slot = document.querySelector(`.puzzle-slot[data-row="${correctRow}"][data-col="${correctCol}"]`);
        const slotRect = slot.getBoundingClientRect();
        
        // Check if piece is in correct position (with some tolerance)
        const xMatch = Math.abs(pieceRect.left - slotRect.left) <= tolerance;
        const yMatch = Math.abs(pieceRect.top - slotRect.top) <= tolerance;
        
        if (xMatch && yMatch) {
            correctCount++;
        }
    });
    
    const isCorrect = correctCount === 9;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'puzzle.php?action=check';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'is_correct';
    input.value = isCorrect ? '1' : '0';
    form.appendChild(input);
    
    document.body.appendChild(form);
    form.submit();
}
                    
                    // Timer countdown
                    let timeLeft = <?= $timeLeft ?>;
                    const timerDisplay = document.getElementById('time-display');
                    
                    const timer = setInterval(() => {
                        timeLeft--;
                        const minutes = Math.floor(timeLeft / 60);
                        const seconds = timeLeft % 60;
                        timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                        
                        if (timeLeft <= 0) {
                            clearInterval(timer);
                            checkSolution();
                        }
                    }, 1000);
                    
                    // Initialize the puzzle pieces
                    createPieces();
                </script>
            <?php else: ?>
                <div class="error-message">
                    <p>Error: Puzzle image not found</p>
                    <button onclick="window.location.href='puzzle.php?restart=1'">Restart Game</button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="error-message">
                <p>Error: Current question not available</p>
                <button onclick="window.location.href='puzzle.php?restart=1'">Restart Game</button>
            </div>
        <?php endif; ?>
        
    <?php elseif ($game['state'] === 'finished'):
        $pdo = config::getConnexion();
        $userConnected = getUserInfo($pdo);
        $newScore = $userConnected->getScore() + $game['score'];
    
        // Mettre à jour dans la base de données
        $stmt = $pdo->prepare("UPDATE utilisateurs SET score = ? WHERE artiste_id = ?");
        $stmt->execute([$newScore, $userConnected->getArtisteId()]); ?>
        <h1>Game Over!</h1>
        <div style="font-size: 2rem; margin: 20px 0;">
            Your final score: <?= $game['score'] ?> out of 5
        </div>
        
        <div class="round-history">
            <h2>Round History</h2>
            <?php if (!empty($game['completed_rounds'])): ?>
                <?php foreach ($game['completed_rounds'] as $index => $round): ?>
                    <div class="round-item <?= $round['is_correct'] ? 'correct' : 'incorrect' ?>">
                        <h3>Round <?= $index + 1 ?>: <?= $round['is_correct'] ? '✓ Correct' : '✗ Incorrect' ?></h3>
                        <p><strong>Question:</strong> <?= htmlspecialchars($round['question']['question_text']) ?></p>
                        <p><strong>Time taken:</strong> <?= gmdate("i:s", $round['time']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No rounds completed.</p>
            <?php endif; ?>
        </div>

        <div style="margin: 30px 0;">
        <button onclick="window.location.href='puzzle.php?action=restart'">Play Again</button>
            <button onclick="window.location.href='frontoffice.php'">Back to Menu</button>
        </div>
        <?php $game['score'] = 0; // Reset score for next game ?>
        <script>
    // [Previous JavaScript code remains the same]
    
    // Enhanced Play Again functionality
    function restartGame() {
        // Clear any existing game data
        if (typeof localStorage !== 'undefined') {
            localStorage.removeItem('puzzleGameState');
        }
        
        // Redirect to restart the game
        window.location.href = 'puzzle.php?action=restart';
    }
</script>
        
    <?php endif; ?>
</div>
</body>
</html>