<?php
require_once 'C:\xampp\htdocs\projetweb\vendor\autoload.php';

$client = new Google_Client();
$client->setClientId('616156464602-sfamde9mshte75jn0qe4cgjgujtj9shr.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/projetweb/View/tunisfy_sans_conexion/LoginMethods/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

$client->setPrompt('select_account');

$login_url = $client->createAuthUrl();
header("Location: $login_url");
exit;
