<?php
require_once '../../config/conexion.php';
require_once '../controllers/VerPerfilController.php';

$controller = new VerPerfilController($conexion);
$controller->index();
