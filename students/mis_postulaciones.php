<?php
require_once '../config/conexion.php';
require_once 'controllers/MisPostulacionesController.php';

$controller = new MisPostulacionesController($conexion);
$controller->index();
