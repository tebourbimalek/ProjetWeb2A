<?php
session_start();

require_once  'C:\xampp\htdocs\projetweb\vendor\autoload.php';

$fb = new \Facebook\Facebook([
    'app_id' => '608320592224451',
    'app_secret' => 'c8ccd49f4fe2dc0550f5953eb205c12e',
    'default_graph_version' => 'v18.0',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // Ce que tu veux demander
$callbackUrl = htmlspecialchars('http://localhost/projetweb/View/tunisfy_sans_conexion/LoginMethods/fb-callback.php');
$loginUrl = $helper->getLoginUrl($callbackUrl, $permissions);

header("Location: " . $loginUrl);
exit;
