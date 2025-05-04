<?php
session_start();

require_once '../../../../vendor/autoload.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';

// Désactivation temporaire de la vérification SSL (dev seulement)
$guzzleClient = new \GuzzleHttp\Client([
    'verify' => false
]);

// Configuration client Google
$client = new Google_Client();
$client->setHttpClient($guzzleClient);
$client->setClientId('751077706808-2tmvik80mvoibk2u4vi52op4517d19d0.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/projetweb/View/pages/tunisfy_sans_conexion/LoginMethods/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

if (!isset($_GET['code'])) {
    die("Erreur : Code d'autorisation manquant.");
}

try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        throw new Exception($token['error_description'] ?? $token['error']);
    }

    $client->setAccessToken($token);
    $google_oauth = new Google_Service_Oauth2($client);
    $userInfo = $google_oauth->userinfo->get();

    $email = $userInfo->getEmail();
    $name = $userInfo->getName();
    $picture = $userInfo->getPicture();

    $pdo = config::getConnexion();

    // Vérifier si utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Nom complet => prénom + nom
        $nameParts = preg_split('/\s+/', trim($name), 2);
        $prenom = $nameParts[0] ?? 'Utilisateur';
        $nom_famille = $nameParts[1] ?? '';

        $insertStmt = $pdo->prepare("
            INSERT INTO utilisateurs (
                nom_utilisateur,
                email,
                mot_de_passe,
                prenom,
                nom_famille,
                type_utilisateur,
                image_path,
                score
            ) VALUES (?, ?, ?, ?, ?, 'user', ?, 0)
        ");

        $insertStmt->execute([
            $name,
            $email,
            'google-oauth',
            $prenom,
            $nom_famille,
            $picture
        ]);

        // Rechargement pour récupérer les infos complètes
        $userId = $pdo->lastInsertId();
        $stmt->execute([$email]);
        $user = $stmt->fetch();
    }

    // Création de la session
    $_SESSION['user'] = $user['artiste_id'];

    // Redirection vers la page avec connexion
    header("Location: ../../tunify_avec_connexion/avec_connexion.php");
    exit;

} catch (PDOException $e) {
    die("Erreur base de données : " . $e->getMessage());
} catch (Exception $e) {
    die("Erreur système : " . $e->getMessage());
}
