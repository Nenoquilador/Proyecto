<?php
require_once '../../config/conexion.php';
require_once '../controllers/EmpresasDashboardController.php';

$controller = new EmpresasDashboardController($conexion);
$controller->index();
