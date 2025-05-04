<?php
require_once '../../../../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('751077706808-2tmvik80mvoibk2u4vi52op4517d19d0.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/projetweb/View/pages/tunisfy_sans_conexion/LoginMethods/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

$login_url = $client->createAuthUrl();
header("Location: $login_url");
exit;
