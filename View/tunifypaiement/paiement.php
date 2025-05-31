<?php
  // Check if the "type" parameter is in the URL
  session_start();
  if (isset($_GET['type'])) {
      $type = htmlspecialchars($_GET['type']); // Secure the output by escaping special characters
  };
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tunify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="payment.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
            <a href="#" class="mot">|Gaming</a>
            <a href="#" class="mot">Reclamation|</a>
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
  
    <!-- Main Content -->
    <div class="main-content" style="  display: flex; justify-content: center; color:white;">
        <form action="add.php" method="post" id="payment-form">
            <input type="hidden" name="type_abonnement" value="<?php echo $type; ?>">
            <h1 style="text-align:center;">Paiement</h1>
            <div class="payment-container">
                <h2>Choisissez un mode de paiement</h2>

                <!-- Carte Bancaire -->
                <div class="payment-option">
                    <input type="radio" id="payment-method-card" name="payment-method" value="card" onchange="togglePaymentForms()">
                    <label for="payment-method-card" style="color:white;"><i class="fas fa-credit-card"></i> Carte Bancaire</label>
                </div>
                <div class="payment-images" style="text-align: center;">
                    <img src="image/carte1.png" alt="carte" width="30">
                    <img src="image/carte2.png" alt="2" width="32">
                </div>
                <div class="form-container" id="card-form" style="display: none;">
                    <h3>Information Carte Bancaire</h3>
                    <div class="card-input">
                        <span class="card-icon"><i class="fas fa-credit-card"></i></span>
                        <input type="text" id="card-number" name="card-number" placeholder="0000 0000 0000 0000" maxlength="19">
                    </div>
                    <div id="card-number-error" style="color: red; display: none;"></div>
                    <br>
                    <div class="form-group">
                    <label for="expiry-date" style="color:white;">Date d'expiration (JJ/MM/AAAA):</label>
                    <input type="text" id="expiry-date" name="expiry-date" placeholder="JJ MM AAAA" maxlength="10">
                    <div id="expiry-date-error" style="color: red; display: none;"></div>
                                  </div>
                    
                    <div class="form-group">
                        <label for="security-code" style="color:white;">Code de sécurité (CVC):</label>
                        <input type="tel" id="security-code" name="security-code" placeholder="XXX" maxlength="3">
                        <div id="security-code-error" style="color: red; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="card-type" style="color:white;">Type de carte:</label>
                        <select id="card-type" name="card-type">
                            <option value="">-- Sélectionner --</option>
                            <option value="visa">Visa</option>
                            <option value="mastercard">MasterCard</option>
                            <option value="amex">American Express</option>
                        </select>
                        <div id="card-type-error" style="color: red; display: none;"></div>
                    </div>
                </div>

                <!-- Paiement Mobile -->
                <div class="payment-option">
                    <input type="radio" id="payment-method-mobile" name="payment-method" value="mobile" onchange="togglePaymentForms()">
                    <label for="payment-method-mobile" style="color:white;"><i class="fas fa-mobile-alt"></i> Paiement Mobile</label>
                </div>
                <div class="payment-images" style="text-align: center;">
                    <img src="image/orange.png" alt="Orange" width="30">
                    <img src="image/tt.png" alt="TT" width="32">
                </div>
                <div class="form-container" id="mobile-form" style="display: none;">
                    <h3>Information Paiement Mobile</h3>
                    <div class="form-group">
                        <label for="phone-number" style="color:white;">Numéro de téléphone:</label>
                        <input type="tel" id="phone-number" name="phone-number" placeholder="12 345 678" maxlength="8">
                        <div id="phone-number-error" style="color: red; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile-provider" style="color:white;">Fournisseur Mobile:</label>
                        <select id="mobile-provider" name="mobile-provider">
                            <option value="">-- Sélectionner --</option>
                            <option value="orange">Orange</option>
                            <option value="ooredoo">Ooredoo</option>
                            <option value="telecom">Tunisie Telecom</option>
                        </select>
                        <div id="mobile-provider-error" style="color: red; display: none;"></div>
                    </div>
                </div>

                <div id="payment-error" style="color: red; display: none; margin-top: 10px;"></div>

                <button id="submit-button" type="submit">Soumettre le paiement</button>
            </div>
        </form>
    </div>

    <script>
    // Fonction pour basculer l'affichage des formulaires de paiement
    function togglePaymentForms() {
        console.log("togglePaymentForms called");
        const cardForm = document.getElementById("card-form");
        const mobileForm = document.getElementById("mobile-form");
        const cardRadio = document.getElementById("payment-method-card");
        const mobileRadio = document.getElementById("payment-method-mobile");

        if (cardRadio && mobileForm && cardForm && mobileRadio) {
            if (cardRadio.checked) {
                cardForm.style.display = "block";
                mobileForm.style.display = "none";
                console.log("Card form displayed");
            } else if (mobileRadio.checked) {
                cardForm.style.display = "none";
                mobileForm.style.display = "block";
                console.log("Mobile form displayed");
            }
        } else {
            console.error("Elements not found for toggling payment forms");
        }
    }
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
    <script src="payment.js"></script>
</body>
</html>