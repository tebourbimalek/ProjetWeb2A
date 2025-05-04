<?php 
session_start();
// Start the session

require_once 'C:\xampp\htdocs\projetweb\controller\controller.php';

// Include the config file to reuse the database connection
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';

// Get a PDO instance from the config class
$pdo = config::getConnexion();


$user = getUserInfo($pdo);

// Ensuite, tu peux afficher les données :
echo "Bienvenue " . htmlspecialchars($user->getNomUtilisateur()) . " !<br>";
echo "Email : " . htmlspecialchars($user->getEmail()) . "<br>";
echo "Score : " . htmlspecialchars($user->getScore()) . "<br>";
echo "Type : " . htmlspecialchars($user->getTypeUtilisateur()) . "<br>";


require_once 'displaysongs.php';

$allmusicrand=chansonrand();
$allartiste=allartiste();


if (isset($_GET['next'])) {


    $randomSong = onechansonrand();
    $music_path = str_replace("C:/xampp/htdocs", "", $randomSong[0]['music_path']);
    $image_path = str_replace("C:/xampp/htdocs", "", $randomSong[0]['image_path']);
    $song_title = htmlspecialchars($randomSong[0]['song_title']);
    $album_name = htmlspecialchars($randomSong[0]['album_name']);

    // Output a JS script to call your existing playSong function
    echo "<script>
        if (window.parent.updateSongInfo) {
            window.parent.updateSongInfo('$song_title', '$album_name', '$image_path','$music_path');
        }
    </script>";

}


// Redirection si l'utilisateur n'est pas connecté
requireLogin();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunify</title>
    <script src="https://kit.fontawesome.com/d4610e21c1.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css.css">
    
</head>
<body>  
<div class="background"></div>    
    <!-- Modal overlay -->
    <nav class="navbar">
        <div class="left-section">
            <img src="/projetweb/assets/img/logo.png" alt="Logo" class="logo">
            <div class="icon-container">
                <div class="icon-house">
                    <a href="/projetweb/View/pages/tunify_avec_connexion/avec_connexion.php"><i class="fa-solid fa-house" style="color: grey;font-size:20px;"></i></a>
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
</style>

<div class="right-section">
    <?php if (isset($_SESSION['user'])): ?>
        <div class="dropdown">
            <button onclick="toggleDropdown()" class="dropdown-button">
                <i class="fas fa-user"></i>
            </button>
            <div id="dropdownMenu" class="dropdown-menu">
                <a href="/projetweb/View/pages/tunify_avec_connexion/account/overview.php" target="_blank">Account <i class="fas fa-external-link-alt external-link"></i></a>
                <a href="#profile" onclick="openProfileSection()">Profile</a>
                <a href="#premium">Upgrade to Premium <i class="fas fa-external-link-alt external-link"></i></a>
                <a href="#support">Support <i class="fas fa-external-link-alt external-link"></i></a>
                <a href="#download">Download <i class="fas fa-external-link-alt external-link"></i></a>
                <a href="#" onclick="showSettingsSection(); return false;">Settings</a>
                <a href="logout.php">Log out</a>
            </div>
        </div>
    <?php else: ?>
        <a href="/projetweb/View/pages/tunisfy_sans_conexion/login.php" class="nav-link">Se connecter</a>
        <a href="/projetweb/View/pages/tunisfy_sans_conexion/register.php" class="nav-link">S'inscrire</a>
    <?php endif; ?>
