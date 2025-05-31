<?php
session_start();  // Start the session to retrieve data


include_once 'C:\xampp\htdocs\projetweb\controlleur\function.php';  // Include the function file   
include_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
include_once 'C:\xampp\htdocs\projetweb\controlleur\functionpaiments.php';
include_once 'C:\xampp\htdocs\projetweb\controlleur\functionsnews.php';
include_once 'C:\xampp\htdocs\projetweb\controlleur\JeuxController.php';
include_once 'C:\xampp\htdocs\projetweb\model\recompense.php';
include_once 'C:\xampp\htdocs\projetweb\controlleur\TypeReclamationController.php';
include_once 'C:\xampp\htdocs\projetweb\controlleur\ReclamationController.php';
require_once 'C:\xampp\htdocs\projetweb\model\Reclamation.php';




$pdo = config::getConnexion();
// Usage of the function
$user = getUserInfo($pdo);

$fullPath = $user->getImagePath(); // e.g., C:\xampp\htdocs\projetweb\assets\includes\ALA.jpeg

// Remove the root path prefix
$relativePath = str_replace('C:\\xampp\\htdocs', '', $fullPath);

// Replace backslashes with slashes for web compatibility
$relativePath = str_replace('\\', '/', $relativePath);





$user_id = $user->getArtisteId();
$unreadCount = countUnreadNotifications($user_id);
if (isSubscriptionExpired($pdo, $user_id)){
    $type= 'expired';
}else{
    $type= 'valid';
}
$counter = 1;  // Initialize a counter for the song number
$counter1 = 1; // Initialize a counter for the song number in the top songs section
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';  // Retrieve any error message
unset($_SESSION['message']); // Clear the message after it has been set

$songs = affichage($user_id);  // Retrieve all songs
$songstrier= affichagetrier($user_id);  // Retrieve songs sorted by streams



// Check if there's a song before trying to access its ID
$song_id = isset($songs[0]['id']) ? $songs[0]['id'] : null;

if ($song_id) {
    $songsid = affichageid($song_id);  // Fetch song by ID
} else {
    $songsid = null;  // No song found
}

$statistics = getStreamingStatistics();

// Convert to JSON only if valid
if ($statistics) {
    $statisticsJson = json_encode($statistics);
} else {
    $statisticsJson = null;
}

$stats = getArtistStats();
$labels = [];
$data = [];

foreach ($stats as $row) {
    $labels[] = $row['nom_utilisateur'];
    $data[] = $row['total_streams'];
}

// Convertir en JSON pour JavaScript
$labelsJson = json_encode($labels);
$dataJson = json_encode($data);


$paiements = affichagePaiement();
$paiementsCarts = affichagePaiementscart();
$paiementsMobile = affichagePaiementsMobile();

if (!isset($_SESSION['user'])) {
    header("Location: /projetweb/View/tunisfy_sans_conexion/login.php");
    exit; // Bonne pratique ici
}

try {
    $pdo = config::getConnexion();
    


    $userConnected = getUserInfo($pdo);
    $user_role = $userConnected->getTypeUtilisateur();
    $user_name= $userConnected->getNomUtilisateur();
    // Récupération des utilisateurs
    $users = getAllUsers($pdo);
    
} catch (PDOException $e) {
    // Gestion d'erreurs propre
    die("Erreur de base de données : " . $e->getMessage());
} catch (Exception $e) {
    // Pour les erreurs de checkIfAdmin
    die($e->getMessage());
}

$news = localGetNews();
$user_role = $userConnected->getTypeUtilisateur();

$controller = new JeuxController($pdo);


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
        case 'add_reward':
            $recompensesController->add();
            break;
        case 'update_reward':
            $recompensesController->update();
            break;
        case 'delete_reward':
            $recompensesController->delete();
            break;
        default:
            echo "Invalid action";
            break;
    }
    exit;
}

