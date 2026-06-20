<?php
require_once '../config/conexion.php';
require_once 'controllers/VerPostulacionesController.php';

$controller = new VerPostulacionesController($conexion);
$controller->index();