</div>

    </nav>
    <div class="main-content">
        <div>
            <div class="sidebar box-1">
                <p style="font-size: 18px; padding: 10px;">Bibliothéque</p>
                
                <div class="icon-container">
                    <button class="icon-plus"><i class="fa-regular fa-plus" style="color: grey;font-size:20px;"></i></button>
                    <span class="tooltip1">Créer une playlist ou un dossier</span>
                </div>
                
            </div>
            <div class="playlist-card">
                <h1 class="title">Créez votre première playlist</h1>
                <p class="subtitle">C'est simple, nous allons vous aider</p>
                <button class="create-button">Créer une playlist</button>
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
        </div>
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


            <!-- Add this inside your box-2 container -->
            <div class="box-2" id="profileSection" style="display: none;">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-info">
            <!-- Profile Image -->
            <?php if (!empty($user->getImagePath())): ?>
                <div class="profile-image-container">
                    <div class="profile-image">
                        <img src="<?= htmlspecialchars($user->getImagePath()) . '?v=' . time() ?>" class="profile-avatar" alt="Profile Picture">
                    </div>
                    <!-- Add three dots menu button -->
                    <div class="profile-options">
                        <button class="options-button" onclick="toggleProfileMenu()">•••</button>
                        <!-- Dropdown menu -->
                        <div id="profileMenu" class="profile-dropdown" style="display: none;">
                            <div class="dropdown-item" onclick="openEditProfileModal()">
                                <span class="icon">✎</span> Edit profile
                            </div>
                            <div class="dropdown-item" onclick="copyProfileLink()">
                                <span class="icon">⧉</span> Copy link to profile
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden file input and form -->
                <form id="uploadForm" method="POST" enctype="multipart/form-data" action="update_profile_picture.php">
                    <input type="file" name="profile_image" id="profileUpload" style="display: none;" onchange="uploadProfileImage()">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($user->getArtisteId()) ?>">
                </form>
            <?php endif; ?>
            
            <div class="profile-meta">
                <h1 class="profile-name"><?= htmlspecialchars($user->getNomUtilisateur()) ?></h1>
                <div class="profile-stats">
                    <span class="stat-item">• 6 Following</span>
                </div>
            </div>
        </div>
        <button class="back-button" onclick="closeProfileSection()">← Back</button>
    </div>

    <!-- Following Grid -->
    <div class="following-section">
        <h2 class="section-title">Following</h2>
        <div class="following-grid">
            <!-- Artist 1: A.L.A -->
            <div class="artist-card">
                <div class="artist-image">
                 <img src="\projetweb\assets\includes\ALA.jpeg" alt="A.L.A">
                </div>
                <div class="artist-info">
                    <h3 class="artist-name">A.L.A</h3>
                    <p class="artist-type">Artist</p>
                </div>
            </div>
            
            <!-- Artist 2: Eminem -->
            <div class="artist-card">
                <div class="artist-image">
                    <img src="\projetweb\assets\includes\kaso.jpeg" alt="Kaso">
                </div>
                <div class="artist-info">
                    <h3 class="artist-name">Kaso</h3>
                    <p class="artist-type">Artist</p>
                </div>
            </div>
            
            <!-- Artist 3: Kendrick Lamar -->
            <div class="artist-card">
                <div class="artist-image">
                    <img src="\projetweb\assets\includes\gga.jpeg" alt="GGA">
                </div>
                <div class="artist-info">
                    <h3 class="artist-name">GGA</h3>
                    <p class="artist-type">Artist</p>
                </div>
            </div>
            
            <!-- Artist 4: Lana Del Rey -->
            <div class="artist-card">
                <div class="artist-image">
                    <img src="\projetweb\assets\includes\balti.jpeg" alt="Balti">
                </div>
                <div class="artist-info">
                    <h3 class="artist-name">Balti</h3>
                    <p class="artist-type">Artist</p>
                </div>
            </div>
            
            <!-- Artist 5: Samara -->
            <div class="artist-card">
                <div class="artist-image">
                    <img src="\projetweb\assets\includes\samara.jpeg" alt="Samara">
                </div>
                <div class="artist-info">
                    <h3 class="artist-name">Samara</h3>
                    <p class="artist-type">Artist</p>
                </div>
            </div>
            
            <!-- Artist 6: The Weeknd -->
            <div class="artist-card">
                <div class="artist-image">
                    <img src="\projetweb\assets\includes\stou.jpeg" alt="Stou">
                </div>
                <div class="artist-info">
                    <h3 class="artist-name">Stou</h3>
                    <p class="artist-type">Artist</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Links -->
    <div class="footer-links">
        <div class="footer-section">
            <h3 class="footer-title">Company</h3>
            <ul class="footer-list">
                <li>About</li>
                <li>Jobs</li>
                <li>For the Record</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3 class="footer-title">Communities</h3>
            <ul class="footer-list">
                <li>For Artists</li>
                <li>Developers</li>
                <li>Advertising</li>
                <li>Investors</li>
                <li>Vendors</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3 class="footer-title">Useful links</h3>
            <ul class="footer-list">
                <li>Support</li>
                <li>Free Mobile App</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3 class="footer-title">Tunify Plans</h3>
            <ul class="footer-list">
                <li>Premium Individual</li>
                <li>Premium Duo</li>
                <li>Premium Family</li>
                <li>Tunify Free</li>
            </ul>
        </div>
    </div>

    <!-- Social Links -->
    <div class="social-links">
        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
        <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
    </div>

    <!-- Legal Footer -->
    <div class="profile-legal">
        <div class="legal-links">
            <a href="#">Legal</a>
            <a href="#">Safety & Privacy Center</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Cookies</a>
            <a href="#">About Ads</a>
            <a href="#">Accessibility</a>
        </div>
        <div class="copyright">© 2025 Tunify AB</div>
    </div>
