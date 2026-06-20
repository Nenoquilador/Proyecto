<?php
require_once '../../config/conexion.php';
require_once '../controllers/ListaEmpresasController.php';

$controller = new ListaEmpresasController($conexion);
$controller->index();
