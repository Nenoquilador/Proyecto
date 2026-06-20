<?php
require_once '../../config/conexion.php';
require_once '../controllers/GestionarTramitesController.php';

$controller = new GestionarTramitesController($conexion);
$controller->index();
