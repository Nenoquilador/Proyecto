<?php
require_once '../../config/conexion.php';
require_once '../controllers/GestionarEmpresaController.php';

$controller = new GestionarEmpresaController($conexion);
$controller->index();
