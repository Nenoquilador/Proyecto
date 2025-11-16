<?php
// admin/gestionar_empresa.php
// Script para aprobar o rechazar solicitudes de registro de empresas

session_start();

// 1. SEGURIDAD: Solo permite acceso al administrador
if (!isset($_SESSION['id_admin'])) {
    header("Location: ../login.php"); 
    exit();
}

// RUTA CORREGIDA
include '../config/conexion.php'; 

$id_empresa = $_GET['id'] ?? null;
$accion = $_GET['accion'] ?? null;
$message = '';
$status = 'error'; // Por defecto, si algo sale mal

// 2. Validación de datos
if (empty($id_empresa) || !is_numeric($id_empresa) || !in_array($accion, ['aprobar', 'rechazar'])) {
    $message = "Error: Parámetros inválidos para gestionar la empresa.";
} else {
    
    // 3. Determinar el nuevo estado
    $nuevo_estado = ($accion === 'aprobar') ? 'aprobada' : 'rechazada';
    
    try {
        // 4. Actualizar la base de datos
        $sql = "UPDATE Empresas SET estado_validacion = :nuevo_estado WHERE id_empresa = :id_empresa";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nuevo_estado', $nuevo_estado);
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $message_log = ($accion === 'aprobar') ? "aprobada" : "rechazada";
            $message = "Empresa ID $id_empresa $message_log con éxito.";
            $status = 'success';
            
            // Si quieres que el Administrador vea el nombre de la empresa, puedes añadir una consulta de SELECT
            // antes de la redirección para obtener el nombre y mostrarlo en el mensaje.
        } else {
            $message = "Error al ejecutar la actualización.";
        }

    } catch (PDOException $e) {
        $message = "Error de base de datos: " . $e->getMessage();
    }
}

// 5. Redirige de vuelta al dashboard con un mensaje de estado
header("Location: dashboard_admin.php?status=$status&msg=" . urlencode($message));
exit();

// NOTA: Este script no tiene HTML y debe residir en admin/
?>