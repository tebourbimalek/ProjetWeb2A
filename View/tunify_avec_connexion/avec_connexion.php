<?php 
session_start();

require_once 'C:\xampp\htdocs\projetweb\View\tunify_avec_connexion\music\displaysongs.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php'; 
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\functionpaiments.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\CommentsController.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\NewsController.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\ReactionController.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\functionsnews.php';


$allmusicrand=chansonrand();
$allartiste=allartiste();

$count2=1;







// Get a PDO instance from the config class
$pdo = config::getConnexion();




$user = getUserInfo($pdo);


$user_id = $user->getArtisteId();
$adresse = $user->getEmail();

if (isSubscriptionExpired($pdo, $user_id)){
    $type= 'expired';
}else{
    $type= 'valid';
}

$playlists = getplaylist($user_id);
$userdata = getUserData($user_id);

$imagePathprofile = str_replace('\\', '/', $userdata['image_path']);
// Remove the local absolute path part (e.g. 'C:/xampp/htdocs')
$imagePathprofile = str_replace('C:/xampp/htdocs', '', $imagePathprofile);
// Remove any leading slash if it's present
$imagePathprofile = ltrim($imagePathprofile, '/');
// Construct the final relative URL to the image
$imageURLprofile = "/" . $imagePathprofile;


$unreadCount = countUnreadNotifications($user_id);




if (isset($_GET['playlist_created']) && $_GET['playlist_created'] == 'import') {
    echo '<div class="message-box success" id="flash-message">Playlist créée avec succès</div>';
}
// Check if there's an error message
if (isset($_SESSION['error'])) {
    echo '<div class="message-box error" id="flash-message">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']); // Clear the error message after displaying
}


// Display success message if playlist was created
if (isset($_GET['playlist_created']) && $_GET['playlist_created'] == 'true') {
    echo '<div class="message-box success" id="flash-message">Playlist importé avec succès</div>';
}


// Check if the session variable for the comment message is set







if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['playlist_id'])) {

    $playlistId = (int) $_POST['playlist_id'];

                
    // Fetch playlist songs and random songs
    $playlist_songs = fetchSongsFromPlaylist($playlistId);   
    $random_songs = executeFetchRandomSongs($playlistId, 10);
    $fetchSongsFromArtiste=fetchSongsFromArtiste($playlistId);
    // Start captu
    // 
    // 
    // ring the playlist songs HTML
    ob_start();

    if ($playlist_songs) {
        echo "<table style='width:100%; border-collapse:collapse; color:white;'>";
        echo "<thead>
                <tr style='color:gray;'>
                    <th style='padding:10px; text-align:left;'>#</th>
                    <th style='padding:10px; text-align:left;'>Title</th>
                    <th style='padding:10px; text-align:left;'>Artist</th>
                    <th style='padding:10px; text-align:left;'></th>
                    <th style='padding:10px; text-align:left;'><i class='fa-solid fa-clock'></i></th>
                    <th style='padding:10px; text-align:left;'><i class='fa-solid fa-gear'></i></th>
                </tr>

            <tr><td colspan='6' style='border-bottom: 2px solid #ccc; padding: 0;'></td></tr>;
            </thead>";

        echo "<tbody>";

        $count = 1;
        foreach ($playlist_songs as $song) {
            $imagePath = str_replace(['C:/xampp/htdocs/projetweb', '\\'], ['', '/'], $song['image_path']);
            $imagePath = ltrim($imagePath, '/');
            $imageURL = "/projetweb/" . $imagePath;


            $music_path = str_replace(['C:/xampp/htdocs/projetweb', '\\'], ['', '/'], $song['music_path']);
            $music_path = ltrim($music_path, '/');
            $musicURL = "/projetweb/" . $music_path;
        
                echo "<tr
                class='song-row'
                data-song-id='" . (int)$song['id'] . "' 
                data-song-title='" . htmlspecialchars($song['song_title']) . "' 
                data-song-url='" . htmlspecialchars($musicURL) . "' 
                data-song-cover='" . htmlspecialchars($imageURL) . "' 
                data-song-artiste='" . htmlspecialchars($song['album_name']) . "' 
                onmouseover=\"this.querySelector('.song-number').innerHTML='<i class=&quot;fa-solid fa-play&quot; style=&quot;font-size:13px;&quot;></i>';\" 
                onmouseout=\"this.querySelector('.song-number').innerHTML=this.querySelector('.song-number').dataset.number;\"
                onclick='playSongplaylist(this);'>
        

                <td style='padding:10px;'>
                    <span class='song-number' data-number='{$count}'>{$count}</span>
                </td>
                <td style='padding:10px; display:flex; align-items:center;'>
                    <img src='" . htmlspecialchars($imageURL) . "' style='width:50px;height:50px;margin-right:10px;'>
                    <span class='song-title'>" . htmlspecialchars($song['song_title']) . "</span>
                </td>
                <td style='padding:10px;'>" . htmlspecialchars($song['album_name']) . "</td>
                <td style='padding:10px; text-align:center;'>
                    <button style='background:none;border:none;cursor:pointer;'>
                        <i class='fa-solid fa-circle-check plus-icon'></i>
                    </button>
                </td>
                <td style='padding:10px;'>
                    <span>" . htmlspecialchars($song['duree']) . "</span>
                </td>
                <td>
                    <button
                        class='deleteSongButton'
                        data-song-id='" . (int)$song['id'] . "'
                        style='background: transparent;border: 2px solid white;color: white;border-radius: 50%;width: 40px;height: 40px;cursor: pointer;transition: background 0.2s, color 0.2s;'
                        onmouseover=\"this.style.background='white'; this.style.color='black';\"
                        onmouseout=\"this.style.background='transparent'; this.style.color='white';\">
                        <i class='fa-solid fa-trash'></i>
                    </button>
                </td>
            </tr>";

            $count++;

        }
                

        echo "</tbody>";
        echo "</table>";
    }
    if ($fetchSongsFromArtiste) {
        echo "<table style='width:100%; border-collapse:collapse; color:white;'>";
        echo "<thead>
                <tr style='color:gray;'>
                    <th style='padding:10px; text-align:left;'>#</th>
                    <th style='padding:10px; text-align:left;'>Title</th>
                    <th style='padding:10px; text-align:left;'>Artist</th>
                    <th style='padding:10px; text-align:left;'></th>
                    <th style='padding:10px; text-align:left;'><i class='fa-solid fa-clock'></i></th>
                    <th style='padding:10px; text-align:left;'><i class='fa-solid fa-gear'></i></th>
                </tr>

            <tr><td colspan='6' style='border-bottom: 2px solid #ccc; padding: 0;'></td></tr>;
            </thead>";

        echo "<tbody>";

        $count = 1;
        foreach ($fetchSongsFromArtiste as $song) {
            $imagePath = str_replace(['C:/xampp/htdocs/projetweb', '\\'], ['', '/'], $song['image_path']);
            $imagePath = ltrim($imagePath, '/');
            $imageURL = "/projetweb/" . $imagePath;


            $music_path = str_replace(['C:/xampp/htdocs/projetweb', '\\'], ['', '/'], $song['music_path']);
            $music_path = ltrim($music_path, '/');
            $musicURL = "/projetweb/" . $music_path;
        
                echo "<tr
                class='song-row'
                data-song-id='" . (int)$song['id'] . "' 
                data-song-title='" . htmlspecialchars($song['song_title']) . "' 
                data-song-url='" . htmlspecialchars($musicURL) . "' 
                data-song-cover='" . htmlspecialchars($imageURL) . "' 
                data-song-artiste='" . htmlspecialchars($song['album_name']) . "' 
                onmouseover=\"this.querySelector('.song-number').innerHTML='<i class=&quot;fa-solid fa-play&quot; style=&quot;font-size:13px;&quot;></i>';\" 
                onmouseout=\"this.querySelector('.song-number').innerHTML=this.querySelector('.song-number').dataset.number;\"
                onclick='playSongplaylist(this);'>
        

                <td style='padding:10px;'>
                    <span class='song-number' data-number='{$count}'>{$count}</span>
                </td>
                <td style='padding:10px; display:flex; align-items:center;'>
                    <img src='" . htmlspecialchars($imageURL) . "' style='width:50px;height:50px;margin-right:10px;'>
                    <span class='song-title'>" . htmlspecialchars($song['song_title']) . "</span>
                </td>
                <td style='padding:10px;'>" . htmlspecialchars($song['album_name']) . "</td>
                <td style='padding:10px; text-align:center;'>
                    <button style='background:none;border:none;cursor:pointer;'>
                        <i class='fa-solid fa-circle-check plus-icon'></i>
                    </button>
                </td>
                <td style='padding:10px;'>
                    <span>" . htmlspecialchars($song['duree']) . "</span>
                </td>
                <td>
                    <button
                        class='deleteSongButton'
                        data-song-id='" . (int)$song['id'] . "'
                        style='background: transparent;border: 2px solid white;color: white;border-radius: 50%;width: 40px;height: 40px;cursor: pointer;transition: background 0.2s, color 0.2s;'
                        onmouseover=\"this.style.background='white'; this.style.color='black';\"
                        onmouseout=\"this.style.background='transparent'; this.style.color='white';\">
                        <i class='fa-solid fa-trash'></i>
                    </button>
                </td>
            </tr>";

            $count++;

        }
                

        echo "</tbody>";
        echo "</table>";
    }
    $playlistHtml = ob_get_clean();

    // Start capturing the random songs recommendations HTML
    ob_start();

    if (!empty($random_songs)) {
        echo "<table style='width:100%; border-collapse:collapse; color:white;'>";
        echo "<tbody>";

        foreach ($random_songs as $song) {
            $imagePath = str_replace(['C:/xampp/htdocs/projetweb', '\\'], ['', '/'], $song['image_path']);
            $imagePath = ltrim($imagePath, '/');
            $imageURL = "/projetweb/" . $imagePath;

            echo "<tr>
                <td style='padding:10px; display:flex; align-items:center;'>
                    <img src='" . htmlspecialchars($imageURL) . "' style='width:50px;height:50px;margin-right:10px;'>
                    " . htmlspecialchars($song['song_title']) . "
                </td>
                <td style='padding:10px;'>" . htmlspecialchars($song['album_name']) . "</td>
                <td style='padding:10px; text-align:end;'>
                    <button 
                        class='addSongButton' 
                        data-song-id='" . (int) $song['id'] . "'
                        style='
                            background: transparent;
                            border: 2px solid white;
                            color: white;
                            border-radius: 30px;
                            width: 120px;
                            height: 50px;
                            cursor: pointer;
                            font-size: 15px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0px -97px 0px 0px;
                        '
                    >Ajouter</button>
                </td>
            </tr>";

        }

        echo "</tbody>";
        echo "</table>";
    }
    $recommendationsHtml = ob_get_clean();

    header('Content-Type: application/json');

    $response = [
        'playlist' => $playlistHtml
    ];
    
    if ($playlist_songs) {
        $response['recommendations'] = $recommendationsHtml;
    }
    
    echo json_encode($response);
    exit;
}



