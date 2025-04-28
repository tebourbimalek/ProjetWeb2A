<?php 
session_start();

require_once 'displaysongs.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php'; 

$allmusicrand=chansonrand();
$allartiste=allartiste();

$count2=1;



$user_id = 8;

$playlists = getplaylist($user_id);

if (isset($_GET['playlist_created']) && $_GET['playlist_created'] == 'true') {
    echo '<div class="message-box success" id="flash-message">Playlist créée avec succès</div>';
}
// Check if there's an error message
if (isset($_SESSION['error'])) {
    echo '<div class="message-box error" id="flash-message">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']); // Clear the error message after displaying
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['playlist_id'])) {
    require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php'; 

    $playlistId = (int) $_POST['playlist_id'];

    // Fetch playlist songs and random songs
    $playlist_songs = fetchSongsFromPlaylist($playlistId);
    $random_songs = executeFetchRandomSongs($playlistId, 10);

    // Start capturing the playlist songs HTML
    ob_start();

    if (!empty($playlist_songs)) {
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
                data-song-url='" . htmlspecialchars($musicURL) . "'
                data-song-cover='" . htmlspecialchars($imageURL) . "'
                data-song-artiste='" . $song['album_name'] . "'
                onmouseover=\"this.querySelector('.song-number').innerHTML='<i class=&quot;fa-solid fa-play&quot; style=&quot;font-size:13px;&quot;></i>';\" 
                onmouseout=\"this.querySelector('.song-number').innerHTML=this.querySelector('.song-number').dataset.number;\"
                onclick='playSongplaylist(this); updateSongDetails(this);'>

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

        $count++;

        }

        echo "</tbody>";
        echo "</table>";
    }
    $recommendationsHtml = ob_get_clean();

    // Respond with JSON
    header('Content-Type: application/json');
    echo json_encode([
        'playlist' => $playlistHtml,
        'recommendations' => $recommendationsHtml
    ]);

    exit;
}








?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunify</title>
    <script src="https://kit.fontawesome.com/d4610e21c1.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css.css">
    <style>
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


    </style>
    
</head>
<body>  
<div class="background"></div>    
    <!-- Modal overlay -->
    <nav class="navbar">
        <div class="left-section">
            <img src="" alt="Logo" class="logo">
            <div class="icon-container">
                <div class="icon-house">
                    <a href="/projetweb/View/tunify_avec_connexion/avec_connexion.php"><i class="fa-solid fa-house" style="color: grey;font-size:20px;"></i></a>
                </div>
                <span class="tooltip">Accueil</span>
            </div>
            <div class="search-bar">
                <div class="icon-container">
                    <button class="icon-searsh"><i class="fa-solid fa-magnifying-glass" style="color: grey;font-size:20px;"></i></button>
                    <span class="tooltip">Rechercher</span>
                </div>
                <input type="text" placeholder="Que souhaitez-vous écouter ou regarder ?" style="width: 360px;">
                <br><br>
                <span class="divider" >|</span>
                <div class="icon-container">
                    <button class="icon-searsh"><i class="fa-regular fa-bookmark" style="color: grey;font-size:20px;"></i></button>
                    <span class="tooltip">parcourir</span>
                </div>  
            </div>
        </div>
        <div class="right-section">
            <a href="#" class="mot">|Gaming</a>
            <a href="#" class="mot">Reclamation</a>
            <a href="#" class="mot">Premium|</a>
            <i class="fa-solid fa-user fa-xl"></i>
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
            <a href="add_playlist.php" class="create-playlist" style="text-decoration: none; color: white;">
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
                                <form method="POST" action="delete.php" style="display:inline;">
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
                                <a href="add_playlist.php" style="text-decoration: none;color:white;"><li data-action="new-playlist"><i class="fa-solid fa-music"></i> Créer une playlist</li></a>
                        </div>
                    <?php
                    echo '</div>';
            }else{
                ?>
                <div class="playlist-card">
                    <h1 class="title">Créez votre première playlist</h1>
                    <p class="subtitle">C'est simple, nous allons vous aider</p>
                    <form action="add_playlist.php" method="post" style="display:inline;">
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
                    <h1 class="playlist-title" style="font-size: 50px;color:white;">Ma playlist<sup>®</sup> 1</h1>
                    <div class="playlist-owner" style="color:white;">tebourbimalek</div>
                </div>
                
                <hr>
            </div>
            <br>
            <div>
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


                <button class="ellipsis-button">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>


            </div>
            <div id="playlist_song" style="display:block;">
            </div>
               
            <div id ="likedsongs" style="display:none;">
                <div>
                <?php 
                $user_id = 8;
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
                echo "</tr>";
                echo "</thead>";
            
                // Horizontal line below header
                echo "<tr><td colspan='5' style='border-bottom: 2px solid #ccc; padding: 0;'></td></tr>";
            
                echo "<tbody>";
                $count2 = 1;
                foreach ($likedSongs as $song) {
                    echo "<tr style='color:white;'>";
            
                    // Song number
                    echo "<td style='padding: 10px;'>" . $count2++ . "</td>";
            
                    // Song image + title
                    $imagePath = str_replace('\\', '/', $song['image_path']);
                    $imagePath = str_replace('C:/xampp/htdocs', '', $imagePath);
                    echo "<td style='padding: 10px; display: flex; align-items: center;'>";
                    echo "<img src='" . $imagePath . "' alt='Song Image' style='width: 50px; height: 50px; margin-right: 10px;'>";
                    echo "<span>" . htmlspecialchars($song['song_title']) . "</span>";
                    echo "</td>";
            
                    // Album/artist
                    echo "<td style='padding: 10px;'>" . htmlspecialchars($song['album_name']) . "</td>";
            
                    // Plus icon button inside the cell
                    echo "<td style='padding: 10px; text-align: center;'>";
                    echo "<button style='background: none; border: none; cursor: pointer;'>";
                    
                    echo '<i class="fa-solid fa-circle-check plus-icon"></i>';
                    
                    echo "</button>";
                    echo "</td>";
                    // Duration
                    echo "<td style='padding: 10px;'>" . htmlspecialchars($song['duree']) . "</td>";
                    
            
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
                    <form action="import_playlist.php" method="post">
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
                <form action="like_song.php" method="POST" id="likeForm">
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
                <button id="playPause" onclick="playPauseToggle()">
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
    <script>

function toggleBox3(playlistId, playlistName, imgUrl) {

    const mainBox = document.getElementById("box2-main");
    const expandedBox = document.getElementById("box2-expanded3");
    const idInput = document.getElementById("id_playlist");
    const img = document.getElementById("image_playlist");
    const iconn = document.getElementById("iconn");
    const edit_id = document.getElementById("edit_id");

    console.log("edit id",edit_id.value);
    document.getElementById("playlist_click_id").value=playlistId;

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

    // Toggle visibility
    if (mainBox.style.display === "none" || mainBox.style.display === "") {
        mainBox.style.display = "block";
        expandedBox.style.display = "none";
    } else {
        mainBox.style.display = "none";
        expandedBox.style.display = "block";
    }

    // Set the hidden input value
    if (idInput) idInput.value = playlistId;

    // Update the on-screen title
    const titleEl = document.querySelector(".playlist-title");
    if (titleEl) titleEl.innerHTML = playlistName + '<sup>®</sup>';


    // Trigger playlist request
    handlePlaylistRequest(playlistId, isLoad = true);
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



    </script>


<script src="js.js"></script>
</body>
</html>