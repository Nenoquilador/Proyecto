<?php
require 'C:\xampp\htdocs\Justo-Sierra\admin\models\EmpresaModel.php';
$c = new PDO('mysql:host=localhost;dbname=bolsa_trabajo_js', 'root', '');
$m = new EmpresaModel($c);
print_r($m->getDatosGraficoCrecimientoEmpresas());
?>