try {
    $pdo = config::getConnexion();
    


    $userConnected = getUserInfo($pdo);
    $user_role = $userConnected->getTypeUtilisateur();
    
    
} catch (PDOException $e) {
    // Gestion d'erreurs propre
    die("Erreur de base de données : " . $e->getMessage());
} catch (Exception $e) {
    // Pour les erreurs de checkIfAdmin
    die($e->getMessage());
}

if (isset($_SESSION['comment_message'])) {
    echo '<div class="notification ' . $_SESSION['comment_type'] . '">';
    echo $_SESSION['comment_message'];
    echo '</div>';

    unset($_SESSION['comment_message'], $_SESSION['comment_type']);
}

if (isset($_SESSION['comment_message_delete'])) {
    echo '<div class="notification ' . $_SESSION['comment_type_delete'] . '">';
    echo $_SESSION['comment_message_delete'];
    echo '</div>';

    unset($_SESSION['comment_message_delete'], $_SESSION['comment_type_delete']);
}




requireLogin();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunify</title>
    <script src="https://kit.fontawesome.com/d4610e21c1.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <link rel="icon" href="/projetweb/View/tunifypaiement/image/logo1.png" type="image/png">
    <link rel="stylesheet" href="css.css">
    <style>
        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            z-index: 9999;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideIn 0.4s ease-out, fadeOut 4s 4s forwards;
            font-family: Arial, sans-serif;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .warning {
            background-color: #fff3cd;
            color: #856404;
        }

        @keyframes slideIn {
            from {
                top: -100px;
                opacity: 0;
            }
            to {
                top: 20px;
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                top: -100px;
            }
        }
        #offline-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background-color: rgb(34, 34, 34);
            color: white;
            font-size: 24px;
            text-align: center;
            padding-top: 20%;
            z-index: 9999;
        }
        #playlist_info {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            width: 100%;
            cursor: pointer;
        }
        
        .message-box {
            position: fixed;
            bottom: 140px; /* Adjust this so it sits just above your music box */
            left: 50%;
            transform: translateX(-50%);
            background-color: #ffffff;
            color: #333;
            padding: 15px;
            border-radius: 5px;
            margin: 5px 0;
            font-size: 17px;
            width: 220px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000; /* make sure it's above other content */*
            transition: all 0.3s ease-in-out;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        #box2-expanded3 {
            display: none; /* Hide the expanded section initially */
        }
        .playlist-header {
            margin-bottom: 20px;
        }
        
        .playlist-type {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .playlist-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .playlist-title sup {
            font-size: 12px;
            vertical-align: super;
        }
        
        .playlist-owner {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }
        
        .section-title {
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .search-container {
            position: relative;
            margin-bottom: 20px;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
        }
        
        .search-input:focus {
            border-color: #1db954;
        }
        
        .track-list {
            list-style: none;
        }
        
        .track-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.2s;
        }
        
        .track-item:hover {
            background-color: #eee;
        }
        
        .track-number {
            width: 30px;
            text-align: center;
            color: #666;
        }
        
        .track-info {
            flex: 1;
            margin-left: 15px;
        }
        
        .track-title {
            font-weight: 500;
        }
        
        .track-artist {
            font-size: 14px;
            color: #666;
        }
        
        .track-duration {
            color: #666;
            margin-right: 15px;
        }
        
        .track-actions {
            opacity: 0;
            transition: opacity 0.2s;
        }
        
        .track-item:hover .track-actions {
            opacity: 1;
        }
        
        .action-btn {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            margin-left: 10px;
            font-size: 16px;
        }
        
        .action-btn:hover {
            color: #1db954;
        }
        .photo-upload {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            }

            .photo-upload:hover {
            opacity: 1;
            }

            .upload-content {
            color: white;
            font-size: 14px;
            text-align: center;
            }

            .upload-content i {
            font-size: 24px;
            margin-bottom: 8px;
            }
            .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.7);
            }

            .modal-content {
            background-color: #2b2b2b;
            margin: 8% auto;
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            color: white;
            position: relative;
            }

            .close {
            position: absolute;
            top: 12px;
            right: 16px;
            font-size: 26px;
            cursor: pointer;
            }

            .modal-body {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            }

            .left-side .upload-box {
            background-color: #3e3e3e;
            width: 190px;
            height: 190px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            }

            .right-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
            }

            .right-side input,
            .right-side textarea {
            background-color: #3e3e3e;
            border: none;
            border-radius: 4px;
            padding: 10px;
            color: white;
            }

            .right-side textarea {
            resize: none;
            height: 140px;
            }

            .save-btn {
            background-color: white;
            color: black;
            border: none;
            padding: 10px;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            }

            .disclaimer {
            font-size: 11px;
            margin-top: 15px;
            color: #b3b3b3;
            }

            #preloader {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background-color: #222;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      transition: opacity 0.5s ease;
    }

    /* Dots container */
    .dots {
      display: flex;
      gap: 12px;
    }

    /* Individual dot */
    .dot {
      width: 16px;
      height: 16px;
      background-color: #00ff00; /* Green color */
      border-radius: 50%;
      animation: bounce 1.5s infinite;
    }

    /* Delay animations for each dot */
    .dot:nth-child(1) {
      animation-delay: 0s;
    }

    .dot:nth-child(2) {
      animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
      animation-delay: 0.4s;
    }

    /* Bounce animation */
    @keyframes bounce {
      0%, 80%, 100% {
        transform: translateY(0);
      }
      40% {
        transform: translateY(-15px);
      }
    }

    /* Hide main content initially */
    #main-content {
      display: none;
    }
    </style>
    
</head>
<body>  
    <div id="preloader">
        <div class="dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
