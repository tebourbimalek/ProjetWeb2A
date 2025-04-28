<?php
require_once 'config/database.php';
require_once 'controllers/JeuxController.php';

$db = Database::connect();
$controller = new JeuxController($db);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'add':
            $controller->add();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        case 'load_questions':
            $controller->loadQuestions($_GET['id_game']);
            break;
        case 'delete_question':
            $controller->deleteQuestion();
            break;
        case 'add_question':
            $controller->addQuestion($_POST);
            break;
        case 'update_question':
            $controller->updateQuestion();
            break;
        case 'get_question':
            $controller->getQuestion();
            break;
        default:
            echo "Invalid action";
            break;
    }
    exit;
}

$games = $controller->index();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Backoffice - Gamification</title>
    <link rel="stylesheet" href="/tunifiy(gamification)/backoffice/gamification/public/assets/css/backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="" alt="Gamification Logo">
        </div>
        <ul class="sidebar-menu">
            <li class="active" data-tab="dashboard"><i class="fas fa-home"></i> Dashboard</li>
            <li data-tab="jeux"><i class="fas fa-gamepad"></i> Jeux</li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1 id="header-title">Gamification Dashboard</h1>
            
            <button id="add-new-game-btn" class="btn btn-primary" style="display: none;">
                <i class="fas fa-plus"></i> Add New Game
            </button>
            <button id="add-new-question-btn" class="btn btn-primary" style="display: none;">
                <i class="fas fa-plus"></i> Add New Question
            </button>
            <button id="back-to-games-btn" class="btn btn-secondary" style="display: none;">
                <i class="fas fa-arrow-left"></i> Back
            </button>
        </div>


        <div id="dashboard" class="tab-content active">
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-title">TOTAL GAMES</div>
                    <div class="stat-value"><?= count($games) ?></div>
                </div>
            </div>
        </div>
        

        <div id="jeux" class="tab-content">
            <div class="content-section" id="games-section">
                <div class="section-header">
                    <div class="section-title">All Your Games</div>
                </div>
                <div class="search-container" style="margin: 20px 0;">
                 <input type="text" id="search-games" placeholder="Search games..." class="search-input">
                </div>

                <form id="add-game-form" action="backoffice.php?action=add" method="POST" enctype="multipart/form-data" style="display: none; margin-top: 20px;">
                    <input type="hidden" name="id_game" id="edit-id-game" />
                    <div class="form-group"><label>Game Name:</label><input type="text" name="nom_jeu" id="nom_jeu" class="form-control" required></div>
                    <div class="form-group">
                        <label>Type:</label>
                        <select name="type_jeu" id="type_jeu" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="guess">Guess</option>
                            <option value="quizz">Quizz</option>
                            <option value="puzzle">Puzzle</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Points:</label><input type="number" name="points_attribues" id="points_attribues" class="form-control" required></div>
                    <div class="form-group"><label>Status:</label>
                        <select name="statut" id="statut" class="form-control">
                            <option value="actif">Active</option>
                            <option value="inactif">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Cover Image:</label><input type="file" name="cover" accept="image/*" class="form-control"></div>
                    <button type="submit" class="btn btn-success">Save Game</button>
                </form>

                <table class="song-table" style="margin-top: 20px;" id="games-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cover</th>
                            <th>Game Name</th>
                            <th>Type</th>
                            <th>Points</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td><?= htmlspecialchars($game['id_game']) ?></td>
                                <td><img src="/tunifiy(gamification)/sources/uploads/<?= $game['cover_path'] ?? 'default.jpg' ?>" width="70" height="70" onerror="this.src='/tunifiy(gamification)/public/assets/default.jpg'"></td>
                                <td><?= htmlspecialchars($game['nom_jeu']) ?></td>
                                <td><?= htmlspecialchars($game['type_jeu']) ?></td>
                                <td><?= htmlspecialchars($game['points_attribues']) ?></td>
                                <td><?= htmlspecialchars($game['statut']) ?></td>
                                <td>
                                    <button class="btn btn-info show-questions"
                                            data-id="<?= (int)$game['id_game']; ?>"
                                            data-nom="<?= htmlspecialchars($game['nom_jeu']); ?>"
                                            data-type="<?= htmlspecialchars($game['type_jeu']); ?>">
                                        Questions
                                    </button>
                                    <button class="btn btn-danger delete-game" data-id="<?= (int)$game['id_game']; ?>"><i class="fas fa-trash"></i></button>
                                    <button type="button" class="btn btn-secondary edit-game"
                                            data-id="<?= (int)$game['id_game']; ?>"
                                            data-nom="<?= htmlspecialchars($game['nom_jeu']); ?>"
                                            data-type="<?= htmlspecialchars($game['type_jeu']); ?>"
                                            data-points="<?= (int)$game['points_attribues']; ?>"
                                            data-statut="<?= htmlspecialchars($game['statut']); ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="content-section" id="questions-section" style="display: none;">
                <div class="section-header">
                    <div class="section-title">Questions of <span id="current-game-name"></span> (<span id="current-game-type"></span>)</div>
                </div>
                
                <div id="question-form-container" style="display: none; margin-bottom: 20px;"></div>
                <div class="search-container" style="margin: 20px 0; display: none;" id="questions-search-container">
    <input type="text" id="search-questions" placeholder="Search questions..." class="search-input">
</div>

                <table class="song-table" style="margin-top: 20px;">
                    <thead id="questions-table-head">
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Details</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="questions-table-body">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="/tunifiy(gamification)/backoffice/gamification/public/assets/js/backoffice.js"></script>
</body>
</html>