<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/CargaMasivaAlumnosController.php';

$controller = new CargaMasivaAlumnosController($conexion);
$controller->index();
?>