<div id="main-content">
    <div class="background"></div>    
    <!-- Modal overlay -->
    <nav class="navbar">
        <div class="left-section">
            <img src="/projetweb/View/tunifypaiement/image/logo1.png" alt="Logo" class="" width="200px" height="60px" style="margin : 0px 0px 0px 0px;"  >
            <div class="icon-container">
                <div class="icon-house">
                    <a href="/projetweb/View/tunify_avec_connexion/avec_connexion.php"><i class="fa-solid fa-house" style="color: grey;font-size:20px;"></i></a>
                </div>
                <span class="tooltip">Accueil</span>
            </div>
            <div class="search-bar">
                <div class="icon-container">
                    <button class="icon-searsh" id="searchBtn"><i class="fa-solid fa-magnifying-glass" style="color: grey;font-size:20px;"></i></button>
                    <span class="tooltip">Rechercher</span>
                </div>
                <input type="text" id="global_search" placeholder="Que souhaitez-vous écouter ou regarder ?" style="width: 360px;">
                <br><br>
                <span class="divider" >|</span>
                <div class="icon-container">
                    <button class="icon-searsh"><i class="fa-regular fa-bookmark" style="color: grey;font-size:20px;"></i></button>
                    <span class="tooltip">parcourir</span>
                </div>  
            </div>
        </div>
        
        <style>
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 20px;
        color: #fff;
        padding: 10px;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: #222;
        min-width: 220px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.4);
        z-index: 999;
        overflow: hidden;
    }

    .dropdown-menu a {
        color: #fff;
        padding: 16px;
        text-decoration: none;
        display: block;
        font-weight: 500;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .dropdown-menu a:hover {
        background-color: rgba(255,255,255,0.05);
    }

    .external-link {
        float: right;
        opacity: 0.7;
    }

    .show {
        display: block;
    }
    .notification-icon {
        position: relative;
        display: inline-block;
    }

    .notification-count {
        position: absolute;
        top: -10px;
        right: -8px;
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }
    .notification-count1 {
        position: absolute;
        top: 14px;
        right: 50%;
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }
    .notification-icon {
    position: relative;
}

</style>
        <div class="right-section">
            <div class="notification-icon" >
                <i class="fa-solid fa-bell" style="color: #b3b3b3; font-size: 20px;"></i>
                <span class="notification-badge" style="text-align: center; font-size:15px;">0</span>
                <div class="notification-dropdown">
                    <div class="notification-header">Notifications</div>
                    <div class="notification-list">
                        <div class="notification-empty">Aucune notification</div>
                    </div>
                </div>
            </div>
            <a href="gamification/frontoffice.php" class="mot">|Gaming</a>
            <?php
                if ($type == 'expired') {
                    echo '<a href="reclamation/front/front.php" class="mot">Reclamation</a>';
                    echo '<a href="../tunifypaiement/dashboard.php" class="mot">Premium|</a>';
                }else{
                    echo '<a href="reclamation/front/front.php" class="mot">Reclamation|</a>';
                }
            ?>

            
            <?php if (isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <button onclick="toggleDropdown()" class="dropdown-button">
                        <i class="fas fa-user"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-count"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu">
                        <a href="user/overview.php" target="_blank" onclick="reloadPage(); return false;">Account 
                            <i class="fas fa-external-link-alt external-link"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notification-count1"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>

                        <script>
                            function reloadPage() {
                                // Open overview.php in a new tab
                                window.open('user/overview.php', '_blank');

                                // Reload the current page (avec_connexion.php)
                                location.reload();
                            }
                        </script>
                        <?php 
                            $imageprofile = $userdata['nom_utilisateur'];                        
                        ?>
                        <a href="#" 
                            onclick="toggleBox4(<?= $userdata['artiste_id']; ?>, '<?= $userdata['nom_utilisateur']; ?>', '<?= $imageURLprofile ?>')" 
                            style="border:none;">
                            Profile
                        </a>

                        <?php
                            if ($type == 'expired') {
                                echo '<a href="#premium">Upgrade to Premium <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <?php
                            if ($user_role == 'admin' || $user_role == 'artiste') {
                                echo '<a href="../backoffice/backoffice.php">Dashboard <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <a href="#support">Support <i class="fas fa-external-link-alt external-link"></i></a>

                        <?php if ($user_role == 'admin' || $user_role == 'artiste') { ?>
                            <a href="reclamation/back/back.php">View Reclamations <i class="fas fa-external-link-alt external-link"></i></a>
                        <?php } ?>

                        <a href="#" onclick="showSettingsSection(); return false;">Settings</a>
                        <a href="logout.php">Log out</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/projetweb/View/tunisfy_sans_conexion/login.php" class="nav-link">Se connecter</a>
                <a href="/projetweb/View/tunisfy_sans_conexion/register.php" class="nav-link">S'inscrire</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="main-content">
        <div>
            <div class="sidebar box-1">
                <p style="font-size: 18px; padding: 10px;">Bibliothéque</p>
                
                <div class="icon-container" style="position:relative; left: 45px; top: 0px;">
                    <button class="icon-plus"><i class="fa-regular fa-plus" style="color: grey;font-size:20px;"></i></button>
                    <span class="tooltip1">Créer une playlist ou un dossier</span>
                </div>
                
            </div>
            <a href="music/add_playlist.php" class="create-playlist" style="text-decoration: none; color: white;">
                <div class="create-options-modal" style="display: none;">
                    <div class="create-option">
                        <i class="fa-solid fa-music fa-2xl" style="font-size:30px;"></i>
                        <div>
                            <h3 style="font-size:25px;">Playlist</h3>
                            <p>Créez une playlist de titres </p>
                            <hr>
                        </div>
                    </div>
                </div>
            </a>
            <?php 
            if ((count($playlists) > 0)) {
                echo '<div id="option2"class="playlist-card" style="transition: background-color 0.3s ease; max-height: 380px; height: 100%;">';
                    echo '<div style="margin: 0px 5px 0px 0px; display: flex; align-items: center;">';  // Added flexbox
                        echo '<i class="fa-solid fa-magnifying-glass fa-lg" style="color:rgba(172, 172, 172, 0.74); margin-right: 10px;"></i>';  // Added margin for spacing
                        echo '<input type="text" id="search-playlist" placeholder="Rechercher une playlist" style="width: 300px; background-color: transparent; border: none; font-size: 16px; margin-left: 10px; color:white;">';
                    echo "</div>";  // Closing div for flexbox
                    echo "<br><br>";

                        if (hasLikedSongs($user_id)) {
                            echo '<div class="playlist-item" style="display: flex; align-items: center;">';
                                echo '<div class="playlist-icon" style="display: flex; align-items: center;">';
                                    echo '<div style="background: linear-gradient(135deg, #6A11CB, #2575FC); width: 70px; height: 60px; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">';
                                        echo '<i class="fa-solid fa-heart" style="color: white; font-size: 15px;"></i>';
                                    echo '</div>';
                                    
                                    // Hidden input for playlist name
                                    echo '<input class="img_url" type="hidden" name="playlist_name" value="vide">';
                                    
                                    // Form for submitting the playlist
                                    
                                    echo '<button 
                                        id="likedSongsButton"
                                        type="submit"
                                        onclick="toggleBox3(\'\', \'Liked Songs\', \'\'); handleButtonClick(\'likedSongs\')"
                                        style="background:none;border:none;color:white;font-size:16px;cursor:pointer;">
                                        ' . htmlspecialchars("Liked Songs") . '
                                    </button>';

                                   

                                    // Hidden input for playlist ID (if necessary)
                                    echo '<input type="hidden" name="playlist_id" value="12345">'; // Replace "12345" with actual ID
                                echo '</div>';
                            echo '</div>';
                            echo '<br>';
                        }


                        foreach (array_slice($playlists, 0, 3) as $playlist) {
                            echo '<div class="playlist-item" style="display: flex; align-items: center;">';
                                echo '<div class="playlist-icon" style="display: flex; align-items: center;">';
                                
                                if (!empty($playlist['img'])) {
                                    $imagePath = str_replace('\\', '/', $playlist['img']);
                                    $imagePath = str_replace('C:/xampp/htdocs', '', $imagePath);
                                    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Playlist Image" style="width: 70px; height: 60px; border-radius: 5px; margin-right: 10px;">';
                                    echo '<input class="img_url" type="hidden" name="playlist_name" value="' . htmlspecialchars($imagePath) . '">';
                                } else {
                                    echo '<div style="background-color: rgb(62, 62, 62); width: 70px; height: 45px; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">';
                                        echo '<i class="fa-solid fa-music fa-lg" style="color: white; font-size: 15px;"></i>';
                                    echo '</div>';
                                    echo '<input class="img_url" type="hidden" name="playlist_name" value="vide">';
                                }
                                
                                $cleanImg = str_replace('\\', '/', $playlist['img']);
                                $cleanImg = str_replace('C:/xampp/htdocs', '', $cleanImg);
                                echo '<button class="buttonplaylist" '
                                . 'onclick="toggleBox3('
                                    . htmlspecialchars($playlist['id']) . ', '
                                    . '\'' . htmlspecialchars(addslashes($playlist['nom'])) . '\', '
                                    . '\'' . htmlspecialchars($cleanImg) . '\')" '
                                . 'oncontextmenu="openPlaylistContextMenu(event, '
                                    . htmlspecialchars($playlist['id']) . ')" '
                                . 'id="playlist_info_' . htmlspecialchars($playlist['id']) . '" '
                                . 'style="background:none;border:none;color:white;font-size:16px;cursor:pointer;">'
                                . htmlspecialchars($playlist['nom'])
                                . '</button>';



                                echo '<input id="edit_id" type="hidden" name="playlist_id" value="' . htmlspecialchars($playlist['id']) . '">';

                                echo '</div>';
                            echo '</div>';
                            echo '<br>';
                        }?>

                        <div id="playlistContextMenu" class="context-menu">
                            <ul>
                                <form method="POST" action="music\delete.php" style="display:inline;">
                                    <!-- Hidden input for the playlist ID -->
                                    <input type="hidden" name="id_delete" id="delete_id" value="">

                                    <li data-action="remove-profile" style="list-style:none;">
                                        <!-- Submit button for deletion -->
                                        <button type="submit" 
                                                style="background:none; border:none; color:inherit; font:inherit; cursor:pointer; display:flex; align-items:center;">
                                                <i class="fa-solid fa-trash"></i> Supprimer du profil
                                        </button>
                                    </li>
                                </form>
                                <li class="sep"></li>
                                <a href="music\add_playlist.php" style="text-decoration: none;color:white;"><li data-action="new-playlist"><i class="fa-solid fa-music"></i> Créer une playlist</li></a>
                        </div>
                    <?php
                    echo '</div>';
            }else{
                ?>
                <div class="playlist-card">
                    <h1 class="title">Créez votre première playlist</h1>
                    <p class="subtitle">C'est simple, nous allons vous aider</p>
                    <form action="music\add_playlist.php" method="post" style="display:inline;">
                        <button type="submit" class="create-button" onclick="toggleBox3()">Créer une playlist</button>
                    </form>

                </div>
                <div class="card1">
                <div>
                    <span class="c">Légal
                    <span class="c" style="margin-left: 10px;">Centre de sécurité et de confidentialité</span> 
                </div>
                <br>
                <div>
                    <span class="c">À propos des annonces
                    <span class="c" style="margin-left: 10px;">cookies</span> 
                    <span class="c" style="margin-left: 15px;">À propos des pubs</span>
                </div>
                <br>
                <div>
                    <span class="c">Accessibilité</span>
                </div>
                <br>
                <div>
                    <button class="pays">
                        <i class="fa-solid fa-globe" style="color: white;font-size:14px;"></i>
                        <span class="" style="font-size: 14px; color: white;">Tunisia</span>
                    </button>
                </div>
            </div>
                <?php
            }
            
            ?>
        </div>
        <div class="box-2" id="box2-expanded3"  style="display:none;">
            <div id="playlist_div">
            <input type="hidden" id="playlist_click_id">
            <div id="box_liked_song" class="playlist-container" style="display: flex; flex-direction: row; align-items: center; gap: 20px; padding: 20px;">
                <button id="button_artiste" style="border:none; background-color: transparent; cursor: pointer;" onclick="bocouvrir()">
                    <div  id="box_img_song" style=" width: 200px; height: 200px; border-radius: 5px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                        <img src="" alt="" id="image_playlist" style="width: 200px; height: 200px; object-fit: cover; border-radius: 5px; display: none;">
                        <i id="iconn"class="fa-solid fa-music fa-lg" style="color: white; font-size: 35px;"></i>
                        
                        <div class="photo-upload">
                            <div class="upload-content">
                                <i class="fa fa-pencil" style="color:white; font-size:15px;"></i>
                                <span>Sélectionner une photo</span>
                            </div>
                        </div>

                    </div>
                </button>
                <form action="update-playlist.php" method="post" enctype="multipart/form-data" id="playlistForm" onsubmit="return validateForm()">
                    <div id="playlistModal" class="modal">
                        <div class="modal-content">
                            <span class="close" onclick="bocfermer()">&times;</span>
                            <h2>Modifier les informations</h2>
                            
                            <div class="modal-body">
                                <div class="left-side">
                                    <div class="upload-box" 
                                        style="background-color: #3e3e3e; width: 190px; height: 190px; border-radius: 5px; display: flex; align-items: center; justify-content: center; cursor: pointer;" 
                                        onclick="document.getElementById('fileInput').click();">
                                        <input type="file" id="fileInput" name="cover_image" accept="image/*" style="display: none;" onchange="handleFileChange(event)">
                                        <!-- Icon will be replaced by the selected image -->
                                        <i id="defaultIcon" class="fa-solid fa-music fa-2xl" style="color: white;"></i>
                                        <img id="previewImage" src="" alt="Image Preview" style="display: none; width: 190px; height: 190px; object-fit: cover; border-radius: 5px;">
                                        <input type="hidden" id="id_playlist" name="id_playlist">
                                    </div>
                                </div>
                                <div class="right-side">
                                    <input type="text" placeholder="Ma playlist n°1" name="nom_playlist">
                                    <textarea placeholder="Ajoutez une description facultative" name="description_playlist"></textarea>
                                    <button class="save-btn" onsubmit="return validateForm();">Sauvegarder</button>
                                </div>
                            </div>

                            <p class="disclaimer">
                                En continuant, vous accordez les droits de l'image que vous décidez d'importer. Vérifiez bien que vous avez le droit d'importer cette image.
                            </p>
                        </div>
                    </div>
                </form>


                <div class="playlist-header">
                    <div class="playlist-type" style="color:white;">Playlist publique</div>
                    <h1 class="playlist-title" style="font-size: 50px;color:white;">Ma playlist 1</h1>
                    <div class="playlist-owner" style="color:white;">tebourbimalek</div>
                </div>
                
                <hr>
            </div>
            <br>
            <div id="buttons">
                <button id="playPauseButton" style="border:none;">
                    <i class="fa-solid fa-circle-play" style="color:black; background-color:green; border-radius:50%; padding:10px; font-size:30px;"></i>
                </button>
                <!-- Share button -->
                <button class="share-button" id="shareButton">
                    <i class="fa-solid fa-share"></i>
                </button>

                <!-- Share box (Initially hidden) -->
                <div id="shareBox" style="display: none;">
                    <input type="text" id="shareLinkInput" readonly />
                    <button id="copyButton">Copy</button>
                    <input type="hidden" value="<?php echo $user_id; ?>" id="id_user">
                    <div id="result"></div>
                </div>
                <?php
                    if ($type == 'valid') {
                        echo '<button class="download-button">
                                <span class="progress-ring"></span>
                                <i class="fa-solid fa-down-long"></i>
                            </button>';
                    }
                ?>

                
                




                <button class="ellipsis-button">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>


            </div>
            <div id="playlist_song" style="display:block;">
            </div>
            
            <div id="historique_song" style="display:none;">
            </div>
            <div id="log_song" style="display:none;">
            </div>

            <div id="playlistdiv" style="display:none;">
            </div>  
            
            
            <div id="news" style="display:none;">
                
            </div>


            <div id ="likedsongs" style="display:none;">
                <div>
                <?php 
                
                $likedSongs = fetchLikedSongs($user_id);
                $likedSongIds = fetchLikedSongIds($user_id);
            if ($likedSongs) {
                echo "<style>
                    table tr:hover {
                        background-color: #282828;
                    }
                    .plus-icon {
                        color: transparent;
                        transition: color 0.3s ease;
                    }
                    tr:hover .plus-icon {
                        color: #1DB954; /* Spotify green */
                    }
                </style>";
            
                echo "<table style='width: 100%; border-collapse: collapse;'>";
                echo "<thead>";
                echo "<tr style='color: gray;'>";
                echo "<th style='padding: 10px; text-align: left;'>#</th>";
                echo "<th style='padding: 10px; text-align: left;'>Title</th>";
                echo "<th style='padding: 10px; text-align: left;'>Artist</th>";
                echo "<th style='padding: 10px; text-align: left;'></th>";
                echo '<th style="padding: 10px; text-align: left;"><i class="fa-solid fa-clock"></i></th>';
                echo '<th style="padding: 10px; text-align: left;">Action</th>';
                echo "</tr>";
                echo "</thead>";
            
                // Horizontal line below header
                echo "<tr><td colspan='6' style='border-bottom: 2px solid #ccc; padding: 0;'></td></tr>";
            
                echo "<tbody>";
                $count2 = 1;
                foreach ($likedSongs as $song) {
                    // Clean up image path
                    $imagePath = str_replace(['C:/xampp/htdocs/projetweb', '\\'], ['', '/'], $song['image_path']);
                    $imagePath = ltrim($imagePath, '/');
                    $imageURL = "/projetweb/" . $imagePath;


                    $music_path = str_replace(['C:/xampp/htdocs/projetweb', '\\'], ['', '/'], $song['music_path']);
                    $music_path = ltrim($music_path, '/');
                    $musicURL = "/projetweb/" . $music_path;
        
                    echo "<tr
                        style='color:white'
                        class='song-row'.
                        data-song-id='" . (int)$song['id'] . "' 
                        data-song-title='" . htmlspecialchars($song['song_title']) . "' 
                        data-song-url='" . htmlspecialchars($musicURL) . "' 
                        data-song-cover='" . htmlspecialchars($imageURL) . "' 
                        data-song-artiste='" . htmlspecialchars($song['album_name']) . "' 
                        onmouseover=\"this.querySelector('.song-number').innerHTML='<i class=&quot;fa-solid fa-play&quot; style=&quot;font-size:13px;&quot;></i>';\" 
                        onmouseout=\"this.querySelector('.song-number').innerHTML=this.querySelector('.song-number').dataset.number;\"
                        onclick='playSongplaylist(this);'>";
                
                    // Song number with proper class
                    echo "<td style='padding: 10px;' class='song-number' data-number='" . $count2 . "'>" . $count2++ . "</td>";
                
                    // Song image + title
                    echo "<td style='padding: 10px; display: flex; align-items: center;'>";
                    echo "<img src='" . htmlspecialchars($imageURL) . "' alt='Song Image' style='width: 50px; height: 50px; margin-right: 10px;'>";
                    echo "<span>" . htmlspecialchars($song['song_title']) . "</span>";
                    echo "</td>";
                
                    // Album/artist
                    echo "<td style='padding: 10px;'>" . htmlspecialchars($song['album_name']) . "</td>";
                
                    // Plus icon button
                    echo "<td style='padding: 10px; text-align: center;'>";
                    echo "<button style='background: none; border: none; cursor: pointer;'>";
                    echo "<i class='fa-solid fa-circle-check plus-icon'></i>";
                    echo "</button>";
                    echo "</td>";
                
                    // Duration
                    echo "<td style='padding: 10px;'>" . htmlspecialchars($song['duree']) . "</td>";

                    echo "<td>
                        <button
                            class='deleteSongButton'
                            data-song-id='" . (int)$song['id'] . "'
                            data-user-id='" . (int)$user_id . "'
                            onclick='delete_from_liked_song(" . (int)$song['id'] . ", " . (int)$user_id . ")'
                            style='background: transparent; border: 2px solid white; color: white; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; transition: background 0.2s, color 0.2s;'
                            onmouseover=\"this.style.background='white'; this.style.color='black';\"
                            onmouseout=\"this.style.background='transparent'; this.style.color='white';\">
                            <i class='fa-solid fa-trash'></i>
                        </button>
                    </td>";


                
                    echo "</tr>";
                }
                
                echo "</tbody>";
                echo "</table>";
                echo "<br>";
                echo "<br>";
            } else {
                echo "No liked songs found.";
            }
            ?>            
            </div>
            </div>
            <br><br><br><br><br><br>
                <div id="box_recomandé">
                    <div class="section-title" style="color:white;font-size:25px;">Recommandés</div>
                    <div style="font-size:15px; color:gray;">Basé sur vos écoutes</div>
                    <br><br>
                    <div id="recomanded_song" style="display:block;">
                    </div>
                </div>
            </div> 
            <div id="resultsDiv" style="display:none;">
            </div>   
           
            
                <div class="societer">
                    <div class="societer-content">
                        <div class="societer-section">
                            <h3>Société</h3>
                            <ul class="societer-links">
                                <li><a href="#">À propos</a></li>
                                <li><a href="#">Offres d'emploi</a></li>
                                <li><a href="#">For the Record</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Communautés</h3>
                            <ul class="societer-links">
                                <li><a href="#">Espace artistes</a></li>
                                <li><a href="#">Développeurs</a></li>
                                <li><a href="#">Campagnes publicitaires</a></li>
                                <li><a href="#">Investisseurs</a></li>
                                <li><a href="#">Fournisseurs</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Liens utiles</h3>
                            <ul class="societer-links">
                                <li><a href="#">Assistance</a></li>
                                <li><a href="#">Appli mobile gratuite</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Abonnements Spotify</h3>
                            <ul class="societer-links">
                                <li><a href="#">Premium Personnel</a></li>
                                <li><a href="#">Premium Duo</a></li>
                                <li><a href="#">Premium Famille</a></li>
                                <li><a href="#">Spotify Free</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-social">
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                </svg>
                            </a>
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="societer-bottom" style="color : white;">
                        <p>© 2025 Tunify AB</p>
                    </div>
                </footer>
                </div>
            </div>
         <!-- Expanded box for larger screens -->
        <div class="box-2" id="box2-expanded">
                <div class="section_title">
                    <span id="tendance">Artiste recommandés pour vous</span>
                </div>  
                <div class="mourab3a_kol" >
                        <?php foreach (array_slice($allartiste, 0, 30) as $artiste): ?>
                            <div class="album-item">
                                <?php
                                    $base_path = "/projetweb/assets/includes/";
                                    $image_path = str_replace("C:\\xampp\\htdocs", "", $artiste['image_path']); // Remove C:\xampp\htdocs
                                    $image_path = str_replace("\\", "/", $image_path); // Replace backslashes with forward slashes
                                    
                                ?>
                                <div class="artiste-img-container">
                                    <img src="<?php  echo $image_path?>" alt="Cover" class="cover-img" >
                                    <div class="start-icon" >
                                        <button class="showModalBtn" style="border:none; background-color:transparent"><i class="fas fa-play"></i></button> <!-- Font Awesome play icon -->
                                    </div>
                                </div>
                                <div class="album-info">
                                    <h3><?= htmlspecialchars($artiste['nom_utilisateur']) ?></h3>
                                    <p>Artiste</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                </div>
                <div class="societer">
                    <div class="societer-content">
                        <div class="societer-section">
                            <h3>Société</h3>
                            <ul class="societer-links">
                                <li><a href="#">À propos</a></li>
                                <li><a href="#">Offres d'emploi</a></li>
                                <li><a href="#">For the Record</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Communautés</h3>
                            <ul class="societer-links">
                                <li><a href="#">Espace artistes</a></li>
                                <li><a href="#">Développeurs</a></li>
                                <li><a href="#">Campagnes publicitaires</a></li>
                                <li><a href="#">Investisseurs</a></li>
                                <li><a href="#">Fournisseurs</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Liens utiles</h3>
                            <ul class="societer-links">
                                <li><a href="#">Assistance</a></li>
                                <li><a href="#">Appli mobile gratuite</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Abonnements Spotify</h3>
                            <ul class="societer-links">
                                <li><a href="#">Premium Personnel</a></li>
                                <li><a href="#">Premium Duo</a></li>
                                <li><a href="#">Premium Famille</a></li>
                                <li><a href="#">Spotify Free</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-social">
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                </svg>
                            </a>
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="societer-bottom" style="color : white;">
                        <p>© 2025 Tunify AB</p>
                    </div>
                </footer>
                </div>
            </div>
            <div class="box-2" id="box2-expanded2">
                <div class="section_title">
                    <span id="tendance">Recommandés pour vous</span>
                </div>  
                <div class="mourab3a_kol" >
                <?php foreach (array_slice($allmusicrand, 0, 30) as $music): ?>
                            <?php
                                $image_path = str_replace("C:/xampp/htdocs", "", $music['image_path']);
                                $music_path = str_replace("C:/xampp/htdocs", "", $music['music_path']);
                            ?>
                            <div class="album-item">
                                <div class="cover-img-container" onclick="playSong(
                                    '<?php echo $music_path; ?>',
                                    '<?php echo htmlspecialchars($music['song_title']); ?>',
                                    '<?php echo htmlspecialchars($music['album_name']); ?>',
                                    '<?php echo $image_path; ?>',
                                    this.querySelector('.buttonplay')
                                )">
                                    <img src="<?php echo $image_path; ?>" alt="Cover" class="cover-img">
                                    <div class="start-icon" id="starticon">
                                        <button 
                                            class="showModalBtn buttonplay" 
                                            style="border:none; background-color:transparent" 
                                            data-path="<?php echo $music_path; ?>">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="album-info">
                                    <h3><?= htmlspecialchars($music['song_title']) ?></h3>
                                    <p><?= htmlspecialchars($music['album_name']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                </div>
                <div class="societer">
                    <div class="societer-content">
                        <div class="societer-section">
                            <h3>Société</h3>
                            <ul class="societer-links">
                                <li><a href="#">À propos</a></li>
                                <li><a href="#">Offres d'emploi</a></li>
                                <li><a href="#">For the Record</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Communautés</h3>
                            <ul class="societer-links">
                                <li><a href="#">Espace artistes</a></li>
                                <li><a href="#">Développeurs</a></li>
                                <li><a href="#">Campagnes publicitaires</a></li>
                                <li><a href="#">Investisseurs</a></li>
                                <li><a href="#">Fournisseurs</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Liens utiles</h3>
                            <ul class="societer-links">
                                <li><a href="#">Assistance</a></li>
                                <li><a href="#">Appli mobile gratuite</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Abonnements Spotify</h3>
                            <ul class="societer-links">
                                <li><a href="#">Premium Personnel</a></li>
                                <li><a href="#">Premium Duo</a></li>
                                <li><a href="#">Premium Famille</a></li>
                                <li><a href="#">Spotify Free</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-social">
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                </svg>
                            </a>
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="societer-bottom" style="color : white;">
                        <p>© 2025 Tunify AB</p>
                    </div>
                </footer>
                </div>
            </div>
          <!-- Box 2 -->
          <div class="box-2" id="box2-main" style="display:block;">
          <div class="">
                <div class="section_title">
                    <span id="tendance">Votre Playliste</span>
                    <button class="share-button" id="shareButton2" style="margin:0px -800px 0px 0px ;">
                        <i class="fa-solid fa-share"></i>
                    </button>
                    <form action="music/import_playlist.php" method="post">
                        <div id="shareBox2" style="display: none;">
                            <input type="text" id="shareLinkInput" name="shared_link" placeholder="votre lien ici"required />
                            <button type="submit" id="copyButton">Import</button>
                            <input type="hidden" value="<?php echo $user_id; ?>" name="id_user_playlist">
                            <div id="result"></div>
                        </div>
                    </form>
                 
                </div>

                <div class="">
                <?php 
            if (count($playlists) > 0){
                echo '<div style="display: flex; gap: 25px; flex-wrap: wrap; margin:25px 0px 0px 0px";>';
                    echo "<br><br>";
                        foreach (array_slice($playlists, 0, 20) as $playlist) {
                            echo '<div class="" style="display: flex; align-items: center;">';
                                echo '<div class="" style="display: flex; align-items: center;">';

                                if (!empty($playlist['img'])) {
                                    $imagePath = str_replace('\\', '/', $playlist['img']);
                                    $imagePath = str_replace('C:/xampp/htdocs', '', $imagePath);
                                    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Playlist Image" style="width: 97px; height: 80px; border-radius: 5px; margin-right: 10px;">';
                                    echo '<input class="img_url" type="hidden" name="playlist_name" value="' . htmlspecialchars($imagePath) . '">';
                                } else {
                                    echo '<div style="background-color: rgb(62, 62, 62); width: 97px; height: 80px; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">';
                                        echo '<i class="fa-solid fa-music fa-lg" style="color: white; font-size: 25px;"></i>';
                                    echo '</div>';
                                    echo '<input class="img_url" type="hidden" name="playlist_name" value="vide">';
                                }

                                $cleanImg = str_replace('\\', '/', $playlist['img']);
                                $cleanImg = str_replace('C:/xampp/htdocs', '', $cleanImg);
                                echo '<button '
                                . 'onclick="toggleBox3('
                                    . htmlspecialchars($playlist['id']) . ', '
                                    . '\'' . htmlspecialchars(addslashes($playlist['nom'])) . '\', '
                                    . '\'' . htmlspecialchars($cleanImg) . '\')" '
                                . 'oncontextmenu="openPlaylistContextMenu(event, '
                                    . htmlspecialchars($playlist['id']) . ')" '
                                . 'id="playlist_info_' . htmlspecialchars($playlist['id']) . '" '
                                . 'style="background:none;border:none;color:white;font-size:30px;cursor:pointer;">'
                                . htmlspecialchars($playlist['nom'])
                                . '</button>';


                                echo '<input id="edit_id" type="hidden" name="playlist_id" value="' . htmlspecialchars($playlist['id']) . '">';

                                echo '</div>';
                            echo '</div>';
                            echo '<br>';
                        }?>

                        <div id="playlistContextMenu" class="context-menu">
                            <ul>
                                <form method="POST" action="delete.php" style="display:inline;">
                                    <!-- Hidden input for the playlist ID -->
                                    <input type="hidden" name="id_delete" id="delete_id" value="">

                                    <li data-action="remove-profile" style="list-style:none;">
                                        <!-- Submit button for deletion -->
                                        <button type="submit" 
                                                style="background:none; border:none; color:inherit; font:inherit; cursor:pointer; display:flex; align-items:center;">
                                            <i class="fa-regular fa-user-slash"></i> Supprimer du profil
                                        </button>
                                    </li>
                                </form>
                                <li class="sep"></li>
                                <a href="add_playlist.php" style="text-decoration: none;color:white;"><li data-action="new-playlist"><i class="fa-solid fa-music"></i> Créer une playlist</li></a>
                        </div>
                    <?php
                    echo '</div>';
            }else{
                ?>
                <div class="playlist-card" style="margin:20px 0px 0px 0px;">
                    <h1 class="title">Créez votre première playlist</h1>
                    <p class="subtitle">C'est simple, nous allons vous aider</p>
                    <form action="add_playlist.php" method="post" style="display:inline;">
                        <button type="submit" class="create-button" onclick="toggleBox3()">Créer une playlist</button>
                    </form>
                </div>
                <?php 
            }
            ?>
            </div>

            </div>
            <div class="carousel-container">
                <div class="section_title">
                    <span id="tendance">Dernières Actualités</span>
                    <a href="" style="color:rgb(132, 129, 129);" id="show-all-link"><span>Tout afficher</span></a>
                </div>
                <div class="albums-wrapper">
                    <div class="albums-container">
                        <?php 
                        $allnews = getAllNews($pdo);  // Fetch the news from the database
                        ?>
                        <?php foreach ($allnews as $news): ?>
                            <div class="album-item" onclick="toggleBox5(
                                <?php echo $news['id']; ?>, <!-- News ID -->
                                '<?php echo addslashes($news['image']); ?>', <!-- News image -->
                                '<?php echo date('d/m/Y', strtotime($news['date_publication'])); ?>', <!-- Date -->
                                '<?php echo addslashes($news['titre']); ?>', <!-- Title -->
                                '<?php echo addslashes($news['description']); ?>', <!-- Description -->
                                <?php echo $news['comment_count']; ?> <!-- Comment count -->
                            )">
                                <div class="news-img-container">
                                    <?php
                                        // Clean up the image path (replace backslashes with slashes)
                                        $imagePathnews = str_replace('\\', '/', $news['image']);
                                        // Remove the local absolute path part (e.g. 'C:/xampp/htdocs')
                                        $imagePathnews = str_replace('C:/xampp/htdocs', '', $imagePathnews);
                                        // Remove any leading slash if it's present
                                        $imagePathnews = ltrim($imagePathnews, '/');
                                        // Construct the final relative URL to the image
                                        $imageURLnews = "/" . $imagePathnews;
                                    ?>
                                    <!-- Display the image with the dynamic path -->
                                    <img src="<?php echo $imageURLnews; ?>" alt="News" width="250px" height="250px">

                                    <!-- Display the overlay with publication date -->
                                    <div class="news-overlay">
                                        <span class="news-date"><?php echo date('d/m/Y', strtotime($news['date_publication'])); ?></span>
                                    </div>
                                </div>

                                <div class="news-info">
                                    <h3><?= htmlspecialchars($news['titre']) ?></h3>
                                    <p><?= htmlspecialchars($news['description']) ?></p>
                                    <br>
                                    <div class="news-meta" style="color: gray">
                                        <span class="comment-count">
                                            <i class="fa-regular fa-comment"></i> 
                                            <?= htmlspecialchars($news['comment_count']) ?> commentaires
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>




            <div class="carousel-container">
                <div class="section_title">
                    <span id="tendance">Recommandés pour vous</span>
                    <a href="" style="color:rgb(132, 129, 129);" id="show-all-link"  onclick="toggleBox(event)" ><span>Tout afficher</span></a>                    
                </div>

                <div class="albums-wrapper">
                    <div class="albums-container">
                        <?php foreach (array_slice($allmusicrand, 0, 10) as $music): ?>
                            <?php
                                $image_path = str_replace("C:/xampp/htdocs", "", $music['image_path']);
                                $music_path = str_replace("C:/xampp/htdocs", "", $music['music_path']);
                            ?>
                            <div class="album-item">
                                <div class="cover-img-container"   onclick="playSong(
                                            '<?= addslashes($music_path) ?>',
                                            '<?= addslashes(htmlspecialchars($music['song_title'])) ?>',
                                            '<?= addslashes(htmlspecialchars($music['album_name'])) ?>',
                                            '<?= addslashes($image_path) ?>',
                                            this.querySelector('.buttonplay'),
                                            <?= (int)$music['id'] ?>
                                        )">
                                    <img src="<?php echo $image_path; ?>" alt="Cover" class="cover-img">
                                    <div class="start-icon" id="starticon">
                                        <button 
                                            class="showModalBtn buttonplay" 
                                            style="border:none; background-color:transparent" 
                                            data-path="<?php echo $music_path; ?>">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="album-info">
                                    <h3><?= htmlspecialchars($music['song_title']) ?></h3>
                                    <p><?= htmlspecialchars($music['album_name']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <div class="carousel-container">
                <div class="section_title">
                    <span id="tendance">Artiste recommandés pour vous</span>
                    <a href="" style="color:rgb(132, 129, 129);" id="show-all-link"  onclick="toggleBox2(event)" ><span>Tout afficher</span></a>                    
                </div>

                <div class="albums-wrapper">
                    <div class="albums-container" id="button_artiste">
                        <?php foreach (array_slice($allartiste, 0, 10) as $artiste): ?>
                            <?php
                            $cleanImg = str_replace('\\', '/', $artiste['image_path']);
                            $cleanImg = str_replace('C:/xampp/htdocs', '', $cleanImg);
                            ?>
                            <div class="album-item" onclick="toggleBox3(
                                '<?php echo htmlspecialchars($artiste['artiste_id']); ?>',
                                '<?php echo htmlspecialchars(addslashes($artiste['nom_utilisateur'])); ?>',
                                '<?php echo htmlspecialchars($cleanImg); ?>'
                            )">

                                <?php
                                    $base_path = "/projetweb/assets/includes/";
                                    $image_path = str_replace("C:\\xampp\\htdocs", "", $artiste['image_path']);
                                    $image_path = str_replace("\\", "/", $image_path);
                                ?>
                                <div class="artiste-img-container" >
                                    <img src="<?php echo $image_path ?>" alt="Cover" class="cover-img">
                                    <div class="start-icon">
                                        <button class="showModalBtn" style="border:none; background-color:transparent"><i class="fas fa-play"></i></button>
                                    </div>
                                </div>
                                <div class="album-info">
                                    <h3><?= htmlspecialchars($artiste['nom_utilisateur']) ?></h3>
                                    <p>Artiste</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <style>
                .chatbot-container {
                    position: fixed;
                    bottom: 150px;
                    right: 20px;
                    z-index: 1000;
                    border-radius: 50%;
                    border: 1px solid #ccc;
                }
                .chat-icon {
                    width: 60px;
                    height: 60px;
                    background-color: var(--primary-color);
                    border-radius: 50%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    cursor: pointer;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                    transition: all 0.3s ease;
                }
                .chat-icon i {
                    color: white;
                    font-size: 24px;
                }

                .chat-icon:hover {
                    transform: scale(1.05);
                    background-color: var(--primary-dark);
                }
                .chat-icon.hidden {
                    opacity: 0;
                    pointer-events: none;
                }

                .chat-window {
                    position: absolute;
                    bottom: 80px;
                    right: 0;
                    width: 350px;
                    height: 450px;
                    background-color: white;
                    border-radius: 10px;
                    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                    opacity: 0;
                    pointer-events: none;
                    transform: translateY(20px);
                    transition: all 0.3s ease;
                }

                .chat-window.active {
                    opacity: 1;
                    pointer-events: all;
                    transform: translateY(0);
                }

                .chat-header {
                    background-color:rgb(18, 18, 18);
                    color: white;
                    padding: 15px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .chat-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 18px;
            margin-bottom: 5px;
            word-wrap: break-word;
        }

        .bot-message {
            align-self: flex-start;
            background-color: #f0f0f0;
            color: #333;
            border-bottom-left-radius: 5px;
        }

        .user-message {
            align-self: flex-end;
            background-color:rgb(18, 18, 18);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .error-message {
            background-color: #ffebee;
            color: #d32f2f;
            font-size: 0.85em;
            border: 1px solid #ffcdd2;
        }

        .chat-input-container {
            display: flex;
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .chat-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
        }

        .send-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.3s;
        }

        .send-btn:hover {
            background-color: var(--primary-dark);
        }

        /* Typing indicator */
        .typing-indicator {
            display: flex;
            align-items: center;
        }

        .dots {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #aaa;
            animation: typing 1.4s infinite;
        }

        .dots span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dots span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typing {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        @media (max-width: 768px) {
            .news-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }

            .chat-window {
                width: 300px;
                height: 400px;
                bottom: 70px;
            }
        }

        @media (max-width: 480px) {
            .news-grid {
                grid-template-columns: 1fr;
            }

            .chat-window {
                width: 280px;
                right: 0;
                bottom: 70px;
            }

            .chat-icon {
                width: 50px;
                height: 50px;
            }

            .chat-icon i {
                font-size: 20px;
            }
        }
            </style>
            <div class="chatbot-container">
                <div class="chat-icon" id="chatIcon">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chat-window" id="chatWindow">
                    <div class="chat-header">
                        <h3>Tunify Assistant</h3>
                        <button class="close-btn" id="closeChat">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <div class="message bot-message">
                            <div class="message-content">
                                Bonjour ! Je suis l'assistant Tunify. Comment puis-je vous aider avec les actualités aujourd'hui ?
                            </div>
                        </div>
                    </div>
                    <div class="chat-input-container">
                        <input type="text" id="chatInput" placeholder="Posez votre question..." class="chat-input">
                        <button id="sendMessage" class="send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>

                <div class="societer">
                    <div class="societer-content">
                        <div class="societer-section">
                            <h3>Société</h3>
                            <ul class="societer-links">
                                <li><a href="#">À propos</a></li>
                                <li><a href="#">Offres d'emploi</a></li>
                                <li><a href="#">For the Record</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Communautés</h3>
                            <ul class="societer-links">
                                <li><a href="#">Espace artistes</a></li>
                                <li><a href="#">Développeurs</a></li>
                                <li><a href="#">Campagnes publicitaires</a></li>
                                <li><a href="#">Investisseurs</a></li>
                                <li><a href="#">Fournisseurs</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Liens utiles</h3>
                            <ul class="societer-links">
                                <li><a href="#">Assistance</a></li>
                                <li><a href="#">Appli mobile gratuite</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-section">
                            <h3>Abonnements Spotify</h3>
                            <ul class="societer-links">
                                <li><a href="#">Premium Personnel</a></li>
                                <li><a href="#">Premium Duo</a></li>
                                <li><a href="#">Premium Famille</a></li>
                                <li><a href="#">Spotify Free</a></li>
                            </ul>
                        </div>
                        
                        <div class="societer-social">
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                </svg>
                            </a>
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="societer-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.593 1.323-1.325V1.325C24 .593 23.407 0 22.675 0z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="societer-bottom" style="color : white;">
                        <p>© 2025 Tunify AB</p>
                    </div>
                </footer>
                </div>
            </div>
        </div>      
    <div id="boxmusic" style="">
            <div id="box8oneya" style="position: relative;">
                <img id="song-cover" style="border: none;"  width="90px" height="90px">
                <div style="margin-left: 10px;">
                    <p id="song-title" style="color:white;"></p>
                    <p id="song-artist" style="color:rgb(132, 129, 129); mar"></p>
                </div>
                <form action="music/like_song.php" method="POST" id="likeForm">
                    <input type="hidden" name="song_id_box" id="song_id_box8ne" value=""> <!-- Placeholder for the current song ID -->
                    <button type="submit" style="border:none; background:none; cursor:pointer;">
                        <div id="icon_id" onclick="sendSongId()" style="margin:32px 0 0 40px; color:white; display:none;">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                    </button>
                </form>
            <div class="playlist-box" id="box8">
                <div class="header" style="color:white;margin:0px 0px 0px 0px;">Ajouter à la playlist</div>

                <div class="playlist-option" style="">
                    <div style="display: flex; align-items: center; border: 1px solid gray; border-radius: 5px; padding: 5px; margin-bottom: 10px;">
                        <label for="search-playlist" style="margin: 4px 0px 5px 5px;"><i class="fa-solid fa-magnifying-glass"></i></label>
                        <input type="text" id="search-playlist1" placeholder="Rechercher une playlist"
                            style="border: none; outline: none; background: transparent; color: white; width: 100%; padding-left: 5px;">
                    </div>
                    <a href="add_playlist.php" style="text-decoration: none; color: white;">
                        <button class="new-playlist-btn">+ Nouvelle playlist</button>
                    </a>
                </div>
            <hr>
            <form action="add_to_playlist.php" methode="POST">
                <div class="option" style="border:none; margin:0px 0px 0px 0px; padding:0px 0px 0px 0px;height: 380px;">
                    <?php 
                    if (count($playlists) > 0) {
                        echo '<div class="playlist-card" style="margin:0px 20px 0px 0px;border:none;">'; // Added scroll functionality

                        // Search input

                        // Liked Songs
                        if (hasLikedSongs($user_id)) {
                            echo '<div class="playlist-item" style="display: flex; align-items: center;">';
                                echo '<div class="playlist-icon" style="display: flex; align-items: center;">';
                                    echo '<div style="background: linear-gradient(135deg, #6A11CB, #2575FC); width: 70px; height: 60px; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">';
                                        echo '<i class="fa-solid fa-heart" style="color: white; font-size: 15px;"></i>';
                                    echo '</div>';
                                echo '<input class="img_url" type="hidden" name="playlist_name" value="vide">';
                            echo '<button id="likedSongsButton" type="button" style="background: none; border: none; color: white; font-size: 16px; cursor: pointer;">Liked Songs</button>';
                                echo '<i class="fa-solid fa-circle-check" style="color:green; position:relative; left:27px"></i>';
                                echo '<input type="hidden" name="playlist_id" value="12345">';
                            echo '</div>';
                            echo '</div><br>';
                        }

                        echo '<input type="hidden" name="song_idd" id="song_idd" value=""> <!-- Will be filled via JavaScript -->';
                        
                        foreach ($playlists as $playlist) {
                            echo '<div class="playlist-item" style="display: flex; align-items: center;">';
                            echo '<div class="playlist-icon" style="display: flex; align-items: center;">';

                            // Playlist image
                            if (!empty($playlist['img'])) {
                                $imagePath = str_replace(['\\', 'C:/xampp/htdocs'], ['/', ''], $playlist['img']);
                                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Playlist Image" style="width: 70px; height: 60px; border-radius: 5px; margin-right: 10px;">';
                                echo '<input class="img_url" type="hidden" name="playlist_name" value="' . htmlspecialchars($imagePath) . '">';
                            } else {
                                echo '<div style="background-color: rgb(62, 62, 62); width: 70px; height: 45px; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">';
                                echo '<i class="fa-solid fa-music fa-lg" style="color: white; font-size: 15px;"></i>';
                                echo '</div>';
                                echo '<input class="img_url" type="hidden" name="playlist_name" value="vide">';
                            }

                            // Playlist name button
                            echo '<button class="buttonplaylist"'
                                . 'id="playlist_info_' . htmlspecialchars($playlist['id']) . '" '
                                . 'style="background:none;border:none;color:white;font-size:16px;cursor:pointer;">'
                                . htmlspecialchars($playlist['nom'])
                                . '</button>';

                            
                            echo '<i class="fa-regular fa-circle" style="margin:0px 0px 0px 50px"></i>';    
                            echo '<input id="edit_id" type="hidden" name="playlist_id" value="' . htmlspecialchars($playlist['id']) . '">';
                            echo '</div></div><br>';
                        }

                        echo '</div>'; // close playlist-card
                    }
                    ?>
                </div>
                </form>
                    <div class="dialog-actions">
                        <button class="cancel-btn">Annuler</button>
                        <button class="confirm-btn">Terminé</button>
                    </div>
                </div>
        




            </div>
        <div id="boxbar" style="width: 400px">
            <div style="margin:0px 0px 0px 30px">
            <div class="tooltip-container">
                <button id="shuffle" disabled>
                    <i class="fas fa-random" style="color:gray;"></i>
                    <span class="tooltip-text">aléatoire</span>
                </button>
            </div>
            <div class="tooltip-container">
                <button id="prev">
                    <i class="fas fa-step-backward"></i>
                    <span class="tooltip-text">Précédent</span>
                </button>
            </div>
            <div class="tooltip-container">
                <button id="playPause">
                    <i class="fas fa-play"></i>
                    <span class="tooltip-text">Lecture</span>
                </button>
            </div>

            <!-- Hidden iframe -->
            <iframe id="hiddenFrame" style="display: none;"></iframe>

            <!-- Next button -->
            <div class="tooltip-container">
                <button id="next">
                    <i class="fas fa-step-forward"></i>
                    <span class="tooltip-text">Suivant</span>
                </button>
            </div>

            <div class="tooltip-container">                     
                <button id="repeat" disabled><i class="fas fa-redo" style="color:gray;"></i></button>
                <span class="tooltip-text">repeat</span>
            </div>
            </div>
            <div class="progress" style="">
                <span id="current-time" style="">0:00</span>
                <div class="progress-bar" onclick="seekSong('<?php echo $musicPath; ?>')" >
                    <div class="progress-current" style=""></div>
                </div>
                <span id="total-time" >0:00</span>
            </div>
        </div>

        <div id="boxoption">
            <div class="player-options">
                <!-- Music Icon -->
                <div class="option">
                    <i class="fa-solid fa-music"></i>
                </div>

                <!-- Volume Icon -->
                <div class="option">
                    <i id="volume-icon" class="fas fa-volume-up"></i>
                </div>

                <!-- Volume Bar -->
                <div class="volume-bar" style="margin:5px 0px 0px 0px;">
                    <div class="volume-current"></div>
                    <div class="volume-dot" style="position: absolute; width: 15px; height: 15px; background-color: white; border-radius: 50%; cursor: pointer; top: -5px;"></div>
                </div>

                <!-- Expand/Fullscreen Icon -->
                <div class="container">
                    <div class="option">
                        <i class="fas fa-expand" id="fullscreen-icon"></i>
                    </div>
                </div>
            </div>
        </div>


        <!-- Hidden Audio Tag -->
        <audio id="audioPlayer"></audio>
        </div>

    </div>
</div>
    <script>

function toggleBox3(playlistId, playlistName, imgUrl) {
    const mainBox = document.getElementById("box2-main");
    const expandedBox = document.getElementById("box2-expanded3");
    const idInput = document.getElementById("id_playlist");
    const img = document.getElementById("image_playlist");
    const iconn = document.getElementById("iconn");
    const edit_id = document.getElementById("edit_id");
    const playlist_div = document.getElementById("playlist_div");
    const resultsDiv = document.getElementById('resultsDiv');
    const button_artiste = document.getElementById('button_artiste');
    const box_liked_song = document.getElementById('box_liked_song');
    const playlistdiv = document.getElementById('playlistdiv');
    const buttons = document.getElementById('buttons');
    const historique_song = document.getElementById('historique_song');
    const log_song = document.getElementById('log_song');
    const newsdiv = document.getElementById("news");




    // Set background color with gradient
    if (box_liked_song) {
        box_liked_song.style.background = 'linear-gradient(to top, rgb(93, 93, 93), rgb(62, 62, 62))';
    }

    // Display relevant sections
    if (button_artiste) button_artiste.style.display = 'block';
    if (playlist_div) playlist_div.style.display = 'block';
    if (resultsDiv) resultsDiv.style.display = 'none';
    if (playlistdiv) playlistdiv.style.display = 'none';
    if (buttons) buttons.style.display = 'block';
    if (historique_song) historique_song.style.display = 'none';
    if (log_song) log_song.style.display = 'none';
    if (newsdiv) newsdiv.style.display = 'none';


    // Set the hidden playlist ID
    const playlistClickId = document.getElementById("playlist_click_id");
    if (playlistClickId) playlistClickId.value = playlistId;

    // Image URL handling
    if (imgUrl === "") {
        console.log("Image URL is empty, showing default icon.");
        if (iconn) iconn.style.display = "block";
        if (img) img.style.display = "none";
    } else {
        console.log("Image URL is not empty, setting image source.");
        if (img) {
            img.src = imgUrl;
            img.style.display = "block";
        }
        if (iconn) iconn.style.display = "none";
    }

    // Toggle visibility of boxes
    if (mainBox) mainBox.style.display = "none";
    if (expandedBox) expandedBox.style.display = "block";

    // Set playlist ID in hidden input
    if (idInput) idInput.value = playlistId;

    // Update playlist title
    const titleEl = document.querySelector(".playlist-title");
    if (titleEl) titleEl.innerHTML = playlistName + '<sup>®</sup>';

    // Trigger playlist request
    handlePlaylistRequest(playlistId, true); // Assuming true means "load" state
}

function toggleBox4(playlistId, playlistName, imgUrl) {
    const mainBox = document.getElementById("box2-main");
    const expandedBox = document.getElementById("box2-expanded3");
    const idInput = document.getElementById("id_playlist");
    const img = document.getElementById("image_playlist");
    const iconn = document.getElementById("iconn");
    const edit_id = document.getElementById("edit_id");
    const playlist_div = document.getElementById("playlist_div");
    const resultsDiv = document.getElementById('resultsDiv');
    const button_artiste = document.getElementById('button_artiste');
    const box_liked_song = document.getElementById('box_liked_song');
    const buttons = document.getElementById('buttons');
    const playlistdiv = document.getElementById('playlistdiv');
    const box_recomandé = document.getElementById('box_recomandé');
    const historique_song = document.getElementById('historique_song');
    const newsdiv = document.getElementById("news");


    // Set background color with gradient
    if (box_liked_song) {
        box_liked_song.style.background = 'linear-gradient(to top, rgb(93, 93, 93), rgb(62, 62, 62))';
    }

    console.log(playlistId);
    // Display relevant sections
    if (button_artiste) button_artiste.style.display = 'block';
    if (playlist_div) playlist_div.style.display = 'block';
    if (resultsDiv) resultsDiv.style.display = 'none';
    if (buttons) buttons.style.display = 'none';
    if (playlistdiv) playlistdiv.style.display = 'block';
    if (historique_song) historique_song.style.display = 'block';
    if (newsdiv) newsdiv.style.display = 'none';
   
    
    if (box_recomandé) box_recomandé.style.display = 'none';

    // Set the hidden playlist ID
    const playlistClickId = document.getElementById("playlist_click_id");
    if (playlistClickId) playlistClickId.value = playlistId;

    // Image URL handling
    if (imgUrl === "") {
        console.log("Image URL is empty, showing default icon.");
        if (iconn) iconn.style.display = "block";
        if (img) img.style.display = "none";
    } else {
        console.log("Image URL is not empty, setting image source.");
        if (img) {
            img.src = imgUrl;
            img.style.display = "block";
        }
        if (iconn) iconn.style.display = "none";
    }

    // Toggle visibility of boxes
    if (mainBox) mainBox.style.display = "none";
    if (expandedBox) expandedBox.style.display = "block";

    // Set playlist ID in hidden input
    if (idInput) idInput.value = playlistId;

    // Update playlist title
    const titleEl = document.querySelector(".playlist-title");
    if (titleEl) titleEl.innerHTML = playlistName + '<sup>®</sup>';

    // Trigger playlist request
    loadPlaylists(playlistId);
    loadhistoriquesongs(playlistId);
    loadhistoriquesongslogs(playlistId);
}

// Clean and slightly re-ordered version
function toggleBox5(playlistId, imgUrl, datePublication, titre, description, commentCount) {
    const mainBox = document.getElementById("box2-main");
    const expandedBox = document.getElementById("box2-expanded3");
    const idInput = document.getElementById("id_playlist");
    const img = document.getElementById("image_playlist");
    const iconn = document.getElementById("iconn");
    const edit_id = document.getElementById("edit_id");
    const playlist_div = document.getElementById("playlist_div");
    const resultsDiv = document.getElementById('resultsDiv');
    const button_artiste = document.getElementById('button_artiste');
    const box_liked_song = document.getElementById('box_liked_song');
    const buttons = document.getElementById('buttons');
    const playlistdiv = document.getElementById('playlistdiv');
    const box_recomandé = document.getElementById('box_recomandé');
    const historique_song = document.getElementById('historique_song');
    const newsdiv = document.getElementById("news");
    const news_id= document.getElementById("news_id");

    // Set background color with gradient
    if (box_liked_song) {
        box_liked_song.style.background = 'linear-gradient(to top, rgb(93, 93, 93), rgb(62, 62, 62))';
    }

    // Hide/show appropriate sections
    if (button_artiste) button_artiste.style.display = 'none';
    if (playlist_div) playlist_div.style.display = 'block';
    if (resultsDiv) resultsDiv.style.display = 'none';
    if (buttons) buttons.style.display = 'none';
    if (playlistdiv) playlistdiv.style.display = 'none';
    if (historique_song) historique_song.style.display = 'none';
    if (newsdiv) newsdiv.style.display = 'block';
   
    if (box_recomandé) box_recomandé.style.display = 'none';

    // Set the hidden playlist ID
    const playlistClickId = document.getElementById("playlist_click_id");
    if (playlistClickId) playlistClickId.value = playlistId;

    // Toggle visibility of boxes
    if (mainBox) mainBox.style.display = "none";
    if (expandedBox) expandedBox.style.display = "block";

    // Set playlist ID in hidden input
    if (idInput) idInput.value = playlistId;

    // Update the title and other details
    const titleEl = document.querySelector(".playlist-title");
    if (titleEl) titleEl.innerHTML = titre + '<sup>®</sup>';

    if (news_id) news_id.value = playlistId;

    news(playlistId);
}








    // Detect URL parameter and trigger toggleBox3
    window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);  // Get URL parameters

        // Optionally remove the success message after 3 seconds
        setTimeout(function() {
            const message = document.getElementById('flash-message');
            if (message) {
                message.remove();
            }
        }, 3000);  // Remove the message after 3 seconds
    }

    function bocouvrir() {
        document.getElementById("playlistModal").style.display = "block";

    }
    
    function bocfermer() {
        document.getElementById("playlistModal").style.display = "none";
    }
    function handleFileChange(event) {
        const fileName = event.target.files[0].name; // Get the name of the selected file
        alert('File selected: ' + fileName); // Show file name (can be replaced with any action)
    }
    function handleFileChange(event) {
        const file = event.target.files[0]; // Get the file
        const reader = new FileReader(); // Create a FileReader to read the file

        // Once the file is loaded, set it as the src for the image preview
        reader.onload = function(e) {
            const previewImage = document.getElementById('previewImage');
            const defaultIcon = document.getElementById('defaultIcon');

            // Hide the default icon
            defaultIcon.style.display = 'none';
            
            // Set the image source to the selected file
            previewImage.src = e.target.result;
            
            // Show the image in the upload box
            previewImage.style.display = 'block';
        };

        // Read the selected file as a data URL (this will trigger the onload event)
        if (file) {
            reader.readAsDataURL(file);
        }
    }


    const likedSongs = <?php echo json_encode($likedSongIds); ?>;
    function updateLikeIcon(currentSongId) {
        const likeIcon = document.getElementById('icon_id').querySelector('i');

        if (likedSongs.includes(parseInt(currentSongId))) {
            likeIcon.style.color = 'green';
        } else {
            likeIcon.style.color = 'gray';
        }
    }


    // Form submit event
    document.getElementById('likeForm').addEventListener('submit', function(event) {
    const songId = parseInt(document.getElementById('song_id_box8ne').value);
    const likeIcon = document.getElementById('icon_id').querySelector('i');
    const messageBox = document.getElementById('messageBox');
    const box8 = document.getElementById('box8');

    if (likedSongs.includes(songId)) {
        event.preventDefault(); // Stop form from submitting
        box8.style.display = "block";
        likeIcon.style.color = 'green';
    }
    });

    // ❌ Close box when clicking outside of it
    document.addEventListener('click', function(event) {
        const box8 = document.getElementById('box8');
        if (box8.style.display === 'block' && !box8.contains(event.target)) {
            box8.style.display = 'none';
        }
    });

    // ❌ Close box when clicking "Annuler" button
    document.querySelector('.cancel-btn').addEventListener('click', function() {
        document.getElementById('box8').style.display = 'none';
    });


    document.addEventListener('DOMContentLoaded', () => {
    // Select all icons with the class 'plus-icon'
        const plusIcons = document.querySelectorAll('.plus-icon');
        console.log(plusIcons);
        
        // Select the box you want to open
        const box8 = document.getElementById('box8');

         plusIcons.forEach(icon => {
            icon.addEventListener('click', () => {
                if (box8) {
                    box8.style.display = 'block'; // Show the box (make it visible)
                }
            });
        });
    });

    
    document.addEventListener('DOMContentLoaded', () => {
        const songNumbers = document.querySelectorAll('.song-number');

        songNumbers.forEach(span => {
            span.addEventListener('mouseenter', () => {
                span.innerHTML = "<i class='fa-solid fa-play'></i>"; // Play icon on hover
            });

            span.addEventListener('mouseleave', () => {
                span.textContent = span.getAttribute('data-number'); // Restore the number
            });
        });
    });
    function toggleDropdown() {
        const dropdown = document.getElementById("dropdownMenu");
        dropdown.classList.toggle("show");
    }

    // Close dropdown when clicking outside of it
    document.addEventListener("click", function(event) {
        const button = document.querySelector(".dropdown-button");
        const menu = document.getElementById("dropdownMenu");

        if (!button.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.remove("show");
        }
    });
    const button = document.querySelector('.download-button');
    if (button) {
        button.addEventListener('click', () => {
            button.classList.add('downloading');

            // Simulate download progress for 3 seconds
            setTimeout(() => {
                button.classList.remove('downloading');
                alert('Download complete!');
            }, 3000);
        });
    }

    setTimeout(function() {
        const notif = document.querySelector('.notification');
        if (notif) {
            notif.style.display = 'none';
        }
    }, 5000); // Hide after 5 seconds

    document.addEventListener('DOMContentLoaded', function() {
            const notificationIcon = document.querySelector('.notification-icon');
            const notificationBadge = document.querySelector('.notification-badge');
            const notificationDropdown = document.querySelector('.notification-dropdown');
            const notificationList = document.querySelector('.notification-list');
            
            // Toggle notification dropdown
            if (notificationIcon) {
                notificationIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('show');
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                if (notificationDropdown && notificationDropdown.classList.contains('show')) {
                    notificationDropdown.classList.remove('show');
                }
            });
            
            // Check for new notifications
            function checkNotifications() {
                // Afficher un message de débogage dans la console
                console.log('Vérification des notifications...');
                
                // Corriger le chemin en fonction de votre structure de projet
                fetch('news/check_notifications.php')
                    .then(response => {
                        console.log('Réponse du serveur reçue');
                        return response.json();
                    })
                    .then(data => {
                        console.log('Données reçues:', data);
                        
                        if (data.notifications && data.notifications.length > 0) {
                            console.log(`${data.notifications.length} notifications trouvées`);
                            
                            // Update badge
                            notificationBadge.textContent = data.notifications.length;
                            notificationBadge.classList.add('show');
                            
                            // Clear list and remove empty message
                            const emptyMessage = notificationList.querySelector('.notification-empty');
                            if (emptyMessage) {
                                notificationList.removeChild(emptyMessage);
                            }
                            
                            // Clear previous notifications
                            while (notificationList.firstChild) {
                                notificationList.removeChild(notificationList.firstChild);
                            }
                            
                            // Add notifications to list
                            data.notifications.forEach(notification => {
                                const notificationItem = document.createElement('div');
                                notificationItem.classList.add('notification-item');
                                
                                const date = new Date(notification.date_publication);
                                const formattedDate = `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()} à ${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')}`;
                                
                                notificationItem.innerHTML = `
                                    <div class="notification-content">Nouvelle publication: ${notification.titre}</div>
                                    <div class="notification-time">${formattedDate}</div>
                                `;
                                
                                
                                notificationList.appendChild(notificationItem);
                            });
                        } else {
                            console.log('Aucune notification trouvée');
                            // Assurer que le message "Aucune notification" est présent
                            notificationList.innerHTML = '<div class="notification-empty">Aucune notification</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la vérification des notifications:', error);
                        notificationList.innerHTML = '<div class="notification-empty">Erreur de chargement</div>';
                    });
            }
            
            // Check notifications immediately and then every 30 seconds
            if (notificationIcon) {
                console.log('Initialisation du système de notifications');
                checkNotifications();
                setInterval(checkNotifications, 60000);
                
                // Force l'affichage d'une notification pour tester
                setTimeout(function() {
                    notificationBadge.classList.add('show');
                }, 2000);
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
    const chatIcon = document.getElementById('chatIcon');
    const chatWindow = document.getElementById('chatWindow');
    const closeChat = document.getElementById('closeChat');
    const chatInput = document.getElementById('chatInput');
    const sendMessage = document.getElementById('sendMessage');
    const chatMessages = document.getElementById('chatMessages');

    chatIcon.addEventListener('click', () => chatWindow.classList.toggle('active'));
    closeChat.addEventListener('click', () => chatWindow.classList.remove('active'));

    function escapeHTML(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function formatNumber(num) {
        if (!num) return 'N/A';
        num = Number(num.toString().replace(/,/g, ''));
        if (isNaN(num)) return 'N/A';
        if (num >= 1e9) return (num / 1e9).toFixed(1) + 'B';
        if (num >= 1e6) return (num / 1e6).toFixed(1) + 'M';
        if (num >= 1e3) return (num / 1e3).toFixed(1) + 'K';
        return num.toString();
    }

    // Keep track of currently shown info div (artist or song)
    let currentInfoDiv = null;

    function addMessage(message, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', `${sender}-message`);
        const contentDiv = document.createElement('div');
        contentDiv.classList.add('message-content');

        if (typeof message === 'object' && message.success) {
            const hasArtist = message.artist && Object.keys(message.artist).length > 0;
            const hasSong = message.song && Object.keys(message.song).length > 0;

            if (message.description) {
                contentDiv.innerHTML = `<p>${escapeHTML(message.description)}</p>`;
            } else if (hasArtist && hasSong) {
                contentDiv.innerHTML = `
                    <p>We found both an <strong>Artist</strong> and a <strong>Song</strong>. Choose what you'd like to see:</p>
                    <div class="btn-group">
                        <button class="chat-btn showArtistBtn">🎤 Show Artist Info</button>
                        <button class="chat-btn showSongBtn">🎵 Show Song Info</button>
                    </div>
                `;

                messageDiv.appendChild(contentDiv);
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                const showArtistBtn = messageDiv.querySelector('.showArtistBtn');
                const showSongBtn = messageDiv.querySelector('.showSongBtn');

                showArtistBtn.addEventListener('click', () => displayArtistInfo(message.artist));
                showSongBtn.addEventListener('click', () => displaySongInfo(message.song));
                return;
            } else if (hasArtist) {
                displayArtistInfo(message.artist);
                return;
            } else if (hasSong) {
                displaySongInfo(message.song);
                return;
            } else {
                contentDiv.textContent = "No artist or song data found.";
            }
        } else {
            contentDiv.textContent = escapeHTML(message);
        }

        messageDiv.appendChild(contentDiv);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function displayArtistInfo(artist) {
        if (currentInfoDiv) currentInfoDiv.remove();  // Remove previous info div

        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', 'bot-message');
        const contentDiv = document.createElement('div');
        contentDiv.classList.add('message-content');

        const channelName = escapeHTML(artist.channel_name || 'Unknown Artist');
        const subscribers = escapeHTML(artist.subscribers || 'N/A');
        const channelUrl = artist.channel_url ? escapeHTML(artist.channel_url) : '#';

        contentDiv.innerHTML = `
            🎤 <strong>${channelName}</strong><br>
            👥 Subscribers: <strong>${subscribers}</strong><br>
            🔗 <a href="${channelUrl}" target="_blank" rel="noopener noreferrer">YouTube Channel</a>
        `;

        messageDiv.appendChild(contentDiv);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        currentInfoDiv = messageDiv;
    }

    function displaySongInfo(song) {
        if (currentInfoDiv) currentInfoDiv.remove();  // Remove previous info div

        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', 'bot-message');
        const contentDiv = document.createElement('div');
        contentDiv.classList.add('message-content');

        const title = escapeHTML(song.title);
        const artist = escapeHTML(song.artist || 'Unknown Artist');
        const releaseDate = escapeHTML(song.release_date || 'N/A');
        const viewsFormatted = formatNumber(song.views);
        const geniusUrl = escapeHTML(song.genius_url || '#');
        const youtubeUrl = escapeHTML(song.youtube_url || '#');

        contentDiv.innerHTML = `
            🎵 <strong>${title}</strong> by <em>${artist}</em><br>
            📅 Released: <strong>${releaseDate}</strong><br>
            👁️ Views : <strong>${viewsFormatted}</strong><br><br>
            <a href="${geniusUrl}" target="_blank" rel="noopener noreferrer">View Lyrics</a><br>
            <a href="${youtubeUrl}" target="_blank" rel="noopener noreferrer">Watch clip</a>
        `;

        messageDiv.appendChild(contentDiv);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        currentInfoDiv = messageDiv;
    }

    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.classList.add('message', 'bot-message', 'typing');
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <span></span><span></span><span></span>
            </div>
        `;
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function hideTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.remove();
    }

    async function sendMessageToAPI(message) {
        try {
            const response = await fetch('/projetweb/View/tunify_avec_connexion/news/api/chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            return { success: false, message: "Sorry, I'm having trouble connecting to the server." };
        }
    }

    async function handleSendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        // Remove previously displayed artist/song info on new search:
        if (currentInfoDiv) {
            currentInfoDiv.remove();
            currentInfoDiv = null;
        }

        addMessage(message, 'user');
        chatInput.value = '';
        chatInput.disabled = true;
        sendMessage.disabled = true;

        // Check if user searched for "tunify" (case-insensitive)
        if (message.toLowerCase() === 'tunify') {
            hideTypingIndicator(); // Just in case
            addMessage({
                success: true,
                artist: {},
                song: {},
                description: "🎵 Tunify is your ultimate music companion site, offering song info, artist data, and much more to enhance your listening experience!"
            }, 'bot');

            chatInput.disabled = false;
            sendMessage.disabled = false;
            chatInput.focus();
            return;
        }

        showTypingIndicator();

        const response = await sendMessageToAPI(message);

        hideTypingIndicator();

        if (response.success) {
            addMessage(response, 'bot');
        } else {
            addMessage(response.message || "Sorry, something went wrong.", 'bot');
        }

        chatInput.disabled = false;
        sendMessage.disabled = false;
        chatInput.focus();
    }

    sendMessage.addEventListener('click', handleSendMessage);
    chatInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') handleSendMessage();
    });
});








    </script>
<style>
    .chat-btn {
    background-color: #4CAF50;
    color: white;
    padding: 8px 14px;
    margin: 5px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.chat-btn:hover {
    background-color: #3e8e41;
}

.btn-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 10px;
}

</style>

<script src="js.js"></script>
</body>
</html>