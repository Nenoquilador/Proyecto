<?php
require_once '../../config/conexion.php';
require_once '../controllers/ProcesarAdminController.php';

$controller = new ProcesarAdminController($conexion);
$controller->index();
