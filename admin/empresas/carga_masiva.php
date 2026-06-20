<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/CargaMasivaEmpresasController.php';

$controller = new CargaMasivaEmpresasController($conexion);
$controller->index();
?>
