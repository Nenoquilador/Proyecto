<?php
// ---------------------------------
// LÓGICA PHP (DASHBOARD DE EMPRESA)
// ---------------------------------
session_start();

// 1. SEGURIDAD: Verificar que la empresa esté logueada
if (!isset($_SESSION['id_empresa']) || ($_SESSION['rol'] ?? '') !== 'empresa') {
    header("Location: ../login.php"); 
    exit();
}

try {
    require_once '../config/conexion.php'; 
} catch (\Throwable $th) {
    die("Error crítico: No se pudo cargar la configuración de la base de datos.");
}

$id_empresa = $_SESSION['id_empresa'];
$nombre_empresa = $_SESSION['nombre_empresa'] ?? 'Empresa Registrada'; 
$active_vacancies = 0;
$total_applications = 0;
$closed_vacancies = 0;
$error_bd = null; 
$mensaje_subida = '';

// --- LÓGICA PARA SUBIR EL FORMATO Y NOTIFICAR AL ADMIN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['formato_sspp'])) {
    if ($_FILES['formato_sspp']['error'] === UPLOAD_ERR_OK) {
        // Carpeta donde se guardan las respuestas de las empresas
        $dir_subida = '../archivos_sspp/formatos_empresas/';
        if (!file_exists($dir_subida)) {
            mkdir($dir_subida, 0777, true);
        }
        
        $nombre_archivo = time() . '_SSPP_' . basename($_FILES['formato_sspp']['name']);
        $ruta_destino = $dir_subida . $nombre_archivo;
        $ruta_relativa = 'archivos_sspp/formatos_empresas/' . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['formato_sspp']['tmp_name'], $ruta_destino)) {
            
            try {
                // Verificar si ya existe un trámite en la tabla solicitudes_sspp
                $sql_check = "SELECT id_solicitud FROM solicitudes_sspp WHERE id_empresa = :id";
                $stmt_check = $conexion->prepare($sql_check);
                $stmt_check->execute([':id' => $id_empresa]);
                $solicitud_existente = $stmt_check->fetchColumn();

                if ($solicitud_existente) {
                    // Actualizar trámite existente
                    $sql_upd = "UPDATE solicitudes_sspp SET estado_tramite = 'Datos Recibidos', notas_admin = :ruta WHERE id_solicitud = :id_sol";
                    $stmt_upd = $conexion->prepare($sql_upd);
                    $stmt_upd->execute([':ruta' => $ruta_relativa, ':id_sol' => $solicitud_existente]);
                } else {
                    // Crear nuevo trámite
                    $sql_ins = "INSERT INTO solicitudes_sspp (id_empresa, estado_tramite, fecha_inicio, notas_admin) 
                                VALUES (:id_empresa, 'Datos Recibidos', CURDATE(), :ruta)";
                    $stmt_ins = $conexion->prepare($sql_ins);
                    $stmt_ins->execute([':id_empresa' => $id_empresa, ':ruta' => $ruta_relativa]);
                }

                $mensaje_subida = "<div class='mensaje exito'><i class='fas fa-check-circle'></i> ¡Documento enviado con éxito! Vinculación revisará tu información.</div>";
            } catch (PDOException $e) {
                $mensaje_subida = "<div class='mensaje error'>Error en base de datos: " . $e->getMessage() . "</div>";
            }
        } else {
            $mensaje_subida = "<div class='mensaje error'>Error al guardar el archivo. Revisa permisos de carpeta.</div>";
        }
    } else {
        $mensaje_subida = "<div class='mensaje error'>Error en el archivo subido.</div>";
    }
}

