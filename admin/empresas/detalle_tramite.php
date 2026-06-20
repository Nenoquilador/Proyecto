<?php
require_once '../../config/conexion.php';
require_once '../controllers/DetalleTramiteController.php';

$controller = new DetalleTramiteController($conexion);
$controller->index();
