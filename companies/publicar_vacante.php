<?php
require_once '../config/conexion.php';
require_once 'controllers/PublicarVacanteController.php';

$controller = new PublicarVacanteController($conexion);
$controller->index();
