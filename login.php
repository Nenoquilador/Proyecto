<?php
require_once 'config/conexion.php'; 
require_once 'config/Security.php';
require_once 'controllers/LoginController.php';

$controller = new LoginController($conexion);
$controller->handleRequest();
?>