<?php
require_once '../../config/conexion.php';
require_once '../controllers/AlumnosController.php';

$controller = new AlumnosController($conexion);
$controller->index();
