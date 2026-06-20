<?php
require_once '../config/conexion.php';
require_once 'controllers/PerfilEmpresaController.php';

$controller = new PerfilEmpresaController($conexion);
$controller->index();