</div>

<!-- Settings Section -->
<div class="box-2" id="settingsSection" style="display: none;">
    <div class="settings-header">
        <h1>Settings</h1>
        <div class="search-icon">
            <i class="fas fa-search"></i>
        </div>
    </div>

    <!-- Account Settings -->
    <div class="settings-group">
        <h2 class="settings-category">Account</h2>
        <div class="settings-item">
            <div class="setting-info">
                <span class="setting-label">Edit login methods</span>
            </div>
            <div class="setting-action">
    <button class="edit-button" onclick="openLoginMethods()">Edit <i class="fas fa-external-link-alt"></i></button>
</div>
<script>
    function openLoginMethods() {
    // Redirige vers la page login-methods.php en utilisant un chemin relatif
    window.location.href = "/projetweb/View/pages/tunify_avec_connexion/account/login-methods.php";
}

</script>
        </div>
    </div>

    <!-- Language Settings -->
    <div class="settings-group">
        <h2 class="settings-category">Language</h2>
        <div class="settings-item">
            <div class="setting-info">
                <span class="setting-label">Choose language - Changes will be applied after restarting the app</span>
            </div>
            <div class="setting-action">
                <div class="dropdown-select">
                    <select>
                        <option selected>English (English)</option>
                        <option>Français (French)</option>
                        <option>Español (Spanish)</option>
                        <option>العربية (Arabic)</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Library Settings -->
    <div class="settings-group">
        <h2 class="settings-category">Your Library</h2>
        <div class="settings-item">
            <div class="setting-info">
                <span class="setting-label">Use compact library layout</span>
            </div>
            <div class="setting-action">
                <label class="toggle-switch">
                    <input type="checkbox">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

    <!-- Display Settings -->
    <div class="settings-group">
        <h2 class="settings-category">Display</h2>
        <div class="settings-item">
            <div class="setting-info">
                <span class="setting-label">Show the now-playing panel on click of play</span>
            </div>
            <div class="setting-action">
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
        <div class="settings-item">
            <div class="setting-info">
                <span class="setting-label">Display short, looping visuals on tracks (Canvas)</span>
            </div>
            <div class="setting-action">
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

    <!-- Social Settings -->
    <div class="settings-group">
        <h2 class="settings-category">Social</h2>
        <div class="settings-item">
            <div class="setting-info">
                <span class="setting-label">Show my follower and following lists on my public profile</span>
            </div>
            <div class="setting-action">
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

    <!-- Footer Links -->
    <div class="footer-links">
        <div class="footer-section">
            <h3 class="footer-title">Company</h3>
            <ul class="footer-list">
                <li>About</li>
                <li>Jobs</li>
                <li>For the Record</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3 class="footer-title">Communities</h3>
            <ul class="footer-list">
                <li>For Artists</li>
                <li>Developers</li>
                <li>Advertising</li>
                <li>Investors</li>
                <li>Vendors</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3 class="footer-title">Useful links</h3>
            <ul class="footer-list">
                <li>Support</li>
                <li>Free Mobile App</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3 class="footer-title">Tunify Plans</h3>
            <ul class="footer-list">
                <li>Premium Individual</li>
                <li>Premium Duo</li>
                <li>Premium Family</li>
                <li>Tunify Free</li>
            </ul>
        </div>
    </div>

    <!-- Social Links -->
    <div class="social-links">
        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
        <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
    </div>

    <!-- Legal Footer -->
    <div class="settings-legal">
        <div class="legal-links">
            <a href="#">Legal</a>
            <a href="#">Safety & Privacy Center</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Cookies</a>
            <a href="#">About Ads</a>
            <a href="#">Accessibility</a>
        </div>
        <div class="copyright">© 2025 Tunify AB</div>
    </div>
