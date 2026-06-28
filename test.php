<?php
require_once 'config/conexion.php';
$sql = "SELECT t.*, a.nombre, a.apellidos, a.matricula, a.carrera,
               v.titulo as vacante_titulo, e.nombre_empresa as empresa_bd_nombre
        FROM tramites_servicio_social t
        JOIN alumnos a ON t.id_alumno = a.id_alumno
        JOIN postulaciones p ON t.id_postulacion = p.id_postulacion
        JOIN vacantes v ON p.id_vacante = v.id_vacante
        JOIN empresas e ON v.id_empresa = e.id_empresa
        ORDER BY t.fecha_solicitud DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
