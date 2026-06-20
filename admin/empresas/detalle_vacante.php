<?php
require_once '../../config/conexion.php';
require_once '../controllers/DetalleVacanteController.php';

$controller = new DetalleVacanteController($conexion);
$controller->index();
