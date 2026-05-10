<?php
// Configuración de la base de datos
$servidor = "localhost";
$usuario = "root";            // Tu usuario de MySQL (generalmente 'root')
$contrasena = "";             // <-- CAMBIO AQUÍ: Comillas vacías
$nombre_db = "bolsa_trabajo_js";

try {
    // Crear la conexión usando PDO
    $conexion = new PDO("mysql:host=$servidor;dbname=$nombre_db", $usuario, $contrasena);
    
    // Configurar PDO para que lance excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Asegurar que la conexión use UTF-8
    $conexion->exec("SET NAMES 'utf8'");

} catch(PDOException $e) {
    // Si la conexión falla, mostrar un error
    die("Error de conexión: " . $e->getMessage());
}
?>