$games = $controller->index();
$rewards = $controller->indexRewards();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Artist Dashboard</title>
    <link rel="stylesheet" href="backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        canvas{
            width: 100% !important;
            height: 100% !important;

        }
        .tabs {
        margin-bottom: 15px;
    }

    .tab-link {
        padding: 10px 20px;
        margin-right: 5px;
        cursor: pointer;
        background-color: #eee;
        border: none;
        border-radius: 5px;
    }

    .tab-link.active {
        background-color:rgb(119, 0, 255);
        color: white;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }
    /* Style des champs de recherche */
    .search-input {
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        width: 250px;
        margin-left: 20px;
        outline: none;
        transition: all 0.3s;
    }

    .search-input:focus {
        border-color: #1DB954;
        box-shadow: 0 0 5px rgba(29, 185, 84, 0.3);
    }

    /* Style pour les tables */
    table {
        width: 100%;
        margin-top: 15px;
    }

    /* Facultatif : Style pour les lignes filtrées */
    tr[style*="display: none"] {
        opacity: 0.5;
        transition: opacity 0.3s;
    }
    .paiment-stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card-paiment {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: white;
        text-align: center;
    }

    .stat-card-paiment:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .stat-title-paiment {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 10px;
        color: rgba(255, 255, 255, 0.8);
    }

    .stat-value-paiment {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-change {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
    }

    /* Couleurs spécifiques pour chaque type */
    .stat-card-paiment.carte {
        background: linear-gradient(135deg, #4B0082 0%, #4B0082 100%);
    }

    .stat-card-paiment.mobile {
        background: linear-gradient(135deg, #4B0082 0%, #4B0082 100%);
    }

    .stat-card-paiment.total {
        background: linear-gradient(135deg, #4B0082 0%, #4B0082 100%);
    }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="" alt="Spotify Logo">
        </div>
        <ul class="sidebar-menu">
            
            <li class="active" data-tab="dashboard"><i class="fas fa-home"></i> Dashboard</li>
            
            <?php if ( $user_role == 'artiste') { ?>
                <!-- 'My Music' is accessible to Admin and Artiste -->
                <li data-tab="music"><i class="fas fa-music"></i> My Music</li>
                <li data-tab="settings"><i class="fas fa-cog"></i> Settings</li>
            <?php } ?>

            <?php if ($user_role == 'admin') { ?>
                <!-- 'Statistics' is accessible only to Admin -->
                <!-- 'Users' is accessible only to Admin -->
                <li data-tab="users"><i class="fas fa-users"></i> Users</li>
                <li data-tab="music"><i class="fas fa-music"></i> My Music</li>
                <li data-tab="settings"><i class="fas fa-cog"></i> Settings</li>
                <li data-tab="recompenses"><i class="fas fa-gift"></i> Récompenses</li>
                <li data-tab="paiment"><i class="fa-solid fa-cart-shopping"></i>paiment</li>
                <li data-tab="news"><i class="fa-solid fa-newspaper"></i>News</li>
                <li data-tab="jeux"><i class="fa-solid fa-gamepad"></i> jeux</li>
            <?php } ?>
            <li>
                <a href="../tunify_avec_connexion/avec_connexion.php" style="text-decoration: none; color: inherit;">
                    <i class="fa-solid fa-headphones"></i> Accueil
                </a>
            </li>
             <li style="color:red;">
                <a href="../tunify_avec_connexion/logout.php" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1 id="header-title"></h1>
            <div class="artist-info">
                <div class="artist-avatar">

                    <img src="<?php echo $relativePath ?>" 
                        style="width: 100%; max-width: 300px; height: auto; border-radius: 50%; margin-bottom: 10px;" 
                        alt="Profile Picture of <?php echo htmlspecialchars($userConnected->getNomUtilisateur()); ?>" />
                </div>
                <div class="artist-name" style="font-size:25px;"><strong>Bienvenu <?php echo $user_name ?></strong></div>
            </div>
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

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <div class="stats-container" style="display: grid;grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));gap: 20px;margin-bottom: 20px;">
                <div class="stat-card" style="background-color: var(--spotify-gray);border-radius: 8px;padding: 20px;transition: transform 0.3s; position:relative ; right:18px;">
                    <div class="stat-title">TOTAL STREAMS</div>
                    <div class="stat-value">1.2M</div>
                    <div class="stat-change">+15% this month</div>
                </div>
                <div class="stat-card" style="background-color: var(--spotify-gray);border-radius: 8px;padding: 20px;transition: transform 0.3s; position:relative ; right:10px;">
                    <div class="stat-title">MONTHLY LISTENERS</div>
                    <div class="stat-value">345K</div>
                    <div class="stat-change">+8% this month</div>
                </div>
                <div class="stat-card" style="background-color: var(--spotify-gray);border-radius: 8px;padding: 20px;transition: transform 0.3s; position:relative ; left:6px;">
                    <div class="stat-title">FOLLOWERS</div>
                    <div class="stat-value">89K</div>
                    <div class="stat-change">+5% this month</div>
                </div>
                <div class="stat-card" style="background-color: var(--spotify-gray);border-radius: 8px;padding: 20px;transition: transform 0.3s;position:relative ; left:18px;">
                    <div class="stat-title">REVENUE</div>
                    <div class="stat-value">$4,512</div>
                    <div class="stat-change">+12% this month</div>
                </div>
            </div>

            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">Top Performing Songs</div>
                </div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Duree</th>
                            <th>Streams</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody id="top-songs-table">
                    <?php foreach ($songstrier as $song): ?>
                            <tr>
                                <td> <?php echo $counter1++; ?></td>
                                <td>
                                    <?php
                                        // Correction du chemin de l'image pour qu'il soit relatif à la racine du projet
                                        $base_path = "/projetweb/assets/includes/"; 
                                        $image_path = str_replace("C:/xampp/htdocs", "", $song['image_path']);
                                    ?>
                                    <img src="<?php echo $image_path; ?>" width="100" height="100" alt="Image de la chanson">
                                </td>
                                <td><?php echo htmlspecialchars($song['song_title']); ?></td>
                                <td><?php echo htmlspecialchars($song['album_name']); ?></td>
                                <td><?php echo htmlspecialchars($song['duree']); ?></td>
                                <td><?php echo isset($song['total_streams']) ? htmlspecialchars($song['total_streams']) : 'Aucune donnée'; ?></td>
                                <td>
                                <a href="music/download.php?song_id=<?php echo $song['id']; ?>" class="btn btn-info"><i class="fa-solid fa-download fa-xl" style="color:white;"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <div class="" style="display :flex; justify-content: space-between;">
                <!-- Left Section: Streaming Performance -->
                <div class="content-section">
                    <div class="section-header" style="">
                        <div class="section-title" style="">Streaming Performance</div>
                    </div>
                    <div class="chart-container" style="width: 500px; height: 400px;">
                        <canvas id="stream-chart"></canvas>
                    </div>
                </div>

                <!-- Right Section: Listener Growth -->
                <div class="content-section">
                    <div class="section-header">
                        <div class="section-title">Top Musiques Performantes</div>
                    </div>
                    <div class="chart-container" style="width: 600px; height: 400px;">
                        <canvas id="artistChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Music Tab -->
        <div id="music" class="tab-content">
             <button id="add-music-btn" class="btn btn-primary" style="display: block; float: right; margin: 20px;">
                <i class="fas fa-plus"></i> Add New Music
            </button>
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">All Your Music</div>
                </div>
                <div class="loader" id="music-loader"></div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Release Date</th>
                            <th>Duree</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="all-songs-table">
                        <?php foreach ($songs as $song): ?>
                            <tr>
                                <td> <?php echo $counter++?></td>
                                <td>
                                    <?php
                                        $base_path = "/projetweb/assets/includes/"; // The base path relative to the document root
                                        $image_path = str_replace("C:/xampp/htdocs", "", $song['image_path']); // Remove the local file system path
                                    ?>
                                    <img src="<?php echo $image_path; ?>" width="70" height="70" alt="">
                                </td>
                                <td><?php echo htmlspecialchars($song['song_title']); ?></td>
                                <td><?php echo htmlspecialchars($song['album_name']); ?></td>
                                <td><?php echo htmlspecialchars($song['release_date']); ?></td>
                                <td><?php echo htmlspecialchars($song['duree']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-secondary edit-song" 
                                            data-id="<?php echo $song['id']; ?>" 
                                            data-title="<?php echo htmlspecialchars($song['song_title']); ?>"
                                            data-album="<?php echo htmlspecialchars($song['album_name']); ?>"
                                            data-release="<?php echo htmlspecialchars($song['release_date']); ?>"
                                            data-duree="<?php echo htmlspecialchars($song['duree']); ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="submit" class="btn btn-danger delete-song" id="supprimer" data-id="<?php echo $song['id']; ?>"><i class="fas fa-trash"></i> Delete</button>
                                </td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
        <!-- user Tab  omar-->
        <div id="users" class="tab-content">
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">All Users</div>
                    <button id="bulk-add-btn" class="btn btn-secondary"><i class="fas fa-upload"></i> Bulk Upload</button>
                </div>
                <div class="loader" id="users-loader"></div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Profile Image</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Score</th>
                            <th>Account Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="all-users-table">
                        <?php $counter = 1; ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <?php 
                                $imageuser= htmlspecialchars($user->getImagePath());
                                $imagePathuser = str_replace('\\', '/', $imageuser);
                                // Remove the local absolute path part (e.g. 'C:/xampp/htdocs')
                                $imagePathuser = str_replace('C:/xampp/htdocs', '', $imagePathuser);
                                // Remove any leading slash if it's present
                                $imagePathuser = ltrim($imagePathuser, '/');
                                // Construct the final relative URL to the image
                                $imageURLuser = "/" . $imagePathuser;
                                ?>
                                <style>
                                    .default-user-icon {
                                        width: 100px;           /* Adjust as needed */
                                        height: 100px;
                                        background-color: #ccc; /* Light gray background */
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                        border-radius: 8px;     /* Optional: rounded corners */
                                        font-size: 40px;
                                        color: #555;
                                    }

                                    .user-img {
                                        width: 100px; /* Same as icon box */
                                        height: 100px;
                                        object-fit: cover;
                                        border-radius: 8px;
                                    }

                                </style>
                               <td>
                                    <?php if (empty($imageuser)) { ?>
                                        <div class="default-user-icon">
                                            <i class="fas fa-user"></i> <!-- Font Awesome user icon -->
                                        </div>
                                    <?php } else { ?>
                                        <img src="<?php echo htmlspecialchars($imageURLuser); ?>" 
                                            class="user-img" 
                                            alt="Profile de <?php echo htmlspecialchars($user->getNomUtilisateur()); ?>">
                                    <?php } ?>
                                </td>

                                <td><?php echo htmlspecialchars($user->getNomUtilisateur()); ?></td>
                                <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                                <td><?php echo htmlspecialchars($user->getTypeUtilisateur()); ?></td>
                                <td><?php echo htmlspecialchars($user->getScore()); ?></td>
                                <td><?php echo htmlspecialchars($user->getDateCreation()); ?></td>
                                <td>
                                <style>
                                    body {
                                    font-family: Arial, sans-serif;
                                    background-color: #121212;
                                    color: #fff;
                                    margin: 0;
                                    padding: 0;
                                }

                                /* Modal styles */
                                .modal {
                                    display: none;
                                    position: fixed;
                                    z-index: 1;
                                    left: 0;
                                    top: 0;
                                    width: 100%;
                                    height: 100%;
                                    overflow: auto;
                                    background-color: rgba(0, 0, 0, 0.4);
                                    padding-top: 60px;
                                }

                                .modal-content {
                                    background-color: #1e1e1e;
                                    margin: 5% auto;
                                    padding: 30px;
                                    border-radius: 10px;
                                    width: 80%;
                                    max-width: 500px;
                                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                                }

                                .modal h3 {
                                    text-align: center;
                                    font-size: 28px;
                                    font-weight: normal;
                                    color: #fff;
                                    margin-bottom: 20px;
                                }

                                .form-group {
                                    margin-bottom: 20px;
                                }

                                .form-group label {
                                    display: block;
                                    margin-bottom: 5px;
                                    font-weight: bold;
                                }

                                .form-group input,
                                .form-group select {
                                    width: 100%;
                                    padding: 12px;
                                    border: none;
                                    border-radius: 5px;
                                    background-color: #333;
                                    color: #fff;
                                    font-size: 14px;
                                }

                                .form-group input:focus,
                                .form-group select:focus {
                                    outline: none;
                                    border: 2px solid #1db954;
                                    background-color: #444;
                                }

                                .form-footer {
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                }

                                .form-footer button {
                                    padding: 12px;
                                    border: none;
                                    border-radius: 5px;
                                    color: white;
                                    cursor: pointer;
                                    font-weight: bold;
                                    width: 48%;
                                }

                                .form-footer .btn-primary {
                                    background-color: #1db954;
                                }

                                .form-footer .btn-secondary {
                                    background-color: #555;
                                }

                                .close {
                                    color: #aaa;
                                    float: right;
                                    font-size: 28px;
                                    font-weight: bold;
                                    cursor: pointer;
                                }

                                .close:hover,
                                .close:focus {
                                    color: white;
                                    text-decoration: none;
                                    cursor: pointer;
                                }

                                /* Optional: Add some animation for the modal opening */
                                @keyframes slideIn {
                                    from {
                                        opacity: 0;
                                        transform: translateY(-50px);
                                    }
                                    to {
                                        opacity: 1;
                                        transform: translateY(0);
                                    }
                                }

                                .modal-content {
                                    animation: slideIn 0.3s ease-out;
                                }
                            </style>

                            <!-- Modal for Editing User Information -->
                            <div id="editUserModal" class="modal">
                                <div class="modal-content">
                                    <span class="close" onclick="closeModal()">&times;</span>
                                    <h3>Edit User Information</h3>
                                    <!-- Formulaire utilisant les méthodes de l'objet User -->
                                    <form action="user/update_user.php" method="POST" id="editUserForm">
                                        <input type="hidden" name="id" id="userId" value="<?php echo htmlspecialchars($user->getArtisteId()); ?>">

                                        <div class="form-group">
                                            <label for="name">User Name</label>
                                            <input type="text" class="form-control" name="name" id="userName" 
                                                value="<?php echo htmlspecialchars($user->getNomUtilisateur()); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" name="email" id="userEmail" 
                                                value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="role">Role</label>
                                            <select class="form-control" name="role" id="userRole" required>
                                                <option value="Admin" <?php echo $user->getTypeUtilisateur() === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                                <option value="Artiste" <?php echo $user->getTypeUtilisateur() === 'Artiste' ? 'selected' : ''; ?>>Artist</option>
                                                <option value="User" <?php echo $user->getTypeUtilisateur() === 'User' ? 'selected' : ''; ?>>User</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" name="status" id="userStatus" required>
                                                <option value="Active" <?php echo $user->getScore() == 'Active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="Inactive" <?php echo $user->getScore() == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>

                                        <div class="form-footer">
                                            <button type="submit" class="btn-primary">Save Changes</button>
                                            <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Bouton pour ouvrir le modal -->
                            <a href="javascript:void(0);" 
                            onclick="openEditModal(
                                <?php echo $user->getArtisteId(); ?>, 
                                '<?php echo htmlspecialchars($user->getNomUtilisateur(), ENT_QUOTES); ?>', 
                                '<?php echo htmlspecialchars($user->getEmail(), ENT_QUOTES); ?>', 
                                '<?php echo htmlspecialchars($user->getTypeUtilisateur(), ENT_QUOTES); ?>', 
                                '<?php echo htmlspecialchars($user->getScore(), ENT_QUOTES); ?>'
                            )" 
                            class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <script>
                                // Function to open the modal and populate the form with the user data
                                function openEditModal(userId, userName, userEmail, userRole, userStatus) {
                                    // Populate the form fields
                                    document.getElementById('userId').value = userId;
                                    document.getElementById('userName').value = userName;
                                    document.getElementById('userEmail').value = userEmail;
                                    document.getElementById('userRole').value = userRole;
                                    document.getElementById('userStatus').value = userStatus;

                                    // Show the modal
                                    document.getElementById('editUserModal').style.display = 'block';
                                }

                                // Function to close the modal
                                function closeModal() {
                                    document.getElementById('editUserModal').style.display = 'none';
                                }

                                // Close the modal if the user clicks anywhere outside of it
                                window.onclick = function(event) {
                                    if (event.target === document.getElementById('editUserModal')) {
                                        closeModal();
                                    }
                                }
                            </script>

                                
                                <!-- Delete button -->
                                <button type="button" class="btn btn-danger delete-user" id="supprimer" data-id="<?php echo $user->getArtisteId(); ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>

                            <!-- JavaScript to handle the delete button click -->
                            <script>
                                // Attach an event listener to the delete button using the class 'delete-user'
                                document.querySelectorAll('.delete-user').forEach(function(button) {
                                    button.addEventListener('click', function() {
                                        var userId = this.getAttribute('data-id'); // Get the user ID from the button's data-id attribute
                                        
                                        // Confirm deletion
                                        if (confirm('Are you sure you want to delete this user?')) {
                                            // Redirect to delete_user.php with the user ID in the URL
                                            window.location.href = 'user/delete_user.php?id=' + userId;
                                        }
                                    });
                                });
                            </script>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- paiment Tab  islem-->
        <div id="paiment" class="tab-content">
            <div class="paiment-stats-container">
                <!-- Carte de statistiques pour tous les paiements -->
                <div class="stat-card-paiment total">
                    <div class="stat-title-paiment">TOTAL DES PAIEMENTS</div>
                    <div class="stat-value-paiment"><?php echo count($paiements); ?></div>
                    <div class="stat-change">Toutes méthodes confondues</div>
                </div>

                <!-- Carte de statistiques pour les paiements par carte -->
                <div class="stat-card-paiment carte">
                    <div class="stat-title-paiment">PAIEMENTS CARTE</div>
                    <div class="stat-value-paiment"><?php echo count($paiementsCarts); ?></div>
                    <div class="stat-change">
                        <?php 
                        $totalPaiements = count($paiements);
                        $pourcentage = $totalPaiements > 0 ? round(count($paiementsCarts) / $totalPaiements * 100, 2) : 0;
                        echo $pourcentage;
                        ?>% du total
                    </div>
                </div>


                <!-- Carte de statistiques pour les paiements mobiles -->
                <div class="stat-card-paiment mobile">
                    <div class="stat-title-paiment">PAIEMENTS MOBILE</div>
                    <div class="stat-value-paiment"><?php echo count($paiementsMobile); ?></div>
                    <div class="stat-change">
                        <?php 
                        $totalPaiements = count($paiements);
                        $pourcentageMobile = $totalPaiements > 0 ? round(count($paiementsMobile) / $totalPaiements * 100, 2) : 0;
                        echo $pourcentageMobile;
                        ?>% du total
                    </div>
                </div>
            </div>
                                <div class="tabs">
                                    <button class="tab-link active" data-tab="paiment">Tous les paiements</button>
                                    <button class="tab-link" data-tab="paimentc">Paiement carte</button>
                                    <button class="tab-link" data-tab="paiement_mobile">Paiement mobile</button>
                                    
                                </div>
                                <div class="content-section">
                                    <div class="section-header">
                                        <div class="section-title">Tous les paiements</div>
                                        <input type="text" id="search-all" class="search-input" placeholder="Rechercher dans tous les champs...">
                                    </div>
                                    <table class="song-table" id="transactions-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Abonnement</th>
                                        <th>User ID</th>
                                        <th>Méthode de paiement</th>
                                        <th>Actions</th>
                                        <th>PDF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $counter = 1; foreach ($paiements as $paiment): ?>
                                        <tr>
                        <td><?php echo $counter++; ?></td>
                        <td class="searchable"><?php echo htmlspecialchars($paiment['Date']); ?></td>
                        <td class="searchable abonnement"><?php echo htmlspecialchars($paiment['Abonnement']); ?></td>
                        <td class="searchable user-id"><?php echo htmlspecialchars($paiment['user_id']); ?></td>
                        <td class="searchable payment-method"><?php echo htmlspecialchars($paiment['payment_method']); ?></td>
                        <td>
                            <button class="btn btn-secondary edit-paiment-song" data-id="<?= $paiment['ID']; ?>" 
                                                                data-type="paiments"
                                                                data-date="<?php echo htmlspecialchars($paiment['Date']); ?>"
                                                                data-abonnement="<?php echo htmlspecialchars($paiment['Abonnement']); ?>"
                                                                data-methode="<?php echo htmlspecialchars($paiment['payment_method']); ?>"
                                                            >
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="submit" class="btn btn-danger delete-paiment" id="supprimer" data-id="<?= $paiment['ID']; ?>" 
                                                                                                    data-type="paiments"
                                                                                                    >
                                <input type="text" class="type_c" hidden>
                                <i class="fas fa-trash"></i> Delete
                                
                            </button>
                        </td>
                        <td>
                            <a href="facture.php?id=<?= $paiment['ID'] ?>" target="_blank" class="btn btn-info btn-sm" style="border:none; font-size:15px; color: inherit; text-decoration: none;">
                                <i class="fas fa-file-pdf"></i> 
                            </a>
                        </td>
                        
                                            
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                                </div>
                            </div>

                <!-- Paiement par carte -->
                <div id="paimentc" class="tab-content">
                    <div class="tabs">
                            <button class="tab-link active" data-tab="paiment">Tous les paiements</button>
                            <button class="tab-link" data-tab="paimentc">Paiement carte</button>
                            <button class="tab-link" data-tab="paiement_mobile">Paiement mobile</button>

                            </div>
                    <div class="content-section">
                        <div class="section-header">
                            <div class="section-title">Paiement par carte</div>
                            <input type="text" id="search-cards" class="search-input" placeholder="Rechercher par numéro...">
                        </div>
                        <table class="song-table" id="cards-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type de carte</th>
                                    <th>Numéro de carte</th>
                                    <th>Date d'expiration</th>
                                    <th>ID de transaction</th>
                                    
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; foreach ($paiementsCarts as $paimentc): ?>
                                    <tr>
                                    <td><?php echo $counter++; ?></td>
                        <td class="searchable type-carte"><?php echo htmlspecialchars($paimentc['Type_Carte']); ?></td>
                        <td class="searchable numero-carte"><?php echo htmlspecialchars($paimentc['Numero_Carte']); ?></td>
                        <td class="searchable"><?php echo htmlspecialchars($paimentc['Date_Expiration']); ?></td>
                        <td class="searchable"><?php echo htmlspecialchars($paimentc['Transaction_id']); ?></td>
                                        <td>
                                            <button class="btn btn-secondary edit-paimentc-song" data-id="<?= $paimentc['ID']; ?>" 
                                                                            data-type="paimentc"
                                                                            data-carte="<?php echo htmlspecialchars($paimentc['Type_Carte']); ?>"
                                                                            data-numero="<?php echo htmlspecialchars($paimentc['Numero_Carte']); ?>"
                                                                            data-expiration="<?php echo htmlspecialchars($paimentc['Date_Expiration']); ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="submit" class="btn btn-danger delete-paiment" id="supprimer" data-id="<?= $paimentc['ID']; ?>" 
                                                                                                                    data-type="paimentc">
                                                <input type="text" class="type_c" hidden>
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>



                <!-- Paiement par mobile -->
                <div id="paiement_mobile" class="tab-content">
                        <div class="tabs">
                            <button class="tab-link active" data-tab="paiment">Tous les paiements</button>
                            <button class="tab-link" data-tab="paimentc">Paiement carte</button>
                            <button class="tab-link" data-tab="paiement_mobile">Paiement mobile</button>
                        </div>
                    <div class="content-section">
                        <div class="section-header">
                            <div class="section-title">Paiement par Mobile</div>
                            
                    <input type="text" id="search-mobile" class="search-input" placeholder="Rechercher par numéro...">
                        </div>
                        <table class="song-table" id="mobile-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fournisseur</th>
                                    <th>Numéro de téléphone</th>
                                    <th>Date d'expiration</th>
                                    <th>Transaction ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; foreach ($paiementsMobile as $paiment): ?>
                                    <tr>
                                    <td><?php echo $counter++; ?></td>
                                        <td class="searchable provider"><?php echo htmlspecialchars($paiment['mobile_provider']); ?></td>
                                        <td class="searchable numero-mobile"><?php echo htmlspecialchars($paiment['phone_number']); ?></td>
                                        <td class="searchable"><?php echo htmlspecialchars($paiment['Date_Expiration']); ?></td>
                                        <td class="searchable"><?php echo htmlspecialchars($paiment['transaction_id']); ?></td>
                                        <td>
                                            <button class="btn btn-secondary edit-paimentmobile-song" data-id="<?= $paiment['ID']; ?>" 
                                                                            data-type="paiment_mobile"
                                                                            data-provider="<?php echo htmlspecialchars($paiment['mobile_provider']); ?>"
                                                                            data-numero="<?php echo htmlspecialchars($paiment['phone_number']); ?>"
                                                                            date_exp="<?php echo htmlspecialchars($paiment['Date_Expiration']); ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="submit" class="btn btn-danger delete-paiment" id="supprimer" data-id="<?= $paiment['ID']; ?>" 
                                                                                                                    data-type="paiment_mobile">
                                                <input type="text" class="type_c" hidden>
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                        
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

        <div id="news" class="tab-content">
             <button id="add-news-btn" class="btn btn-primary" style="display: block; float: right; margin: 20px;">
                <i class="fas fa-plus"></i> Ajouter une publication
            </button>
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">Gestion des Publications</div>
                </div>
                <div class="loader" id="music-loader"></div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Release Date</th>
                            <th>Duree</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="all-songs-table">
                        <?php $counter2 = 1; ?>
                        <?php foreach ($news as $new): ?>
                            <tr>
                                <td> <?php echo $counter2++?></td>
                                <td>
                                    <?php
                                        $imagePathnews = str_replace('\\', '/', $new['image']);
                                        // Remove the local absolute path part (e.g. 'C:/xampp/htdocs')
                                        $imagePathnews = str_replace('C:/xampp/htdocs', '', $imagePathnews);
                                        // Remove any leading slash if it's present
                                        $imagePathnews = ltrim($imagePathnews, '/');
                                        // Construct the final relative URL to the image
                                        $imageURLnews = "/" . $imagePathnews;
                                       
                                    ?>
                                    <img src="<?php echo $imageURLnews; ?>" width="70" height="70" alt="">
                                </td>
                                <td><?php echo htmlspecialchars($new['titre']); ?></td>
                                <td><?php echo htmlspecialchars($new['contenu']); ?></td>
                                <td><?php echo htmlspecialchars($new['date_publication']); ?></td>
                                <td><?php echo htmlspecialchars($new['description']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-secondary edit-news" 
                                        data-id="<?php echo $new['id']; ?>" 
                                        data-title="<?php echo htmlspecialchars($new['titre']); ?>"
                                        data-album="<?php echo htmlspecialchars($new['contenu']); ?>"
                                        data-release="<?php echo htmlspecialchars($new['date_publication']); ?>"
                                        data-duree="<?php echo htmlspecialchars($new['description']); ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="submit" class="btn btn-danger delete-news" id="supprimer" data-id="<?php echo $new['id']; ?>"><i class="fas fa-trash"></i> Delete</button>
                                </td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
        <!-- jeux Tab    cahine-->
        <div id="jeux" class="tab-content">
  <div class="content-section" id="games-section">
    <!-- Title + Search -->
    <div class="section-header">
      <div class="section-title">All Your Games</div>
    </div>
    <div class="search-container" style="margin: 20px 0;">
      <input type="text" id="search-games" placeholder="Search games..." class="search-input">
    </div>

    <!-- Add/Edit Game Form -->
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

    <!-- Games Table -->
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
        <?php $counterjeux = 1; ?>
        <?php foreach ($games as $game): ?>
        <tr>
          <td><?= $counterjeux++;  ?></td>
          <td><img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/<?= $game['cover_path'] ?? 'default.jpg' ?>" width="70" height="70" onerror="this.src='/projetweb/View/tunify_avec_connexion/gamification/public/assets/default.jpg'"></td>
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

  <!-- Questions Section -->
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



        <!-- Rewards Tab -->
        <div id="recompenses" class="tab-content">
  <div class="content-section">
    <div class="section-header">
      <div class="section-title">Manage Rewards</div>
      <button id="add-new-reward-btn" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Reward
      </button>
    </div>

    <form id="add-reward-form" action="backoffice.php?action=add_reward" method="POST" enctype="multipart/form-data" style="display: none; margin-top: 20px;">
      <input type="hidden" name="id_reward" id="edit-id-reward">
      <div class="form-group"><label>Reward Name:</label><input type="text" name="nom_reward" class="form-control" required></div>
      <div class="form-group"><label>Required Points:</label><input type="number" name="points_requis" class="form-control" required></div>
      <div class="form-group"><label>Type:</label>
        <select name="type_reward" class="form-control" required>
          <option value="">Select Type</option>
          <option value="discount">Discount</option>
          <option value="premium">Premium</option>
          <option value="physical">Physical</option>
        </select>
      </div>
      <div class="form-group"><label>Availability:</label>
        <select name="disponibilite" class="form-control" required>
          <option value="1">Available</option>
          <option value="0">Unavailable</option>
        </select>
      </div>
      <div class="form-group">
        <label>Image:</label>
        <input type="file" name="image" class="form-control" accept="image/*">
        <img id="reward-image-preview" src="" style="display: none; max-width: 200px; margin-top: 10px;">
      </div>

      <button type="submit" class="btn btn-success">Save Reward</button>
      <button type="button" class="btn btn-secondary" onclick="document.getElementById('add-reward-form').style.display='none'">Cancel</button>
    </form>

    <div class="search-container" style="margin: 20px 0;">
      <input type="text" id="search-rewards" placeholder="Search rewards..." class="search-input">
    </div>

    <div class="table-responsive">
      <table class="song-table" id="rewards-table" style="width:100%;">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Type</th>
            <th>Points</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php $countreawrd = 1; ?>
          <?php if (!empty($rewards) && is_array($rewards)): ?>
            <?php foreach ($rewards as $reward): ?>
              <tr>
                <td><?= $countreawrd++;  ?></td>
                <td>
                  <?php if (!empty($reward['image_path'])): ?>
                    <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/<?= htmlspecialchars($reward['image_path']) ?>" width="50" height="50">
                  <?php else: ?>
                    <div class="no-image">No Image</div>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($reward['nom_reward'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($reward['type_reward'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($reward['points_requis'] ?? 0) ?></td>
                <td>
                  <span class="badge <?= ($reward['disponibilite'] ?? 0) ? 'badge-success' : 'badge-danger' ?>">
                    <?= ($reward['disponibilite'] ?? 0) ? 'Available' : 'Unavailable' ?>
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-sm btn-danger delete-reward" data-id="<?= (int)($reward['id_reward'] ?? 0) ?>">
                      <i class="fas fa-trash"></i> Delete
                    </button>
                    <button class="btn btn-sm btn-primary edit-reward"
                            data-id="<?= (int)($reward['id_reward'] ?? 0) ?>"
                            data-nom="<?= htmlspecialchars($reward['nom_reward'] ?? '') ?>"
                            data-points="<?= (int)($reward['points_requis'] ?? 0) ?>"
                            data-type="<?= htmlspecialchars($reward['type_reward'] ?? '') ?>"
                            data-dispo="<?= (int)($reward['disponibilite'] ?? 0) ?>"
                            data-image="<?= htmlspecialchars($reward['image_path'] ?? '') ?>">
                      <i class="fas fa-edit"></i> Edit
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

        <!-- Profile Tab -->
        <div id="profile" class="tab-content">
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">Artist Profile</div>
                    <button class="btn btn-secondary"><i class="fas fa-edit"></i> Edit Profile</button>
                </div>
                <div style="display: flex; margin-top: 20px;">
                    <div style="flex: 0 0 200px; margin-right: 30px;">
                        <img src="" alt="Artist Profile" style="width: 100%; border-radius: 8px;">
                    </div>
                    <div style="flex: 1;">
                        <h2 style="margin-bottom: 20px;">Your Artist Name</h2>
                        <p style="margin-bottom: 15px; line-height: 1.6;">Artist bio goes here. This is where you can tell your fans about yourself, your music style, influences, and journey. Make it personal and engaging to connect with your audience.</p>
                        <div style="margin-top: 30px;">
                            <h3 style="margin-bottom: 15px;">Contact & Social</h3>
                            <p><i class="fas fa-envelope"></i> artist@example.com</p>
                            <p><i class="fab fa-instagram"></i> @yourartistname</p>
                            <p><i class="fab fa-twitter"></i> @yourartistname</p>
                            <p><i class="fab fa-youtube"></i> Your Artist Name</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <?php
    // Get image path using object method
    $imagePath = (!empty($userConnected->getImagePath())) 
        ? '/projetweb/View/backoffice/user/' . htmlspecialchars($userConnected->getImagePath())
        : '/projetweb/assets/img/default.jpg'; // Ensure default image path is correct
    ?>
    

    <div id="settings" class="tab-content">
        <div class="content-section">
            <div class="section-header">
                <div class="section-title">Account Settings</div>
            </div>

            <form action="user/update_settings.php" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($userConnected->getArtisteId()); ?>">

                <div class="form-group">
                    <label>Profile Picture</label><br>
                    <img src="<?php echo $relativePath?>" 
                        width="100" 
                        height="100" 
                        style="border-radius: 50%; margin-bottom: 10px;" 
                        alt="Profile Picture of <?php echo htmlspecialchars($userConnected->getNomUtilisateur()); ?>">
                    <input type="file" name="profile_image" class="form-control">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" 
                        name="email" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($userConnected->getEmail()); ?>" 
                        required>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Leave blank to keep current password">
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Save Changes</button>
            </form>
        </div>
    </div>    
</div>
    <!-- Add Music Modal -->
    <form action="music/ajout.php" method="POST" enctype="multipart/form-data" id="song-form">
        <div id="add-music-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Add New Music</div>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="upload-area" id="cover-upload">
                    <label for="file-upload" class="upload-icon">
                        <i class="fas fa-image" style="font-size: 40px;"></i>
                    </label>
                    <input type="file" id="file-upload" name="image_input" accept="image/*" style="display: block; visibility: hidden;"/>
                    <p id="cover-upload-text">Drop your album artwork here or click to upload</p>
                    <p id="recommended-size" style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">Recommended size: 1000x1000px</p>
                </div>                    
                <div class="form-group">
                    <label for="song-title">Song Title</label>
                    <input type="text" id="song-title" name="song-title" class="form-control"  placeholder="Enter Song name" required>
                    <div id="song-title-error" class="error-message" style="color: red; display: none;"></div>
                </div>
                
                <div class="form-group">
                    <label for="album-name">Album Name</label>
                    <input type="text" id="album-name"  name="album-name" class="form-control" placeholder="Enter album name" required>
                    <div id="album-name-error" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="song-release">Release Date</label>
                    <input type="date" id="song-release" name="song-release" class="form-control" placeholder="YYYY-MM-DD" required>
                    <div id="release-date-error" class="error-message" style="color: red; display: none;"></div> <!-- Error message here -->
                </div>
                <div class="form-group">
                    <label for="duree-relese">Duree</label>
                    <input type="text" name="duree" id="duree" class="form-control" placeholder="MM:SS/02:20" required>
                    <div id="duree-error" class="error-message" style="color: red; display: none;"></div>
                </div>
                <br>
                <div class="upload-area" id="audio-upload">
                    <label for="audio-upload-input" class="upload-icon">
                        <i class="fas fa-music" style="font-size: 40px;"></i>
                    </label>
                    <input type="file" id="audio-upload-input" name="music_input" accept="audio/mp3, audio/wav" style="display: block; visibility: hidden;"/>
                    <p id="audio-upload-text">Drop your audio file here or click to upload</p>
                    <p id="text" style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">Accepted formats: MP3, WAV (max 50MB)</p>
                </div> 
                
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-add">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-add">Add Song</button>
                </div>
                
            </div>
        </div>
    </form>
     <form action="news/ajouternews.php" method="POST" enctype="multipart/form-data" id="news-form">
        <div id="add-news-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Add New News</div>
                    <button class="close-modal">&times;</button>
                </div>                   

                <div class="form-group">
                    <label for="news-title">News Title</label>
                    <input type="text" id="news-title" name="news-title" class="form-control" placeholder="Enter news title" required>
                    <div id="news-title-error" class="error-message" style="color: red; display: none;"></div>
                </div>
                
                <div class="form-group">
                    <label for="news-content">Contenu</label>
                    <input type="text" id="news-content" name="news-content" class="form-control" placeholder="Enter news content" required>
                    <div id="news-content-error" class="error-message" style="color: red; display: none;"></div>
                </div>

                <div class="form-group">
                    <label for="news-date">Description:</label>
                    <textarea id="news-date" name="news-date" class="form-control" placeholder="Enter description" required></textarea>
                    <div id="news-date-error" class="error-message" style="color: red; display: none;"></div>
                </div>


                <div class="upload-area" id="news-upload">
                    <label for="news-upload-input" class="upload-icon">
                        <i class="fas fa-newspaper" style="font-size: 40px;"></i>
                    </label>
                    <input type="file" id="news-upload-input" name="news_file" accept=".pdf,.doc,.docx,.jpg,.png" style="display: block; visibility: hidden;"/>
                    <p id="news-upload-text">Drop your file here or click to upload</p>
                    <p class="hint" style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">Accepted formats: PDF, DOC, JPG, PNG (max 50MB)</p>
                </div> 

                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-add">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-add">Add News</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Edit Music Modal -->
    <form action="music/edit.php" method="POST" enctype="multipart/form-data" id="edit-form">
        <div id="edit-music-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Song</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="hidden" name="song_id" id="song_id"> <!-- Hidden input for song ID -->
                <div class="upload-area" id="edit-cover-upload">
                        <label for="file-edit" class="upload-icon">
                            <i class="fas fa-image" style="font-size: 40px;"></i>
                        </label>
                        <input type="file" id="file-edit" name="image_input" accept="image/*" style="display: block; visibility: hidden;"/>
                        <p id="edit-text" name="text_edit" >edit si tu veux </p>
                        <p id="recommendeds-size" style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">Recommended size: 1000x1000px</p>
                    </div>
                <div class="form-group">
                    <label for="edit-song-title">Song Title</label>
                    <input type="text" id="edit-song-title" name="edit-song-title" class="form-control" placeholder="Enter song title" value="">
                    <div id="song-title-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="edit-song-album">Album</label>
                    <input type="text" id="edit-song-album" name="edit-song-album" class="form-control" placeholder="Enter album name"value="">
                    <div id="album-name-error-edit" class="error-message" style="color: red; display: none;"></div>

                </div>
                <div class="form-group">
                    <label for="edit-song-release">Release Date</label>
                    <input type="text" id="edit-song-release" name="temps" class="form-control" 
                    value="">
                    <div id="release-date-error-edit" class="error-message" style="color: red; display: none;"></div> <!-- Error message here -->
                </div>
                <div class="form-group">
                    <label for="edit-song-release">Duree</label>
                    <input type="text" id="edit-song-duree" name="edit-duree" class="form-control" placeholder="MM:DD/02:20"
                    value="">   
                    <div id="duree-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="upload-area" id="audio-edit-upload">
                    <label for="audio-edit-input" class="upload-icon">
                        <i class="fas fa-music" style="font-size: 40px;"></i>
                    </label>
                    <input type="file" id="audio-edit-input" name="music_input" accept="audio/mp3, audio/wav" style="display: block; visibility: hidden;"/>
                    <p id="audio-edit-text">edit si tu veux</p>
                    <p id="texts" style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">Accepted formats: MP3, WAV (max 50MB)</p>
                </div> 
                <input type="hidden" id="edit-song-id">
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    <form action="news/edit.php" method="POST" enctype="multipart/form-data" id="edit-news-form">
    <div id="edit-news-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Edit News</div>
                <button type="button" class="close-modal">&times;</button>
            </div>
            <input type="hidden" name="news_id" id="news_id"> <!-- Hidden input for news ID -->

            <div class="upload-area" id="edit-news-upload">
                <label for="news-file-edit" class="upload-icon">
                    <i class="fas fa-file-alt" style="font-size: 40px;"></i>
                </label>
                <input type="file" id="news-file-edit" name="news_file" accept=".pdf,.doc,.docx,.jpg,.png"
                    style="display: block; visibility: hidden;" />
                <p id="edit-news-text">Upload a new file (optional)</p>
                <p style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">
                    Accepted: PDF, DOC, DOCX, JPG, PNG (max 50MB)
                </p>
            </div>

            <div class="form-group">
                <label for="edit-news-title">News Title</label>
                <input type="text" id="edit-news-title" name="news-title" class="form-control"
                    placeholder="Enter news title" value="">
                <div id="news-title-error" class="error-message" style="color: red; display: none;"></div>
            </div>

            <div class="form-group">
                <label for="edit-news-content">News Content</label>
                <textarea id="edit-news-content" name="news-content" class="form-control"
                    placeholder="Write the news content here..."></textarea>
                <div id="news-content-error" class="error-message" style="color: red; display: none;"></div>
            </div>

            <div class="form-group">
                <label for="edit-news-date">Publication Date</label>
                <input type="date" id="edit-news-date" name="news-date" class="form-control" value="">
                <div id="news-date-error" class="error-message" style="color: red; display: none;"></div>
            </div>

            <div class="form-footer">
                <button class="btn btn-secondary" id="cancel-edit-news">Cancel</button>
                <button type="submit" class="btn btn-primary" id="confirm-edit-news">Save Changes</button>
            </div>
        </div>
    </div>
</form>

    <form action="payment/update_paimnet.php" method="POST" enctype="multipart/form-data" id="edit-paiment-form">
        <div id="edit-paiment-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Paiment</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="hidden" name="song_id" id="song_paiment_id"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-date-title">Date Paiment</label>
                    <input type="text" id="edit-date-title" name="edit-paiment-title" class="form-control" placeholder="Enter song title" value="">
                </div>
                <div class="form-group">
                    <label for="edit-abonnement">Abonnement</label>
                    <select id="edit-abonnement" name="edit-song-album" class="form-control" required>
                        <option value="" disabled selected>Choose abonnement</option>
                        <option value="Mini">Mini</option>
                        <option value="Familial">Familial</option>
                        <option value="Duo">Duo</option>
                        <option value="Personnel">Personnel</option>
                    </select>
                </div>
                <input type="hidden" id="edit-song-id">
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-paiment-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-paiment-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    <form action="payment/update_paimnet.php" method="POST" enctype="multipart/form-data" id="edit-paimentc-form">
        <div id="edit-paimentc-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Paiment</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="hidden" name="song_paimentc_id" id="song_paimentc_id"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-carte-title">Numero carte</label>
                    <input type="text" id="edit-carte-title" name="edit-paimentc-numero" class="form-control" placeholder="Enter song title" value="">
                    <div id="song-title-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="edit-carte-type">type carte</label>
                    <select id="edit-carte-type" name="edit-paimentc-methode" class="form-control" required>
                        <option value="visa">visa</option>
                        <option value="mastercard">MasterCard</option>
                        <option value="amex">American Express</option>
                    </select>
                </div>
                <input type="hidden" name="type_paimentc_id" id="type_paimentc"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-date-title">date expiration</label>
                    <input type="text" id="edit-datec-title" name="edit-paimentc-title" class="form-control" placeholder="Enter song title" value="">
                </div>
                <input type="hidden" id="edit-type-paiment" name="edit-type-paiment">
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-paimentc-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-paimentc-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    <form action="payment/update_paimnet.php" method="POST" enctype="multipart/form-data" id="edit-paimentmobile-form">
        <div id="edit-paimentmobile-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Paiment</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="hidden" name="song_paimentmobile_id" id="song_paimentmobile_id"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-mobile-title">phone numero</label>
                    <input type="text" id="edit-mobile-title" name="edit-mobile-numero" class="form-control" placeholder="Enter song title" value="">
                    <div id="song-title-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="edit-mobile-type">Mobile Provider</label>
                    <select id="edit-mobile-type" name="edit-mobile-methode" class="form-control" required>
                        <option value="" disabled selected>Choose mobile provider</option>
                        <option value="ooredoo">Ooredoo</option>
                        <option value="orange">Orange</option>
                        <option value="telecom">Tunisie Télécom</option>
                    </select>
                </div>

                <input type="hidden" name="type_paimentc_id" id="type_mobile"> <!-- Hidden input for song ID -->
                <div class="form-group">
                    <label for="edit-mobile-date">date expiration</label>
                    <input type="text" id="edit-mobile" name="edit-mobile" class="form-control" placeholder="Enter song title" value="">
                </div>
                <input type="hidden" id="edit-type-mobile" name="edit-type-mobile">
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-mobile-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-mobile-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content" style="width: 400px;">
            <div class="modal-header">
                <div class="modal-title">Confirm Delete</div>
                <button class="close-modal">&times;</button>
            </div>
            <p style="margin-bottom: 20px;">Are you sure you want to delete this song? This action cannot be undone.</p>
            <input type="hidden" id="delete-song-id">
            <div class="form-footer">
                <button class="btn btn-secondary" id="cancel-delete">Cancel</button>
                <button class="btn btn-primary" style="background-color: #e74c3c;" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>
    <div id="delete-modal-payment" class="modal">
        <div class="modal-content" style="width: 400px;">
            <div class="modal-header">
                <div class="modal-title">Confirm Delete</div>
                <button class="close-modal">&times;</button>
            </div>
            <p style="margin-bottom: 20px;">Are you sure you want to delete this song? This action cannot be undone.</p>
            <input type="hidden" id="delete-song-id">
            <div class="form-footer">
                <button class="btn btn-secondary" id="cancel-delete">Cancel</button>
                <button class="btn btn-primary" style="background-color: #e74c3c;" id="confirm-delete-payment">Delete</button>
            </div>
        </div>
    </div>
    <div id="delete-modal-news" class="modal">
        <div class="modal-content" style="width: 400px;">
            <div class="modal-header">
                <div class="modal-title">Confirm Delete</div>
                <button class="close-modal">&times;</button>
            </div>
            <p style="margin-bottom: 20px;">Are you sure you want to delete this news? This action cannot be undone.</p>
            <input type="hidden" id="delete-news-id">
            <div class="form-footer">
                <button class="btn btn-secondary" id="cancel-delete">Cancel</button>
                <button class="btn btn-primary" style="background-color: #e74c3c;" id="confirm-delete-news">Delete</button>
            </div>
        </div>
    </div>

    <div id="notification" style="display: none; background-color: #4CAF50; color: white; padding: 15px; margin: 20px; border-radius: 50px; position: relative; left: 150px;">
        <span id="message"></span>
    </div>

    <script>
        const statistics = <?php echo $statisticsJson; ?>;

        if (statistics && statistics.top_songs) {
            const songTitles = statistics.top_songs.map(song => song.song_title);
            const songStreams = statistics.top_songs.map(song => song.total_streams);
            const totalListeners = statistics.total_listeners;

            // Calculate the percentage of listeners for each song
            const songPercentages = songStreams.map(streamCount => (streamCount / totalListeners) * 100);

            // Function to generate dark colors
            function generateDarkColor() {
                const letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    // Restrict the random values to the darker half of the color spectrum (0-7 for RGB)
                    const randomValue = Math.floor(Math.random() * 8); // 0-7 for darker shades
                    color += letters[randomValue];
                }
                return color;
            }

            // Generate dark colors for each song
            const backgroundColor = songTitles.map(() => generateDarkColor());
            const borderColor = backgroundColor.map(color => color); // Same as background for border

            const chartData = {
                labels: songTitles,
                datasets: [{
                    label: 'Stream Count Percentage by Listeners',
                    data: songPercentages,
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    borderWidth: 1
                }]
            };

            const ctx = document.getElementById('stream-chart').getContext('2d');
            const streamChart = new Chart(ctx, {
                type: 'pie', // Changed to 'pie' for a pie chart
                data: chartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: 'white' // Set legend text color to white
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    const percentage = tooltipItem.raw.toFixed(2);
                                    return tooltipItem.label + ': ' + percentage + '%';
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.error("No valid data for chart.");
        }
        document.addEventListener("DOMContentLoaded", function() {
            let message = "<?php echo $message; ?>";
            if (message.trim() !== "") {
                let notification = document.createElement("div");
                notification.innerText = message;
                notification.style.position = "fixed";
                notification.style.top = "10px";
                notification.style.right = "10px";
                notification.style.backgroundColor = "green";
                notification.style.color = "white";
                notification.style.padding = "10px";
                notification.style.borderRadius = "5px";
                document.body.appendChild(notification);

                // Remove notification after 3 seconds
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 4000);
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('artistChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo $labelsJson; ?>,
                    datasets: [{
                        label: 'Total Streams',
                        data: <?php echo $dataJson; ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                        
                            
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        const tabs = document.querySelectorAll(".tab-link");
    const contents = document.querySelectorAll(".tab-content");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            // Supprimer 'active' de tous
            tabs.forEach(t => t.classList.remove("active"));
            contents.forEach(c => c.classList.remove("active"));

            // Ajouter 'active' sur le tab cliqué
            tab.classList.add("active");
            document.getElementById(tab.getAttribute("data-tab")).classList.add("active");
        });
    });
    // Force dashboard tab to be active on page load
    document.querySelector('[data-tab="dashboard"]').classList.add("active");
    document.getElementById("dashboard").classList.add("active");

    

    window.addEventListener("DOMContentLoaded", function () {
        // Optional: If your dashboard tab also has a button or menu item linked to it
        document.querySelector("#dashboard").classList.add("active");

        // Hide other tabs
        document.querySelectorAll(".tab-content").forEach(tab => {
            if (tab.id !== "dashboard") {
                tab.classList.remove("active");
            }
        });
    });


        
    </script>
    <script src="backoffice.js"></script>
    <script src="payment/backpaiement.js"></script>
    <script src="news/news.js"></script>
    <script src="/projetweb/View/tunify_avec_connexion/gamification/public/assets/js/backchahine.js"></script>

</body>
</html>
