<?php
require_once '../config/conexion.php';
require_once 'controllers/ProcesarEmpresaController.php';

$controller = new ProcesarEmpresaController($conexion);
$controller->index();
