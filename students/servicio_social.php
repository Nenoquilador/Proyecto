<?php
require_once '../config/conexion.php';
require_once 'controllers/ServicioSocialController.php';

$controller = new ServicioSocialController($conexion);
$controller->index();
