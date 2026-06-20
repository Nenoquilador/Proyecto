<?php
require_once '../config/conexion.php';
require_once 'controllers/DashboardController.php';

$controller = new DashboardController($conexion);
$controller->index();
