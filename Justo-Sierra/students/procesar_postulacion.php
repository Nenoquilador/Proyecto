<?php
// ---------------------------------
// LÓGICA PHP (PROCESAR POSTULACIÓN)
// ---------------------------------
session_start();

// 1. SEGURIDAD: Verificar que el alumno esté logueado
if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
    header("Location: ../login.php"); 
    exit();
}

try {
    require_once '../config/conexion.php'; 
} catch (\Throwable $th) {
    die("Error crítico: No se pudo cargar la configuración de la base de datos.");
}

$id_alumno = $_SESSION['id_alumno'];
$id_vacante = $_GET['id'] ?? null;

// Validar ID de la vacante
if (!$id_vacante || !is_numeric($id_vacante)) {
    header("Location: dashboard.php");
    exit();
}

$mensaje = "Tu postulación ha sido enviada con éxito. Serás redirigido en 3 segundos.";
$exito = true;

try {
    // 2. VERIFICAR SI YA SE POSTULÓ
    $sql_check = "SELECT COUNT(*) FROM Postulaciones WHERE id_alumno = :id_alumno AND id_vacante = :id_vacante";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt_check->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->fetchColumn() > 0) {
        $mensaje = "Ya te habías postulado a esta vacante anteriormente. No se procesó una nueva.";
        $exito = true; // Se considera éxito si no hay error de BD, aunque no se inserta
    } else {
        // 3. INSERTAR POSTULACIÓN
        // Asumiendo que las columnas son: id_alumno, id_vacante, estado_postulacion, fecha_postulacion
        $sql_insert = "INSERT INTO Postulaciones (id_alumno, id_vacante, estado_postulacion, fecha_postulacion) 
                       VALUES (:id_alumno, :id_vacante, 'enviada', NOW())";
        
        $stmt_insert = $conexion->prepare($sql_insert);
        $stmt_insert->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt_insert->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
        $stmt_insert->execute();

        // 4. Redirigir al dashboard
        header("refresh:3;url=dashboard.php");
    }

} catch (PDOException $e) {
    $mensaje = "Error de base de datos al procesar la postulación: " . $e->getMessage();
    $exito = false;
    // Si falla, redirigimos más lento o nos quedamos para mostrar el error
    header("refresh:5;url=detalle_vacante.php?id=" . $id_vacante);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Procesando Postulación</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body class="centrado"> 
    
    <div class="form-card" style="text-align: center;"> 
        <h1 style="color: var(--color-js-rojo-principal); font-size: 1.8rem;">
            <?php echo $exito ? '✅ Postulación Exitosa' : '❌ Error de Postulación'; ?>
        </h1>
        
        <div class='mensaje <?php echo $exito ? 'exito' : 'error'; ?>' style="margin-top: 20px;">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
        
        <p style="margin-top: 15px; color: var(--color-texto-secundario);">
            Serás redirigido automáticamente. Si no lo haces, haz clic <a href="dashboard.php">aquí</a>.
        </p>
    </div>

</body>
</html>