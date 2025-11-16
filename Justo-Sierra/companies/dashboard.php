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
    // Usaremos $conexion para las consultas SQL
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


// 2. OBTENER ESTADÍSTICAS Y DATOS CLAVE
try {
    // Consulta para Vacantes Activas
    $sql_vacantes = "SELECT COUNT(*) FROM Vacantes WHERE id_empresa = :id AND estado = 'abierta'";
    $stmt_vacantes = $conexion->prepare($sql_vacantes); 
    $stmt_vacantes->bindParam(':id', $id_empresa, PDO::PARAM_INT);
    $stmt_vacantes->execute();
    $active_vacancies = $stmt_vacantes->fetchColumn();

    // Consulta para Postulaciones Recibidas
    $sql_apps = "SELECT COUNT(p.id_postulacion) 
                  FROM Postulaciones p
                  JOIN Vacantes v ON p.id_vacante = v.id_vacante
                  WHERE v.id_empresa = :id";
    $stmt_apps = $conexion->prepare($sql_apps); 
    $stmt_apps->bindParam(':id', $id_empresa, PDO::PARAM_INT);
    $stmt_apps->execute();
    $total_applications = $stmt_apps->fetchColumn();
    
    // Consulta para Vacantes Cerradas
    $sql_cerradas = "SELECT COUNT(*) FROM Vacantes WHERE id_empresa = :id AND estado = 'cerrada'";
    $stmt_cerradas = $conexion->prepare($sql_cerradas);
    $stmt_cerradas->bindParam(':id', $id_empresa, PDO::PARAM_INT);
    $stmt_cerradas->execute();
    $closed_vacancies = $stmt_cerradas->fetchColumn();


} catch (PDOException $e) {
    error_log("Error de BD en Dashboard: " . $e->getMessage());
    $error_bd = "No se pudieron cargar las estadísticas. Intente de nuevo más tarde.";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Empresa - <?php echo htmlspecialchars($nombre_empresa); ?></title>
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
    
    <style>
        /* Estilos de las tarjetas de estadísticas (TU CÓDIGO ORIGINAL) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background-color: var(--color-blanco);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border-left: 5px solid var(--color-js-rojo-principal);
        }
        .stat-card h3 {
            color: var(--color-js-rojo-secundario);
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        .stat-card .value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--color-texto-principal);
        }
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
    </style>
</head>
<body>
    
    <nav class="navbar" style="background: var(--color-js-rojo-secundario);">
        <div class="navbar-brand">
            Portal de Reclutamiento <span class="brand-js">JS</span>
        </div>
        <div class="navbar-links">
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="dashboard-container">
        
        <h1 style="margin-top: 30px; margin-bottom: 10px; text-align: left; color: var(--color-js-rojo-principal) !important; font-size: 2.2rem;">
            ¡Bienvenido, <?php echo htmlspecialchars($nombre_empresa); ?>!
        </h1>
        <p style="color: var(--color-texto-secundario); margin-bottom: 40px;">
            Este es tu centro de control para la publicación y gestión de talento de Justo Sierra.
        </p>

        <?php if ($error_bd): ?>
            <div class='mensaje error' style="padding: 15px; text-align: left;"><?php echo $error_bd; ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Vacantes Activas</h3>
                <div class="value"><?php echo $active_vacancies; ?></div>
            </div>
            <div class="stat-card">
                <h3>Postulaciones Recibidas</h3>
                <div class="value"><?php echo $total_applications; ?></div>
            </div>
            <div class="stat-card">
                <h3>Vacantes Cerradas</h3>
                <div class="value"><?php echo $closed_vacancies; ?></div> 
            </div>
        </div>

        <h2 style="margin-bottom: 25px; color: var(--color-js-rojo-secundario);">Acciones Rápidas</h2>
        <div class="action-grid">
            
            <a href="publicar_vacante.php" class="boton-principal" style="padding: 20px; font-size: 1.1rem;">
                + Publicar Nueva Vacante
            </a>
            
            <a href="gestion_vacantes.php" class="btn-secundario-form" style="padding: 20px; font-size: 1.1rem; border-color: var(--color-js-rojo-secundario);">
                Gestionar Vacantes (<?php echo $active_vacancies; ?> activas)
            </a>

            <a href="perfil_empresa.php" class="btn-secundario-form" style="padding: 20px; font-size: 1.1rem; border-color: var(--color-js-rojo-secundario);">
                Ver/Actualizar Perfil de Empresa
            </a>

        </div>
        
    </div>

</body>
</html>