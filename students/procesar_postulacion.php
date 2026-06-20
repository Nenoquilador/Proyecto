<?php
require_once '../config/conexion.php';
require_once 'controllers/ProcesarPostulacionController.php';

$controller = new ProcesarPostulacionController($conexion);
$controller->procesar();
