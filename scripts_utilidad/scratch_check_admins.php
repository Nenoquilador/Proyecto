<?php
require_once __DIR__ . '/config/conexion.php';

try {
    $conexion->exec("UPDATE administradores SET password = 'password' WHERE email = 'admin@ujsierra.com.mx'");
    $conexion->exec("UPDATE administradores SET password = 'password' WHERE email = 'escolares@ujsierra.com.mx'");
    echo "¡Contraseñas reseteadas a 'password'!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
