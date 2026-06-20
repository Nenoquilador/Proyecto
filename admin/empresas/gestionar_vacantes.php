<?php
require_once '../../config/conexion.php';
require_once '../controllers/GestionarVacantesController.php';

$controller = new GestionarVacantesController($conexion);
$controller->index();
