<?php
require_once '../config/conexion.php';
require_once 'controllers/RegistroEmpresaController.php';

$controller = new RegistroEmpresaController($conexion);
$controller->index();
