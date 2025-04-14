<?php
session_start();
session_destroy();
header("Location: /projetweb/pages/tunisfy_sans_conexion/page_sans_connexion.php");
exit;
