<?php
require_once '../config/conexion.php';
require_once 'controllers/RegistroAlumnoController.php';

$controller = new RegistroAlumnoController($conexion);
$controller->index();
