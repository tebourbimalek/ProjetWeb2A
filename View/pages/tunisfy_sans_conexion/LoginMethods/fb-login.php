<?php
session_start();

require_once __DIR__ . '/../../../../vendor/autoload.php';

$fb = new \Facebook\Facebook([
    'app_id' => '700940239177411',
    'app_secret' => 'e6716470410136e4db29cd68a8f66a88',
    'default_graph_version' => 'v18.0',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // Ce que tu veux demander
$callbackUrl = htmlspecialchars('http://localhost/projetweb/View/pages/tunisfy_sans_conexion/LoginMethods/fb-callback.php');
$loginUrl = $helper->getLoginUrl($callbackUrl, $permissions);

header("Location: " . $loginUrl);
exit;
