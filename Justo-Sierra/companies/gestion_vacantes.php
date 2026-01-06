<?php
// ---------------------------------
// LÓGICA PHP (GESTIÓN DE VACANTES)
// ---------------------------------
session_start();

// Seguridad y autenticación
if (!isset($_SESSION['id_empresa']) || ($_SESSION['rol'] ?? '') !== 'empresa') {
    header("Location: ../login.php"); 
    exit();
}

try {
    require_once '../config/conexion.php'; 
} catch (\Throwable $th) {
    die("Error crítico: No se pudo cargar la configuración de la base de datos.");
}

// Función de utilidad para mejorar la visualización de los tags de la BD
function formatear_tag($texto) {
    if (empty($texto)) {
        return "N/A";
    }
    // 1. Reemplaza guiones bajos por espacios
    $formato = str_replace('_', ' ', $texto);
    // 2. Capitaliza la primera letra de cada palabra
    return ucwords($formato);
}


$id_empresa = $_SESSION['id_empresa'];
$vacantes = [];
$error_bd = null;

// 1. OBTENER TODAS LAS VACANTES DE ESTA EMPRESA
try {
    // Consulta usando la estructura de tabla verificada (fecha_publicacion, salario_ofrecido, tipo_contrato)
    $sql = "SELECT id_vacante, titulo, ubicacion, estado, tipo_contrato, modalidad, salario_ofrecido, fecha_publicacion 
            FROM vacantes 
            WHERE id_empresa = :id_empresa 
            ORDER BY fecha_publicacion DESC";
            
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
    $stmt->execute();
    $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si la consulta falla (debido a un error de columna), se informa al usuario.
    error_log("Error de BD en Gestion Vacantes: " . $e->getMessage());
    $error_bd = "Error al cargar las vacantes. Intente de nuevo más tarde. (Detalle: " . $e->getMessage() . ")";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vacantes - Justo Sierra</title>
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
    <nav class="navbar" style="background: var(--color-js-rojo-secundario);">
        <div class="navbar-brand">Portal de Reclutamiento <span class="brand-js">JS</span></div>
        <div class="navbar-links">
            <a href="dashboard.php" class="btn-secondary-nav">Dashboard</a> <a href="perfil_empresa.php" class="btn-secondary-nav">Mi Perfil</a> 
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="dashboard-container">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; margin-top: 30px;">
            <a href="dashboard.php" class="back-link" style="font-size: 1.1rem; color: var(--color-js-rojo-principal);">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
            <a href="publicar_vacante.php" class="boton-principal" style="padding: 10px 20px;">
                + Publicar Nueva Vacante
            </a>
        </div>
        
        <h1 style="text-align: left; margin-top: 0; font-size: 2.2rem; color: var(--color-js-rojo-principal) !important; font-weight: 700;">
            Gestión de Mis Vacantes
        </h1>
        
        <?php if ($error_bd): ?>
            <div class='mensaje error' style="text-align: left;"><?php echo $error_bd; ?></div>
        <?php endif; ?>

        <?php if (count($vacantes) > 0): ?>
            <div class="job-list">
                <?php foreach ($vacantes as $vacante): ?>
                    <div class="job-card">
                        
                        <div class="job-card-header">
                            <h2 style="font-size: 1.5rem; margin-bottom: 3px; color: var(--color-texto-principal);">
                                <?php echo htmlspecialchars($vacante['titulo']); ?>
                            </h2>
                            <h3 style="font-size: 1.1rem; color: var(--color-texto-secundario); font-weight: 500;">
                                <i class="fas fa-map-marker-alt" style="margin-right: 5px; color: var(--color-js-rojo-principal);"></i>
                                <?php echo htmlspecialchars($vacante['ubicacion']); ?>
                            </h3>
                        </div>
                        
                        <div class="job-card-body">
                            <div class="job-card-tags" style="margin-bottom: 15px;">
                                
                                <span class="tag tag-contrato">
                                    <?php echo formatear_tag($vacante['tipo_contrato']); ?>
                                </span>
                                <span class="tag">
                                    <?php echo formatear_tag($vacante['modalidad']); ?>
                                </span>
                                
                                <?php 
                                    $estado = htmlspecialchars($vacante['estado']);
                                    $clase_estado = ($estado === 'abierta') ? 'status-enviada' : 'status-rechazada';
                                    echo "<span class='status-badge {$clase_estado}' style='margin-left: 10px;'>{$estado}</span>";
                                ?>
                            </div>
                            
                            <p style="font-size: 0.9rem; color: var(--color-texto-secundario);">
                                Publicada el: <?php echo date('d/M/Y', strtotime($vacante['fecha_publicacion'])); ?>
                            </p>
                        </div>
                        
                        <div class="job-card-footer">
                            <span style="font-weight: 600; color: var(--color-texto-principal);">
                                Salario: 
                                <?php echo !empty($vacante['salario_ofrecido']) && $vacante['salario_ofrecido'] > 0 ? '$' . number_format($vacante['salario_ofrecido'], 2) : 'No especificado'; ?>
                            </span>
                            
                            <div class="table-actions-group">
                                <!-- CORRECCIÓN AQUÍ: cambiar "id" por "id_vacante" -->
                                <a href="ver_postulaciones.php?id_vacante=<?php echo $vacante['id_vacante']; ?>" 
                                   class="boton-principal-sm" style="background: #2980b9;">
                                    <i class="fas fa-eye"></i> Ver Postulaciones
                                </a>
                                
                                <a href="publicar_vacante.php?id=<?php echo $vacante['id_vacante']; ?>" 
                                   class="boton-principal-sm" style="background: #f39c12;">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                
                                <?php if ($vacante['estado'] === 'abierta'): ?>
                                    <!-- También corregido aquí para mantener consistencia -->
                                    <a href="procesar_empresa.php?action=cerrar&id_vacante=<?php echo $vacante['id_vacante']; ?>" 
                                       class="btn-secundario-form" 
                                       style="padding: 8px 15px; font-size: 0.9rem; border-color: var(--color-error); color: var(--color-error);" 
                                       onclick="return confirm('¿Está seguro de que desea cerrar esta vacante?');">
                                        <i class="fas fa-times"></i> Cerrar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="job-card-empty" style="text-align: left;">
                <?php if (!$error_bd): ?>
                    <h2>Aún no has publicado ninguna vacante.</h2>
                    <p>Usa el botón "Publicar Nueva Vacante" para empezar.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>

</body>
</html>