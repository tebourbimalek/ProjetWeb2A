<?php 


include_once 'C:\xampp\htdocs\projetweb\controlleur\functionpaiments.php';


$id_user = 1;
markNotificationsAsRead($id_user);
header("Location: historiquepaiment.php");
exit;










?>