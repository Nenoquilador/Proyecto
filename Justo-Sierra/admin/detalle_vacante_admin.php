<?php
// ---------------------------------
// LÓGICA PHP (DETALLE VACANTE - ADMIN)
// ---------------------------------
session_start();

// 1. Control de Sesión Admin
if (!isset($_SESSION['id_admin']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Obtener ID de la vacante desde URL
$id_vacante = $_GET['id'] ?? null;
if (!$id_vacante || !is_numeric($id_vacante)) {
    // Si no hay ID o no es número, redirigir a la gestión
    header("Location: gestionar_vacantes_admin.php?status=error&msg=" . urlencode("ID de vacante inválido."));
    exit();
}

// Conexión a BD
include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Vinculación';
$vacante = null;
$error_db = null;

// 2. Obtener Detalles Completos de la Vacante y Empresa
try {
    $sql = "SELECT
                v.id_vacante, v.titulo, v.descripcion, v.ubicacion, v.modalidad, v.tipo_contrato, v.salario_ofrecido, v.fecha_publicacion, v.estado AS estado_vacante,
                e.id_empresa, e.nombre_empresa, e.email_contacto, e.rfc, e.descripcion AS descripcion_empresa, e.sitio_web, e.estado_validacion AS estado_empresa
            FROM
                Vacantes v
            JOIN
                Empresas e ON v.id_empresa = e.id_empresa
            WHERE
                v.id_vacante = :id_vacante";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
    $stmt->execute();
    $vacante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vacante) {
        $error_db = "No se encontró la vacante solicitada.";
    }

} catch (PDOException $e) {
    $error_db = "Error al cargar los detalles de la vacante: " . $e->getMessage();
}

// Función para formatear texto (si no está definida globalmente)
if (!function_exists('formatear_tag')) {
    function formatear_tag($texto) {
        if (empty($texto)) { return "N/A"; }
        $formato = str_replace('_', ' ', $texto);
        return ucwords($formato);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Vacante - <?php echo htmlspecialchars($vacante['titulo'] ?? 'Admin'); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>
        /* Estilos adicionales para la vista de detalle */
        .detail-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--color-borde);
        }
        .detail-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-section h3 {
            color: var(--color-js-rojo-secundario);
            font-size: 1.3rem;
            margin-bottom: 15px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px 25px;
        }
         .detail-item strong {
            display: block;
            font-size: 0.9rem;
            color: var(--color-texto-secundario);
            font-weight: 600;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
         .detail-item span {
            font-size: 1rem;
            color: var(--color-texto-principal);
        }
        .description-text-admin {
             white-space: pre-wrap;
             font-size: 1rem;
             color: var(--color-texto-principal);
             background-color: #f9f9f9;
             padding: 15px;
             border-radius: 6px;
             border: 1px solid var(--color-borde);
             max-height: 300px;
             overflow-y: auto;
        }
         /* Ajuste para los botones de acción al final */
        .detail-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--color-borde);
            display: flex;
            justify-content: space-between; /* Alinea los botones */
            align-items: center;
        }
    </style>

