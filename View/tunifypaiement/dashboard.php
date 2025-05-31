<?php 

session_start();


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tunify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="payment.css">
    
    <style>

    * {
        overflow-y: block;
    }
    </style>

</head>
<body>
    <!-- Sidebar -->
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
                    <button class="icon-searsh" id=""><i class="fa-solid fa-magnifying-glass" style="color: grey;font-size:20px;"></i></button>
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
    </style>
        <div class="right-section">
          
            <?php if (isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <button onclick="toggleDropdown()" class="dropdown-button">
                        <i class="fas fa-user"></i>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu">
                        <a href="../tunify_avec_connexion/user/overview.php" target="_blank">Account <i class="fas fa-external-link-alt external-link"></i></a>
                        <a href="" onclick="toggleBox4(<?= $userdata['artiste_id']; ?>, '<?= $userdata['nom_utilisateur']; ?>', '<?= $userdata['image_path']; ?>')" style="border:none;">Profile</a>
                        <a href="">Support <i class="fas fa-external-link-alt external-link"></i></a>
                        <a href="" onclick="showSettingsSection(); return false;">Settings</a>
                        <a href="../tunify_avec_connexion/logout.php">Log out</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="connect-button">Se connecter</a>
            <?php endif; ?>
        </div>
    </nav>
    </div>
  
      
    <!-- Bannière promotionnelle -->
    <div class="promo-banner">
        <div class="promo-text">
            <h1>Votre musique sans limites.</h1>
            <p>Essayez Tunify Premium pendant 2 mois pour <strong>14,99 TND</strong>.</p>
            <p>Seulement 14,99 TND/mois ensuite. Annulation possible à tout moment.</p>
            <a href="paiement.html" class="promo-button">Obtenir Premium Personnel</a>
        </div>
        <div class="promo-image">
            <img src="image/promo.png" alt="Promo Tunify">
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="">
        <h1 style="text-align:center;">Vivez la différence</h1>
        <p style="text-align:center;"><strong>Passez à Premium et bénéficiez d'un contrôle total sur votre musique. Annulez à tout moment.</strong></p>
    
        <div class="stats">
            <!-- Offre Personnel -->
            <div class="offer-card offer-personnel">
                <h3>Personnel</h3>
                <p><strong>14,99 TND</strong> pour 1 mois</p>
                <p><strong>14,99 TND/1mois</strong> ensuite ⭐</p>
                <hr>
                <ul>
                    <li>1 compte Tunify Premium</li>
                    <li>Annulez à tout moment</li>
                </ul>
                <a href="paiement.php?type=Personnel" class="offer-button">Essayez pendant 1 mois</a>
            </div>
    
            <!-- Offre Familial -->
            <div class="offer-card offer-familial">
                <h3>Familial</h3>
                <p><strong>19,99 TND/1 mois</strong></p>
                <p><strong>19,99 TND/mois</strong> ensuite ⭐</p>
                <hr>
                <ul>
                    <li>Jusqu'à 6 comptes Premium</li>
                    <li>Annulez à tout moment</li>
                </ul>
                <a href="paiement.php?type=Familial" class="offer-button">Essayez pendant 1 mois</a>
            </div>
    
            <!-- Offre Duo -->
            <div class="offer-card offer-duo">
                <h3>Duo</h3>
                <p><strong>16,99 TND</strong> pour 1 mois</p>
                <p><strong>16,99 TND/mois</strong> ensuite ⭐</p>
                <hr>
                <ul>
                    <li>2 comptes Tunify Premium</li>
                    <li>Annulez à tout moment</li>
                </ul>
                <a href="paiement.php?type=Duo" class="offer-button">Essayez pendant 1 mois</a>
            </div>
    
            <!-- Offre Mini -->
            <div class="offer-card offer-mini">
                <h3>Mini</h3>
                <p><strong>5,99 TND</strong> pour  1 semaine</p>
                <hr>
                <ul>
                    <li>1 compte Premium pour mobile uniquement</li>
                    <li>Écoutez jusqu'à 30 titres sur 1 appareil hors connexion</li>
                    <li>Un paiement unique</li>
                </ul>
                <a href="paiement.php?type=Mini" class="offer-button">Essayez pendant 1 semaine</a>
            </div>
        </div>
    </div>
    
    
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Société</h4>
                <ul>
                    <li><a href="#">À propos</a></li>
                    <li><a href="#">Offres d'emploi</a></li>
                    <li><a href="#">Tunify Actus</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Communautés</h4>
                <ul>
                    <li><a href="#">Artistes</a></li>
                    <li><a href="#">Développeurs</a></li>
                    <li><a href="#">Partenariats</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Liens Utiles</h4>
                <ul>
                    <li><a href="#">Assistance</a></li>
                    <li><a href="#">Lecteur Web</a></li>
                    <li><a href="#">Application mobile</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Abonnements Tunify</h4>
                <ul>
                    <li><a href="#">Premium Personnel</a></li>
                    <li><a href="#">Premium Duo</a></li>
                    <li><a href="#">Premium Famille</a></li>
                    <li><a href="#">Tunify Free</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-social">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-facebook"></i></a>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Tunify | Tous droits réservés</p>
        </div>
    </footer>

    <script>
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
    </script>
</body>


</html>