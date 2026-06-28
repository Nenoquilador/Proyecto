<?php
require_once '../../config/conexion.php';
require_once '../controllers/TramitesSSAlumnosController.php';

$controller = new TramitesSSAlumnosController($conexion);
$controller->index();
