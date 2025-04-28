<?php
session_start();

// Identifiants statiques
define('STATIC_EMAIL', 'admin@tunify.com');
define('STATIC_PASSWORD', 'admin123');

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Récupérer les données du formulaire
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Vérifier les identifiants statiques
            if ($email === STATIC_EMAIL && $password === STATIC_PASSWORD) {
                // Créer la session
                $_SESSION['user_id'] = 1;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = 'Admin';

                // Rediriger vers le backoffice
                header('Location: /projetweb_wassim/projet/app/views/pages/backoffice/backoffice.php');
                exit();
            } else {
                $_SESSION['error'] = "Email ou mot de passe incorrect";
                header('Location: /projetweb_wassim/projet/app/views/auth/login.php');
                exit();
            }
        }
    }

    public function logout() {
        // Détruire toutes les variables de session
        session_unset();
        session_destroy();
        
        // Rediriger vers la page de connexion
        header('Location: /projetweb_wassim/projet/app/views/auth/login.php');
        exit();
    }
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();
    $auth->login();
}
?> 