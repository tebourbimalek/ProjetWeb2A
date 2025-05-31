<?php
session_start();

require_once 'C:\xampp\htdocs\projetweb\vendor\autoload.php';
require_once 'C:\xampp\htdocs\projetweb\model\config.php';

// Désactivation temporaire de la vérification SSL (développement uniquement)
$guzzleClient = new \GuzzleHttp\Client([
    'verify' => false
]);

// Configuration du client Google
$client = new Google_Client();
$client->setHttpClient($guzzleClient);
$client->setClientId('616156464602-sfamde9mshte75jn0qe4cgjgujtj9shr.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/projetweb/View/tunisfy_sans_conexion/LoginMethods/google-callback.php');
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

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Traitement du nom
        $nameParts = preg_split('/\s+/', trim($name), 2);
        $prenom = $nameParts[0] ?? 'Utilisateur';
        $nom_famille = $nameParts[1] ?? '';

        // Télécharger l'image de profil
        $imageContent = file_get_contents($picture);
        $imageExtension = pathinfo(parse_url($picture, PHP_URL_PATH), PATHINFO_EXTENSION);
        $imageExtension = strtolower($imageExtension);

        // Si pas d'extension valide, utiliser jpg par défaut
        if (!in_array($imageExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageExtension = 'jpg';
        }

        $imageName = uniqid('profile_', true) . '.' . $imageExtension;
        $imagePath = "C:/xampp/htdocs/projetweb/View/tunify_avec_connexion/uploads/profile_images/" . $imageName;
        $imagePathRelative = "C:/xampp/htdocs/projetweb/View/tunify_avec_connexion/uploads/profile_images/" . $imageName;

        // Sauvegarde de l'image
        file_put_contents($imagePath, $imageContent);

        // Insertion de l'utilisateur dans la base
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
            $imagePathRelative
        ]);

        // Rechargement des données de l'utilisateur
        $stmt->execute([$email]);
        $user = $stmt->fetch();
    }

    // Création de la session
    $_SESSION['user'] = $user['artiste_id'];

    // Redirection
    header("Location: ../../tunify_avec_connexion/avec_connexion.php");
    exit;

} catch (PDOException $e) {
    die("Erreur base de données : " . $e->getMessage());
} catch (Exception $e) {
    die("Erreur système : " . $e->getMessage());
}
