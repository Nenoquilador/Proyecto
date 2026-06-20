<?php
require_once __DIR__ . '/EnvParser.php';

// Cargar variables de entorno si el archivo .env existe
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    EnvParser::load($envPath);
}

// Configuración de la base de datos
$servidor = $_ENV['DB_HOST'] ?? "localhost";
$usuario = $_ENV['DB_USER'] ?? "root";            
$contrasena = $_ENV['DB_PASS'] ?? "";             
$nombre_db = $_ENV['DB_NAME'] ?? "bolsa_trabajo_js";

try {
    // Crear la conexión usando PDO
    $conexion = new PDO("mysql:host=$servidor;dbname=$nombre_db", $usuario, $contrasena);
    
    // Configurar PDO para que lance excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Asegurar que la conexión use UTF-8
    $conexion->exec("SET NAMES 'utf8'");

} catch(PDOException $e) {
    // Si la conexión falla, registrar internamente y mostrar mensaje genérico
    error_log("Error de conexión PDO: " . $e->getMessage());
    die("Error interno del servidor. Por favor, intente más tarde.");
}
?>