<?php
// ---------------------------------
// LÓGICA PHP (GESTIONAR VACANTES - ADMIN)
// ---------------------------------
session_start();

// 1. Control de Sesión Admin
if (!isset($_SESSION['id_admin']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Conexión a BD
include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Vinculación';
$vacantes = [];
$error_db = null;
$search_term = $_GET['search'] ?? ''; // Para la búsqueda

// 2. Obtener lista de Todas las Vacantes (con búsqueda opcional)
try {
    // Seleccionamos campos relevantes de Vacantes y unimos con Empresas
    $sql = "SELECT
                v.id_vacante, v.titulo, v.estado AS estado_vacante, v.fecha_publicacion,
                e.nombre_empresa, e.estado_validacion AS estado_empresa
            FROM
                Vacantes v
            JOIN
                Empresas e ON v.id_empresa = e.id_empresa";

    $params = [];
    if (!empty($search_term)) {
        // Buscar por título de vacante o nombre de empresa
        $sql .= " WHERE v.titulo LIKE :search OR e.nombre_empresa LIKE :search";
        $params[':search'] = '%' . $search_term . '%';
    }

    $sql .= " ORDER BY v.fecha_publicacion DESC"; // Ordenar por fecha de publicación

    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = "Error al cargar la lista de vacantes: " . $e->getMessage();
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
    <title>Gestionar Vacantes - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

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

            <h2><i class="fas fa-list-alt"></i> Gestión de Vacantes Publicadas</h2>
            <p>Listado completo de todas las ofertas de trabajo en la plataforma.</p>

            <form action="gestionar_vacantes_admin.php" method="GET" class="admin-search-form">
                <input type="text" name="search" class="search-input"
                       placeholder="Buscar por Título o Empresa..."
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="boton-principal search-button">
                    <i class="fas fa-search"></i> Buscar
                </button>
                 <?php if (!empty($search_term)): ?>
                    <a href="gestionar_vacantes_admin.php" class="btn-secundario-form" style="height: 40px; padding: 0 15px; font-size: 0.9rem;">
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>

            <?php if ($error_db): ?>
                <div class="mensaje error"><?php echo htmlspecialchars($error_db); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Vacante</th>
                            <th>Empresa</th>
                            <th>Estado Publicación</th>
                            <th>Estado Empresa</th>
                            <th>Fecha Publicación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vacantes)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--color-texto-secundario);">
                                    <?php echo !empty($search_term) ? 'No se encontraron vacantes con ese criterio.' : 'No hay vacantes publicadas.'; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vacantes as $vacante): ?>
                                <tr>
                                    <td data-label="Vacante">
                                        <strong><?php echo htmlspecialchars($vacante['titulo']); ?></strong>
                                    </td>
                                    <td data-label="Empresa"><?php echo htmlspecialchars($vacante['nombre_empresa']); ?></td>
                                    <td data-label="Estado Publicación">
                                        <?php
                                            $estado_vac = strtolower($vacante['estado_vacante']);
                                            $clase_estado_vac = ($estado_vac === 'abierta') ? 'status-aceptada' : 'status-rechazada'; // Reusing student status colors
                                        ?>
                                        <span class="status-badge <?php echo $clase_estado_vac; ?>">
                                            <?php echo htmlspecialchars(ucfirst($estado_vac)); ?>
                                        </span>
                                    </td>
                                     <td data-label="Estado Empresa">
                                        <?php
                                            $estado_emp = strtolower($vacante['estado_empresa']);
                                            // Asignar clases de badge según estado de empresa
                                            $clase_estado_emp = 'status-en_revision'; // Default for pendiente
                                            if ($estado_emp === 'aprobada') $clase_estado_emp = 'status-aceptada';
                                            if ($estado_emp === 'rechazada') $clase_estado_emp = 'status-rechazada';
                                        ?>
                                        <span class="status-badge <?php echo $clase_estado_emp; ?>">
                                            <?php echo htmlspecialchars(ucfirst($estado_emp)); ?>
                                        </span>
                                    </td>
                                    <td data-label="Fecha Publicación"><?php echo date('d/m/Y', strtotime($vacante['fecha_publicacion'])); ?></td>
                                    <td data-label="Acciones">
                                        <div class="table-actions-group">
                                             <a href="detalle_vacante_admin.php?id=<?php echo $vacante['id_vacante']; ?>" class="btn-table-action" style="background-color: var(--color-info);">
                                                <i class="fas fa-eye"></i> Detalle
                                            </a>
                                            <?php if ($vacante['estado_vacante'] === 'abierta'): ?>
                                                <a href="procesar_admin.php?action=cerrar_vacante&id=<?php echo $vacante['id_vacante']; ?>" class="action-close" onclick="return confirm('¿Seguro que deseas cerrar esta vacante?');">
                                                    <i class="fas fa-times-circle"></i> Cerrar
                                                </a>
                                            <?php else: ?>
                                                 <a href="procesar_admin.php?action=abrir_vacante&id=<?php echo $vacante['id_vacante']; ?>" class="action-download" onclick="return confirm('¿Seguro que deseas reabrir esta vacante?');">
                                                    <i class="fas fa-check-circle"></i> Reabrir
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>

</body>
</html>