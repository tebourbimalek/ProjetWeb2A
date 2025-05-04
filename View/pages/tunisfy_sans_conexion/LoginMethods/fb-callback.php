<?php
session_start();

require_once '../../../../vendor/autoload.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';

$fb = new \Facebook\Facebook([
    'app_id' => '700940239177411',
    'app_secret' => 'e6716470410136e4db29cd68a8f66a88',
    'default_graph_version' => 'v18.0',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    die('Graph returned an error: ' . $e->getMessage());
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    die('Facebook SDK returned an error: ' . $e->getMessage());
}

if (!isset($accessToken)) {
    die('Erreur : Aucun token reçu');
}

try {
    $response = $fb->get('/me?fields=id,name,email,picture', $accessToken);
    $fbUser = $response->getGraphUser();

    $email = $fbUser->getEmail();
    $name = $fbUser->getName();
    $pictureUrl = $fbUser->getPicture()->getUrl();

    // Create a unique filename
    $filename = 'profile_' . uniqid() . '.jpg';

    // Define absolute upload path
    $absolutePath = 'C:/xampp/htdocs/projetweb/View/pages/tunify_avec_connexion/uploads/profile_images/' . $filename;

    // Define relative DB path
    $relativePath = 'uploads/profile_images/' . $filename;

    // Ensure the directory exists
    if (!is_dir(dirname($absolutePath))) {
        mkdir(dirname($absolutePath), 0755, true);
    }

    // Download and save the image locally
    $imageContents = file_get_contents($pictureUrl);
    file_put_contents($absolutePath, $imageContents);

    // Database logic
    $pdo = config::getConnexion();

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
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
                image_path
            ) VALUES (?, ?, ?, ?, ?, 'user', ?)
        ");

        $insertStmt->execute([
            $name,
            $email,
            'facebook-oauth',
            $prenom,
            $nom_famille,
            $relativePath // ✅ Store relative path here
        ]);

        $userId = $pdo->lastInsertId();
        $stmt->execute([$email]);
        $user = $stmt->fetch();
    }

    $_SESSION['user'] = $user['artiste_id'];

    header("Location: ../../tunify_avec_connexion/avec_connexion.php");
    exit;

} catch (Exception $e) {
    die('Erreur Facebook : ' . $e->getMessage());
}
?>