</div>

<style>
/* Profile Section Styles */
.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 1px solid #282828;
}

.profile-info {
    display: flex;
    align-items: center;
    gap: 25px;
}
.profile-image {
    position: relative;
    cursor: pointer;
}

.profile-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.profile-image:hover .profile-image-overlay {
    opacity: 1;
}

.profile-image-overlay span {
    color: white;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    padding: 0 10px;
}

/* Notification styles */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 4px;
    color: white;
    font-size: 14px;
    max-width: 300px;
    z-index: 1000;
    transform: translateX(120%);
    transition: transform 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.notification.show {
    transform: translateX(0);
}

.notification.success {
    background-color: #1DB954;
}

.notification.error {
    background-color: #E61E32;
}
.profile-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.profile-meta {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.profile-name {
    color: #fff;
    font-size: 48px;
    font-weight: 700;
    margin: 0;
}

.profile-stats {
    display: flex;
    gap: 20px;
    color: #b3b3b3;
    font-size: 16px;
}

.back-button {
    background: transparent;
    color: #fff;
    border: none;
    font-size: 16px;
    cursor: pointer;
    padding: 8px 16px;
    border-radius: 20px;
    transition: background-color 0.2s;
}

.back-button:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

/* Following Grid */
.following-section {
    margin: 40px 0;
}

.section-title {
    color: #fff;
    font-size: 24px;
    margin-bottom: 25px;
}

.following-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
}

.artist-card {
    background: #181818;
    border-radius: 8px;
    padding: 15px;
    transition: background-color 0.3s;
    cursor: pointer;
}

.artist-card:hover {
    background: #282828;
}

.artist-image {
    position: relative;
    margin-bottom: 15px;
}

.artist-image img {
    width: 100%;
    aspect-ratio: 1/1;
    object-fit: cover;
    border-radius: 50%;
}

.artist-info {
    text-align: center;
}

.artist-name {
    color: #fff;
    font-size: 16px;
    margin: 5px 0;
    font-weight: 500;
}

.artist-type {
    color: #b3b3b3;
    font-size: 14px;
    margin: 0;
}

/* Footer Links */
.footer-links {
    display: flex;
    justify-content: space-between;
    margin: 60px 0 30px;
    padding-top: 30px;
    border-top: 1px solid #282828;
}

.footer-section {
    flex: 1;
    max-width: 200px;
}

.footer-title {
    color: #fff;
    font-size: 16px;
    margin-bottom: 20px;
    font-weight: 500;
}

.footer-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-list li {
    color: #a7a7a7;
    margin-bottom: 12px;
    font-size: 14px;
    cursor: pointer;
    transition: color 0.2s;
}

.footer-list li:hover {
    color: #fff;
}

/* Social Links */
.social-links {
    display: flex;
    gap: 16px;
    margin: 30px 0;
}

.social-icon {
    background: #292929;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    transition: background-color 0.2s;
}

.social-icon:hover {
    background: #333;
}

/* Legal Footer */
.profile-legal {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #282828;
}

.legal-links {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 15px;
}

.legal-links a {
    color: #a7a7a7;
    text-decoration: none;
    font-size: 12px;
    transition: color 0.2s;
}

.legal-links a:hover {
    color: #fff;
}

.copyright {
    color: #a7a7a7;
    font-size: 12px;
    margin-top: 20px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .following-grid {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .footer-links {
        flex-wrap: wrap;
        gap: 30px;
    }
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }
    
    .following-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .footer-links {
        flex-direction: column;
        gap: 40px;
    }
    
    .footer-section {
        max-width: 100%;
    }
}

@media (max-width: 480px) {
    .profile-info {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .following-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Settings Section Styles */
.settings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    margin-bottom: 20px;
}

.settings-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #fff;
    margin: 0;
}

.search-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    cursor: pointer;
}

.search-icon i {
    color: #fff;
    font-size: 16px;
}

.settings-group {
    margin-bottom: 40px;
}

.settings-category {
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 20px;
}

.settings-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.setting-label {
    font-size: 14px;
    color: #fff;
}

