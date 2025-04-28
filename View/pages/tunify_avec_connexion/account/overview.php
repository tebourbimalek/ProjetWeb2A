<?php
session_start();
// Start session if not already started


// Redirection si l'utilisateur n'est pas connecté
// Include the file where requireLogin is defined
require_once 'C:\xampp\htdocs\projetweb\controller\controller.php';

// Call the function
requireLogin();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte - Tunify</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="acc.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img src="/projetweb/assets/img/logo.png" alt="Logo">
            </div>
            <div class="nav-links">
                <a href="#">Premium</a>
                <a href="#">Assistance</a>
                <a href="#">Télécharger</a>
                <span>|</span>
            </div>
            <div class="profile-menu">
                <button class="profile-button">
                    <i class="fas fa-user-circle"></i>
                    Profil
                    <i class="fas fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <a href="#">Compte</a>
                    <a href="/projetweb/View/pages/tunify_avec_connexion/logout.php">Déconnexion</a>
                </div>
            </div>
        </header>

        <div class="search-container">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Rechercher un compte ou des articles d'aide">
            </div>
        </div>

        <div class="subscription-info">
            <div class="subscription-header">
                <h2>Votre abonnement</h2>
                <img class="logo" src="/projetweb/assets/img/logo.png" alt="Logo">
            </div>
            <h3 class="subscription-title">Tunify sans abonnement</h3>
        </div>

        <!-- Compte Section -->
        <div class="settings-section">
            <h2 class="settings-header">Compte</h2>
            
            <div class="settings-item" onclick="window.location.href='overview.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="settings-content">
                        <div>Gérer votre abonnement</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="settings-item" onclick="window.location.href='profil.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-pen"></i>
                    </div>
                    <div class="settings-content">
                        <div>Modifier le profil</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="settings-item" onclick="window.location.href='restore-playlists.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-sync"></i>
                    </div>
                    <div class="settings-content">
                        <div>Restaurer des playlists</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Paiement Section -->
        <div class="settings-section">
            <h2 class="settings-header">Paiement</h2>
            
            <div class="settings-item" onclick="window.location.href='order-history.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="settings-content">
                        <div>Historique des commandes</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="settings-item" onclick="window.location.href='payment-methods.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="settings-content">
                        <div>Cartes de paiement sauvegardées</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="settings-item" onclick="window.location.href='redeem-code.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="settings-content">
                        <div>Activer un code</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Sécurité et confidentialité Section -->
        <div class="settings-section">
            <h2 class="settings-header">Sécurité et confidentialité</h2>
            
            <div class="settings-item" onclick="window.location.href='manage-apps.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div class="settings-content">
                        <div>Gérer les applis</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="settings-item" onclick="window.location.href='notification-settings.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="settings-content">
                        <div>Paramètres de notification</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="settings-item" onclick="window.location.href='privacy-settings.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="settings-content">
                        <div>Confidentialité du compte</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="settings-item" onclick="window.location.href='login-methods.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="settings-content">
                        <div>Modifier les méthodes de connexion</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="settings-item" onclick="window.location.href='change-password.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="settings-content">
                        <div>Choisir un mot de passe</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <div class="settings-item" onclick="window.location.href='logout-everywhere.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="settings-content">
                        <div>Se déconnecter de tous les appareils</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        </div>
        
        <!-- Aide Section -->
        <div class="settings-section">
            <h2 class="settings-header">Aide</h2>
            
            <div class="settings-item" onclick="window.location.href='support.php'">
                <div class="settings-left">
                    <div class="settings-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="settings-content">
                        <div>Service d'assistance Spotify</div>
                    </div>
                </div>
                <div class="arrow-icon">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        </div>

        <footer>
            <div class="footer-content">
                <div class="footer-column">
                    <img src="/projetweb/assets/img/logo.png" alt="Logo" style="width: 130px;">
                </div>
                <div class="footer-column">
                    <h3 class="footer-heading">SOCIÉTÉ</h3>
                    <ul class="footer-links">
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Offres d'emploi</a></li>
                        <li><a href="#">For the Record</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-heading">COMMUNAUTÉS</h3>
                    <ul class="footer-links">
                        <li><a href="#">Espace artistes</a></li>
                        <li><a href="#">Développeurs</a></li>
                        <li><a href="#">Campagnes publicitaires</a></li>
                        <li><a href="#">Investisseurs</a></li>
                        <li><a href="#">Fournisseurs</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-heading">LIENS UTILES</h3>
                    <ul class="footer-links">
                        <li><a href="#">Assistance</a></li>
                        <li><a href="#">Lecteur Web</a></li>
                        <li><a href="#">Appli mobile gratuite</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-heading">ABONNEMENTS TUNIFY</h3>
                    <ul class="footer-links">
                        <li><a href="#">Premium Personnel</a></li>
                        <li><a href="#">Premium Duo</a></li>
                        <li><a href="#">Premium Famille</a></li>
                        <li><a href="#">Tunify Free</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <div class="social-links">
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-legal">
                    <a href="#">Légal</a>
                    <a href="#">Centre de sécurité et de confidentialité</a>
                    <a href="#">Protection des données</a>
                    <a href="#">Cookies</a>
                    <a href="#">À propos des pubs</a>
                    <a href="#">Accessibilité</a>
                </div>
                <div class="language-selector">
                    <i class="fas fa-globe"></i>
                    <span>Tunisie (français)</span>
                </div>
                <div>© 2025 Tunify AB</div>
            </div>
        </footer>
    </div>

    <script src="acc.js"></script>
</body>
</html>