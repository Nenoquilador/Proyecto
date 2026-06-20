<?php
require_once '../config/conexion.php';
require_once 'controllers/ActualizarPerfilController.php';

$controller = new ActualizarPerfilController($conexion);
$controller->index();
