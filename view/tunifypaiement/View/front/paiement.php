<?php
  // Check if the "type" parameter is in the URL
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="image/logo1.png" alt="logo">
        <a href="paiement.html"><i class="fas fa-credit-card"></i><span>Paiements</span></a>
    </div>
    <div class="user-profile">
        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <form action="add.php" method="post" id="payment-form">
            <input type="hidden" name="type_abonnement" value="<?php echo $type; ?>">
            <h1>Paiement</h1>
            <div class="payment-container">
                <h2>Choisissez un mode de paiement</h2>

                <!-- Carte Bancaire -->
                <div class="payment-option">
                    <input type="radio" id="payment-method-card" name="payment-method" value="card" onchange="togglePaymentForms()">
                    <label for="payment-method-card"><i class="fas fa-credit-card"></i> Carte Bancaire</label>
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
                    
                    <div class="form-group">
<<<<<<< HEAD
                        <label for="expiry-date">Date d'expiration (MM/AA):</label>
                        <input type="text" id="expiry-date" name="expiry-date" placeholder="MM/AA" maxlength="5">
                        <div id="expiry-date-error" style="color: red; display: none;"></div>
                    </div>
=======
                    <label for="expiry-date">Date d'expiration (JJ/MM/AAAA):</label>
                    <input type="text" id="expiry-date" name="expiry-date" placeholder="JJ MM AAAA" maxlength="10">
                    <div id="expiry-date-error" style="color: red; display: none;"></div>
                                  </div>
>>>>>>> 628366a (cruuud)
                    
                    <div class="form-group">
                        <label for="security-code">Code de sécurité (CVC):</label>
                        <input type="tel" id="security-code" name="security-code" placeholder="XXX" maxlength="3">
                        <div id="security-code-error" style="color: red; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="card-type">Type de carte:</label>
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
                    <label for="payment-method-mobile"><i class="fas fa-mobile-alt"></i> Paiement Mobile</label>
                </div>
                <div class="payment-images" style="text-align: center;">
                    <img src="image/orange.png" alt="Orange" width="30">
                    <img src="image/tt.png" alt="TT" width="32">
                </div>
                <div class="form-container" id="mobile-form" style="display: none;">
                    <h3>Information Paiement Mobile</h3>
                    <div class="form-group">
                        <label for="phone-number">Numéro de téléphone:</label>
                        <input type="tel" id="phone-number" name="phone-number" placeholder="12 345 678" maxlength="8">
                        <div id="phone-number-error" style="color: red; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile-provider">Fournisseur Mobile:</label>
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
    
    </script>
    <script src="payment.js"></script>
</body>
</html>