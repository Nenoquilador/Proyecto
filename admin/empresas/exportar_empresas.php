<?php
require_once '../../config/conexion.php';
require_once '../controllers/ExportarEmpresasController.php';

$controller = new ExportarEmpresasController($conexion);
$controller->index();