</head>
<body>

    <header class="admin-nav">
        <h2><i class="fas fa-briefcase"></i> Bolsa de Trabajo JS | Vinculación</h2>
        <a href="../logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </header>

    <div class="dashboard-layout">

        <aside class="sidebar">
            <h3>Bienvenido,<br><?php echo htmlspecialchars($nombre_admin); ?></h3>
            <ul>
                <li><a href="dashboard_admin.php"><i class="fas fa-building"></i> Empresas Pendientes</a></li>
                <li><a href="gestionar_vacantes_admin.php" class="active"><i class="fas fa-list-alt"></i> Gestionar Vacantes</a></li>
                <li><a href="gestionar_alumnos.php"><i class="fas fa-users"></i> Gestionar Alumnos</a></li>
            </ul>
        </aside>

        <main class="main-content">

            <?php if ($error_db): ?>
                <div class="mensaje error"><?php echo htmlspecialchars($error_db); ?></div>
            <?php elseif ($vacante): ?>

                <h2>
                    <i class="fas fa-clipboard-list"></i> Detalle: <?php echo htmlspecialchars($vacante['titulo']); ?>
                </h2>
                <p style="margin-bottom: 30px;">
                    Publicado por: <strong><?php echo htmlspecialchars($vacante['nombre_empresa']); ?></strong>
                    (Estado Empresa: <span class="status-badge status-<?php echo strtolower($vacante['estado_empresa']); ?>"><?php echo ucfirst($vacante['estado_empresa']); ?></span>)
                </p>

                <section class="detail-section">
                    <h3>Información de la Vacante</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <strong>Título</strong>
                            <span><?php echo htmlspecialchars($vacante['titulo']); ?></span>
                        </div>
                         <div class="detail-item">
                            <strong>Estado Actual</strong>
                             <span class="status-badge status-<?php echo strtolower($vacante['estado_vacante']); ?>"><?php echo ucfirst($vacante['estado_vacante']); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Tipo de Contrato</strong>
                            <span><?php echo formatear_tag($vacante['tipo_contrato']); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Modalidad</strong>
                            <span><?php echo formatear_tag($vacante['modalidad']); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Ubicación</strong>
                            <span><?php echo htmlspecialchars($vacante['ubicacion']); ?></span>
                        </div>
                        <div class="detail-item">
                            <strong>Salario Ofrecido</strong>
                            <span><?php echo $vacante['salario_ofrecido'] ? '$' . number_format($vacante['salario_ofrecido'], 2) : 'No especificado'; ?></span>
                        </div>
                         <div class="detail-item">
                            <strong>Fecha de Publicación</strong>
                            <span><?php echo date('d/m/Y', strtotime($vacante['fecha_publicacion'])); ?></span>
                        </div>
                    </div>
                </section>

                <section class="detail-section">
                     <h3>Descripción y Requisitos</h3>
                     <div class="description-text-admin">
                         <?php echo nl2br(htmlspecialchars($vacante['descripcion'])); ?>
                     </div>
                </section>

                <section class="detail-section">
                    <h3>Información de la Empresa</h3>
                     <div class="detail-grid">
                        <div class="detail-item">
                            <strong>Nombre Empresa</strong>
                            <span><?php echo htmlspecialchars($vacante['nombre_empresa']); ?></span>
                        </div>
                         <div class="detail-item">
                            <strong>Email Contacto</strong>
                            <span><?php echo htmlspecialchars($vacante['email_contacto']); ?></span>
                        </div>
                         <div class="detail-item">
                            <strong>RFC</strong>
                            <span><?php echo htmlspecialchars($vacante['rfc'] ?? 'N/A'); ?></span>
                        </div>
                         <div class="detail-item">
                            <strong>Sitio Web</strong>
                            <span><a href="<?php echo htmlspecialchars($vacante['sitio_web']); ?>" target="_blank"><?php echo htmlspecialchars($vacante['sitio_web']); ?></a></span>
                        </div>
                    </div>
                </section>

                <div class="detail-actions">
                     <a href="gestionar_vacantes_admin.php" class="btn-secundario-form">
                         <i class="fas fa-arrow-left"></i> Regresar
                     </a>

                     <div> <?php if ($vacante['estado_vacante'] === 'abierta'): ?>
                            <a href="procesar_admin.php?action=cerrar_vacante&id=<?php echo $vacante['id_vacante']; ?>" class="action-close" onclick="return confirm('¿Seguro que deseas cerrar esta vacante?');">
                                <i class="fas fa-times-circle"></i> Cerrar Vacante
                            </a>
                        <?php else: ?>
                             <a href="procesar_admin.php?action=abrir_vacante&id=<?php echo $vacante['id_vacante']; ?>" class="action-download" onclick="return confirm('¿Seguro que deseas reabrir esta vacante?');">
                                <i class="fas fa-check-circle"></i> Reabrir Vacante
                            </a>
                        <?php endif; ?>
                     </div>
                 </div>

            <?php endif; ?>

        </main>
    </div>

</body>
</html>