.edit-button {
    background-color: transparent;
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.2s;
    display: flex;
    align-items: center;
}

.edit-button i {
    margin-left: 5px;
}

.edit-button:hover {
    transform: scale(1.05);
    background-color: rgba(255, 255, 255, 0.1);
}

/* Dropdown Select */
.dropdown-select {
    position: relative;
    width: 240px;
}

.dropdown-select select {
    width: 100%;
    background-color: #333;
    color: #fff;
    padding: 12px 16px;
    border: none;
    border-radius: 4px;
    appearance: none;
    font-size: 14px;
    cursor: pointer;
}

.dropdown-select i {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #fff;
    pointer-events: none;
}

/* Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #535353;
    transition: 0.4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #1DB954;
}

input:checked + .toggle-slider:before {
    transform: translateX(22px);
}

/* Footer styling for Settings */
.settings-legal {
    border-top: 1px solid #333;
    padding-top: 20px;
    margin-top: 30px;
}

.legal-links {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.legal-links a {
    color: #aaa;
    text-decoration: none;
    font-size: 12px;
    margin-right: 20px;
    margin-bottom: 10px;
}

.legal-links a:hover {
    color: #fff;
    text-decoration: underline;
}

.copyright {
    color: #aaa;
    font-size: 12px;
}
</style>

<script>
   function uploadProfileImage() {
    const form = document.getElementById('uploadForm');
    const fileInput = document.getElementById('profileUpload');

    if (fileInput && fileInput.files.length > 0) {
        form.submit();
    } else {
        alert("Veuillez sélectionner une image avant de soumettre.");
    }
}


// Profile Toggle Functions
function openProfileSection() {
    document.getElementById('box2-main').style.display = 'none';
    document.getElementById('settingsSection').style.display = 'none';
    document.getElementById('profileSection').style.display = 'block';
    window.scrollTo(0, 0);
}

function closeProfileSection() {
    document.getElementById('profileSection').style.display = 'none';
    document.getElementById('box2-main').style.display = 'block';
}

function toggleDropdown() {
        document.getElementById("dropdownMenu").classList.toggle("show");
    }

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.dropdown-button') && !event.target.matches('.dropdown-button *')) {
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

// Function to show settings section
function showSettingsSection() {
    // Hide other sections
    document.getElementById('profileSection').style.display = 'none';
    // Show settings section
    document.getElementById('settingsSection').style.display = 'block';
    document.getElementById('box2-main').style.display = 'none';
}

// Function to hide settings section
function closeSettingsSection() {
    document.getElementById('settingsSection').style.display = 'none';
    // You can show another section or main content here
}
</script>
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
    
          <div class="box-2" id="box2-main">
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
                </div>

            </div>
            <div class="carousel-container">
                <div class="section_title">
                    <span id="tendance">Artiste recommandés pour vous</span>
                    <a href="" style="color:rgb(132, 129, 129);" id="show-all-link"  onclick="toggleBox2(event)" ><span>Tout afficher</span></a>                    
                </div>

                <div class="albums-wrapper">
                    <div class="albums-container">
                        <?php foreach (array_slice($allartiste, 0, 10) as $artiste): ?>
                            <div class="album-item">
                                <?php
                                    $base_path = "/projetweb/assets/includes/";
                                    $image_path = str_replace("C:\\xampp\\htdocs", "", $artiste['image_path']); // Remove C:\xampp\htdocs
                                    $image_path = str_replace("\\", "/", $image_path); // Replace backslashes with forward slashes
                                    
                                ?>
                                <div class="artiste-img-container" onclick="showAlbumModal('<?php echo $image_path; ?>')">
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

        <div id="boxoption" style="">
            <div class="player-options" style="">
                <div class="option">
                    <i class="fa-solid fa-music"onclick="addNewDiv()"></i>
                </div>
                <div class="option"  >
                    <i id="volume-icon" class="fas fa-volume-up"></i>
                </div>
                <div class="volume-bar" style="margin:5px 0px 0px 0px;">
                    <div class="volume-current" style=""></div>
                    <div class="volume-dot" style="position: absolute; width: 15px; height: 15px; background-color: white; border-radius: 50%; cursor: pointer; top: -5px; "></div> <!-- The draggable dot -->

                </div>
                <div class="option"><i class="fas fa-expand"></i></div>
            </div>
        </div>

        <!-- Hidden Audio Tag -->
        <audio id="audioPlayer"></audio>
        </div>


           <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Profile details</h2>
                <span class="close-button" onclick="closeEditProfileModal()">×</span>
            </div>
            <div class="modal-body">
                <div class="profile-image-upload" onclick="document.getElementById('profileUpload').click();">
                    <?php if (!empty($user->getImagePath())): ?>
                        <img src="<?= htmlspecialchars($user->getImagePath()) . '?v=' . time() ?>" alt="Profile Picture">
                    <?php else: ?>
                        <div class="placeholder-image">
                            <!-- User icon placeholder -->
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="profile-input-field">
                    <input type="text" id="nom_utilisateur" value="<?= htmlspecialchars($user->getNomUtilisateur()) ?>">
                </div>
                
                <div class="disclaimer">
                    <p>By proceeding, you agree to give Spotify access to the image you choose to upload. Please make sure you have the right to upload the image.</p>
                </div>
                
                <div class="modal-actions">
                    <button class="save-button" onclick="saveProfileChanges()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
    
    function openEditProfileModal() {
        document.getElementById('editProfileModal').style.display = 'block';
        document.getElementById('profileMenu').style.display = 'none';
    }
    
    function closeEditProfileModal() {
        document.getElementById('editProfileModal').style.display = 'none';
    }
    
    function copyProfileLink() {
        // Get user ID or username for the profile link
        const userId = '<?= htmlspecialchars($user->getArtisteId()) ?>';
        const profileLink = `${window.location.origin}/profile/${userId}`;
        
        // Copy to clipboard
        navigator.clipboard.writeText(profileLink)
            .then(() => {
                alert('Profile link copied to clipboard!');
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
            });
        
        document.getElementById('profileMenu').style.display = 'none';
    }
    
    function uploadProfileImage() {
        document.getElementById('uploadForm').submit();
    }
    
    function saveProfileChanges() {
    const nomUtilisateur = document.getElementById('nom_utilisateur').value;

    // Crée un formulaire et l'envoie dynamiquement
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'update_username.php';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'nom_utilisateur';
    input.value = nomUtilisateur;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}


</script>
<style>
  /* Profile Options Dropdown */
.profile-options {
    position: relative;
}

.options-button {
    background: none;
    border: none;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
}

.profile-dropdown {
    position: absolute;
    background-color: #282828;
    min-width: 200px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    border-radius: 4px;
}

.dropdown-item {
    padding: 12px 16px;
    display: flex;
    align-items: center;
    color: #fff;
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: #333;
}

.dropdown-item .icon {
    margin-right: 10px;
}


/* Modal Styles */
.modal {
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    /* Move it down a bit by adding this: */
    padding-top: 150px;
    align-items: flex-start;
}

.modal-content {
    background-color: #282828;
    border-radius: 8px;
    width: 400px;
    max-width: 90%;
    color: #fff;
    position: relative;
    margin: auto;
    animation: modalFadeIn 0.3s ease;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
}

@keyframes modalFadeIn {
    from {opacity: 0; transform: translateY(-20px);}
    to {opacity: 1; transform: translateY(0);}
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    border-bottom: 1px solid #333;
}

.modal-header h2 {
    margin: 0;
    font-size: 20px;
}

.close-button {
    cursor: pointer;
    font-size: 24px;
}

.modal-body {
    padding: 20px;
}

.profile-image-upload {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    background-color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
}

.profile-image-upload img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.placeholder-image {
    width: 40px;
    height: 40px;
    background-image: url('path/to/user-icon.svg');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
}

.profile-input-field {
    margin-bottom: 20px;
}

.profile-input-field input {
    width: 100%;
    padding: 12px;
    background-color: #333;
    border: none;
    border-radius: 4px;
    color: #fff;
    font-size: 16px;
}

.disclaimer {
    font-size: 12px;
    color: #aaa;
    margin-bottom: 20px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
}

.save-button {
    background-color: #fff;
    color: #000;
    border: none;
    border-radius: 20px;
    padding: 8px 24px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.save-button:hover {
    background-color: #f0f0f0;
}
</style>
 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script src="js.js"></script>
</body>
</html>