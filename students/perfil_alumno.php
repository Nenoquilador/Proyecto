<?php
require_once '../config/conexion.php';
require_once 'controllers/PerfilAlumnoController.php';

$controller = new PerfilAlumnoController($conexion);
$controller->index();
