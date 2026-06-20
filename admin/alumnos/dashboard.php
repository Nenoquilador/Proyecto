<?php
require_once '../../config/conexion.php';
require_once '../controllers/AlumnosDashboardController.php';

$controller = new AlumnosDashboardController($conexion);
$controller->index();
