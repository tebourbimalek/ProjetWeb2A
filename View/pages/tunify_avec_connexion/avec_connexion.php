<?php 
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
session_start();

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user'])) {
    header("Location: /projetweb/View/pages/tunisfy_sans_conexion/page_sans_connexion.php");
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
        color: #333;
        padding: 10px;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: white;
        min-width: 180px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        z-index: 999;
        border-radius: 5px;
        overflow: hidden;
    }

    .dropdown-menu a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-menu a:hover {
        background-color: #f1f1f1;
    }

    .show {
        display: block;
    }

/* Modal styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6); /* Dark background */
    color: #fff;
    font-family: Arial, sans-serif;
}

.modal-content {
    background-color: #1e1e1e; /* Dark background */
    margin: 10% auto;
    padding: 20px;
    width: 80%;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    color: #fff;
}

.close {
    color: #aaa;
    font-size: 30px;
    font-weight: bold;
    position: absolute;
    top: 15px;
    right: 20px;
}

.close:hover,
.close:focus {
    color: #fff;
    text-decoration: none;
    cursor: pointer;
}

h2, h3 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 20px;
}

form input,
form select {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: none;
    border-radius: 5px;
    background-color: #333;
    color: #fff;
}

form label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

form button {
    width: 100%;
    padding: 12px;
    background-color: #1db954;
    border: none;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    margin-bottom: 15px;
}

form button:hover {
    background-color: #1ed760;
}

.user-info img {
    max-width: 150px;
    border-radius: 50%;
    display: block;
    margin: 20px auto;
}

.user-info p {
    margin-bottom: 10px;
}




</style>
<div class="right-section">
    <?php if (isset($_SESSION['user'])): ?>
        <div class="dropdown">
            <button onclick="toggleDropdown()" class="dropdown-button">
                <i class="fas fa-user"></i>
            </button>
            <div id="dropdownMenu" class="dropdown-menu">
                <a href="#"><i class="fas fa-crown"></i> Premium</a>
                <a href="#" onclick="openModal()"><i class="fas fa-user"></i> Profil</a>
                <a href="settings.php"><i class="fas fa-cogs"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
    <?php else: ?>
        <a href="/projetweb/View/pages/tunisfy_sans_conexion/login.php" class="nav-link">Se connecter</a>
        <a href="/projetweb/View/pages/tunisfy_sans_conexion/register.php" class="nav-link">S'inscrire</a>
    <?php endif; ?>
</div>


<!-- Modal -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Your Profile</h3>

        <!-- Affichage de la photo actuelle -->
        <?php if (!empty($user->getImagePath())): ?>
            <div class="form-group" style="text-align: center;">
                <img src="<?php echo htmlspecialchars($user->getImagePath()); ?>" alt="Profile Image" 
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;">
            </div>
        <?php endif; ?>

        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user->getId()); ?>">

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" name="name" 
                       value="<?php echo htmlspecialchars($user->getNomUtilisateur()); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" 
                       value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" name="status" required>
                    <option value="Active" <?php echo $user->getScore() === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $user->getScore() === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <input type="file" class="form-control" name="profile_image" accept="image/*">
            </div>

            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
</div>


<script>
    function toggleDropdown() {
        document.getElementById("dropdownMenu").classList.toggle("show");
    }

    // Fermer le menu si on clique en dehors
    window.onclick = function(event) {
        if (!event.target.closest('.dropdown')) {
            const menu = document.getElementById("dropdownMenu");
            if (menu.classList.contains('show')) {
                menu.classList.remove('show');
            }
        }
    };


       // Open the modal when "Profil" is clicked
       function openModal() {
        document.getElementById('profileModal').style.display = 'block';
    }

    // Close the modal
    function closeModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    // Close the modal if clicked outside of the modal content
    window.onclick = function(event) {
        if (event.target == document.getElementById('profileModal')) {
            closeModal();
        }
    }
</script>
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

      

<script src="js.js"></script>
</body>
</html>