// 2. OBTENER ESTADO Y ESTADÍSTICAS
try {
    // Nota: Usamos nombres de tabla en minúsculas para coincidir con tu phpMyAdmin
    $sql_estado = "SELECT estado_validacion FROM empresas WHERE id_empresa = :id";
    $stmt_estado = $conexion->prepare($sql_estado);
    $stmt_estado->bindParam(':id', $id_empresa, PDO::PARAM_INT);
    $stmt_estado->execute();
    $estado_empresa = $stmt_estado->fetchColumn();

    if ($estado_empresa === 'aprobada') {
        // Vacantes Activas
        $sql_vacantes = "SELECT COUNT(*) FROM vacantes WHERE id_empresa = :id AND estado = 'abierta'";
        $stmt_vacantes = $conexion->prepare($sql_vacantes); 
        $stmt_vacantes->execute([':id' => $id_empresa]);
        $active_vacancies = $stmt_vacantes->fetchColumn();

        // Postulaciones
        $sql_apps = "SELECT COUNT(p.id_postulacion) FROM postulaciones p JOIN vacantes v ON p.id_vacante = v.id_vacante WHERE v.id_empresa = :id";
        $stmt_apps = $conexion->prepare($sql_apps); 
        $stmt_apps->execute([':id' => $id_empresa]);
        $total_applications = $stmt_apps->fetchColumn();
        
        // Cerradas
        $sql_cerradas = "SELECT COUNT(*) FROM vacantes WHERE id_empresa = :id AND estado = 'cerrada'";
        $stmt_cerradas = $conexion->prepare($sql_cerradas);
        $stmt_cerradas->execute([':id' => $id_empresa]);
        $closed_vacancies = $stmt_cerradas->fetchColumn();
    }
} catch (PDOException $e) {
    $error_bd = "Error de conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Empresa - <?php echo htmlspecialchars($nombre_empresa); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background-color: var(--color-blanco); padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-left: 5px solid var(--color-js-rojo-principal); }
        .stat-card h3 { color: var(--color-js-rojo-secundario); font-size: 1.1rem; margin-bottom: 5px; }
        .stat-card .value { font-size: 2.5rem; font-weight: 700; color: var(--color-texto-principal); }
        .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .bloqueo-panel { background-color: #fff8eb; border: 1px solid #f3c363; border-radius: 10px; padding: 40px; text-align: center; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .bloqueo-panel h2 { color: #b7790b; margin-top: 0; font-size: 1.8rem; }
        .bloqueo-panel p { color: #555; font-size: 1.1rem; margin-bottom: 25px; line-height: 1.6; }
        .download-box { background: #fff; border: 2px dashed #f3c363; padding: 25px; border-radius: 8px; margin: 20px auto; max-width: 550px; transition: 0.3s; }
        .download-box:hover { border-color: var(--color-js-rojo-principal); background-color: #fffaf0; }
    </style>
</head>
<body>
    <nav class="navbar" style="background: var(--color-js-rojo-secundario);">
        <div class="navbar-brand">Portal de Reclutamiento <span class="brand-js">JS</span></div>
        <div class="navbar-links"><a href="../logout.php" class="btn-logout">Cerrar Sesión</a></div>
    </nav>

    <div class="dashboard-container">
        <h1 style="margin-top: 30px; color: var(--color-js-rojo-principal); font-size: 2.2rem;">
            ¡Bienvenido, <?php echo htmlspecialchars($nombre_empresa); ?>!
        </h1>
        <p style="color: var(--color-texto-secundario); margin-bottom: 40px;">
            Centro de control para la gestión de talento de Justo Sierra.
        </p>

        <?php echo $mensaje_subida; ?>
        <?php if ($error_bd): echo "<div class='mensaje error'>$error_bd</div>"; endif; ?>

        <?php if ($estado_empresa === 'pendiente' || $estado_empresa === 'rechazada'): ?>
            <div class="bloqueo-panel">
                <h2><i class="fas fa-lock"></i> Activación de Cuenta Requerida</h2>
                <p>Para publicar vacantes, completa el proceso de registro institucional de Servicio Social y Prácticas Profesionales (SSPP).</p>
                
                <div class="download-box">
                    <h3 style="color: #333; font-size: 1.25rem;"><i class="fas fa-file-word"></i> 1. Descarga el Formato</h3>
                    <p style="font-size: 0.95rem; margin-bottom: 15px;">Descarga la plantilla, llénala con los datos de la institución y fírmala.</p>
                    <a href="../formatos_oficiales/FORMATO REGISTRO EMPRESA SS Y PP.docx" class="boton-principal" style="background-color: #2980b9;" download>
                        <i class="fas fa-download"></i> Descargar FORMATO REGISTRO EMPRESA SS Y PP.docx
                    </a>
                </div>

                <div class="download-box" style="border-color: #3498db; background-color: #f4f9fd;">
                    <h3 style="color: #333; font-size: 1.25rem;"><i class="fas fa-cloud-upload-alt"></i> 2. Sube el Formato Lleno</h3>
                    <p style="font-size: 0.95rem; margin-bottom: 15px;">Sube el archivo aquí para revisión de Vinculación.</p>
                    <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="formato_sspp" accept=".doc,.docx,.pdf" required style="margin-bottom: 15px; padding: 10px; background: white; border-radius: 5px; border: 1px solid #ddd;">
                        <br>
                        <button type="submit" class="boton-principal" style="background-color: #27ae60; width: 100%;">
                            <i class="fas fa-upload"></i> Enviar a Revisión
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="stats-grid">
                <div class="stat-card"><h3>Vacantes Activas</h3><div class="value"><?php echo $active_vacancies; ?></div></div>
                <div class="stat-card"><h3>Postulaciones</h3><div class="value"><?php echo $total_applications; ?></div></div>
                <div class="stat-card"><h3>Vacantes Cerradas</h3><div class="value"><?php echo $closed_vacancies; ?></div></div>
            </div>
            <h2 style="margin-bottom: 25px; color: var(--color-js-rojo-secundario);">Acciones Rápidas</h2>
            <div class="action-grid">
                <a href="publicar_vacante.php" class="boton-principal" style="padding: 20px;"><i class="fas fa-plus"></i> Publicar Vacante</a>
                <a href="gestion_vacantes.php" class="btn-secundario-form" style="padding: 20px;"><i class="fas fa-tasks"></i> Mis Vacantes</a>
                <a href="perfil_empresa.php" class="btn-secundario-form" style="padding: 20px;"><i class="fas fa-user-edit"></i> Mi Perfil</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>