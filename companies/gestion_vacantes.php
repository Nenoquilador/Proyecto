<?php
require_once '../config/conexion.php';
require_once 'controllers/GestionVacantesController.php';

$controller = new GestionVacantesController($conexion);
$controller->index();
