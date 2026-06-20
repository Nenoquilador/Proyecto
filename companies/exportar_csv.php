<?php
require_once '../config/conexion.php';
require_once 'controllers/ExportarCSVController.php';

$controller = new ExportarCSVController($conexion);
$controller->exportar();
