<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\controller\controller.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\user.php';

$message = '';
$pdo = config::getConnexion();
redirectIfLoggedIn();

if (!$pdo) {
    die("Database connection failed!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['mot_de_passe'] ?? '';

    // Vérification du CAPTCHA
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $recaptchaSecret = '6LdW2CMrAAAAANupaHMcu0rHjj0hRQ3qYDZ77wv1'; // Secret Key

    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
    $captcha = json_decode($verify);

    if (!$captcha->success) {
        $message = "❌ Veuillez valider le reCAPTCHA.";
    } else {
        error_log("Login attempt with: ".$email." / ".$password);
        $message = loginUser($pdo, $email, $password);

        if (isset($_SESSION['user'])) {
            error_log("Login successful, redirecting...");
            header("Location: avec_connexion.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Tunify</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="login-container">
        <img src="/projetweb/assets/img/logo.png" alt="Tunify Logo" class="logo-image">
        <h1 class="logo">Tunify</h1>
        <h2>Se connecter à Tunify</h2>

        <?php if (!empty($message)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <label for="email">Adresse e-mail ou nom d'utilisateur</label>
            <input type="text" id="email" name="email" required autocomplete="username" placeholder="Email ou nom d'utilisateur">

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required autocomplete="current-password" placeholder="Mot de passe">

            <!-- reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6LdW2CMrAAAAAN6U7fNYDx52aQYCdKd-cx-7-cBi"></div>

            <div class="options">
                <label>
                    <input type="checkbox" name="remember"> Se souvenir de moi
                </label>
                <a href="forgot_password.php">Mot de passe oublié ?</a>
            </div>

            <button type="submit">SE CONNECTER</button>

            <p class="divider">OU</p>

            <button class="social-login" type="button" data-provider="google">
                Continuer avec Google
            </button>

            <button class="social-login" type="button" data-provider="facebook">
                Continuer avec Facebook
            </button>

            <p class="register">
                Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous</a>
            </p>
        </form>
    </div>

    <script>
        // Bloque la soumission si reCAPTCHA pas coché
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const recaptcha = grecaptcha.getResponse();
            if (recaptcha.length === 0) {
                e.preventDefault();
                alert("⚠️ Vous devez cocher 'Je ne suis pas un robot' !");
            }
        });

        // Animation de clic sur boutons
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('click', function (e) {
                    const x = e.clientX - this.getBoundingClientRect().left;
                    const y = e.clientY - this.getBoundingClientRect().top;

                    const ripple = document.createElement('span');
                    ripple.className = 'ripple';
                    ripple.style.left = `${x}px`;
                    ripple.style.top = `${y}px`;

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });
    </script>
</body>
</html>
