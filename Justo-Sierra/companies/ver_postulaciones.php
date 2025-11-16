<?php
// ---------------------------------
//  LÓGICA PHP (VER POSTULACIONES)
// ---------------------------------
session_start();

if (!isset($_SESSION['id_empresa'])) {
    header("Location: ../login.php"); 
    exit();
}

include '../config/conexion.php'; 

$id_empresa = $_SESSION['id_empresa'];
$id_vacante = $_GET['id_vacante'] ?? null;
$postulantes = [];
$vacante_titulo = '';
$error_db = null;

// 1. VALIDACIÓN: Asegurar que el ID sea válido y pertenezca a esta empresa
if (empty($id_vacante) || !is_numeric($id_vacante)) {
    $error_db = "ID de vacante no válido.";
    header("Location: gestion_vacantes.php?error=invalid_id");
    exit();
}

try {
    // 2. VERIFICACIÓN DE PROPIEDAD Y OBTENCIÓN DEL TÍTULO
    $sql_check = "SELECT titulo FROM Vacantes WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
    $stmt_check->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
    $stmt_check->execute();
    
    $vacante_data = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$vacante_data) {
        // Redirige si la vacante no existe o no pertenece a la empresa logueada
        header("Location: gestion_vacantes.php?error=unauthorized");
        exit();
    }
    $vacante_titulo = $vacante_data['titulo'];


    // 3. CONSULTA PRINCIPAL: Obtener la lista de alumnos postulados
    $sql = "SELECT 
                p.id_postulacion,
                p.fecha_postulacion,
                p.estado_postulacion,
                a.nombre,
                a.apellidos,
                a.matricula,
                a.carrera,
                a.cv_url
            FROM 
                Postulaciones AS p
            JOIN 
                Alumnos AS a ON p.id_alumno = a.id_alumno
            WHERE 
                p.id_vacante = :id_vacante
            ORDER BY 
                p.fecha_postulacion ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
    $stmt->execute();
    $postulantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = "Error al cargar los postulantes: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postulantes: <?php echo htmlspecialchars($vacante_titulo); ?></title>
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body>
    
    <nav class="navbar" style="background: var(--color-js-rojo-secundario);">
        <div class="navbar-brand">
            Portal de Reclutamiento <span class="brand-js">JS</span>
        </div>
        <div class="navbar-links">
            <a href="gestion_vacantes.php" class="btn-secondary-nav">Mis Vacantes</a>
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="dashboard-container">
        
        <h1 style="margin-top: 30px; margin-bottom: 5px; color: var(--color-js-rojo-secundario) !important; text-align: left;">
            Postulantes para:
        </h1>
        <h2 style="margin-top: 0; margin-bottom: 30px; font-weight: 600;"><?php echo htmlspecialchars($vacante_titulo); ?></h2>

        <?php if ($error_db): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($error_db); ?></div>
        <?php elseif (empty($postulantes)): ?>
            <div class="job-card-empty">
                <h2>Aún no hay alumnos postulados a esta oferta.</h2>
                <p>La vacante está abierta. Vuelve a revisar más tarde.</p>
            </div>
        <?php else: ?>
            
            <div class="table-responsive">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Matrícula / Carrera</th>
                            <th>Fecha</th>
                            <th>Estado Actual</th>
                            <th>CV / Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postulantes as $p): ?>
                            <tr>
                                <td data-label="Alumno">
                                    <strong><?php echo htmlspecialchars($p['nombre'] . ' ' . $p['apellidos']); ?></strong>
                                    <br><small><?php echo htmlspecialchars($p['email']); ?></small>
                                </td>
                                <td data-label="Matrícula">
                                    <?php echo htmlspecialchars($p['matricula']); ?>
                                    <br><small><?php echo htmlspecialchars($p['carrera']); ?></small>
                                </td>
                                <td data-label="Fecha"><?php echo date('d/m/Y', strtotime($p['fecha_postulacion'])); ?></td>
                                <td data-label="Estado">
                                    <span class="status-badge status-<?php echo strtolower($p['estado_postulacion']); ?>">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $p['estado_postulacion']))); ?>
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    <div class="table-actions-group">
                                        <?php if ($p['cv_url']): ?>
                                            <a href="../<?php echo htmlspecialchars($p['cv_url']); ?>" target="_blank" 
                                               class="table-action-link action-download">
                                                Descargar CV
                                            </a>
                                        <?php else: ?>
                                            <span style="color: red; font-size: 0.9em;">CV No Subido</span>
                                        <?php endif; ?>
                                        
                                        <select onchange="window.location.href='procesar_empresa.php?id_postulacion=<?php echo $p['id_postulacion']; ?>&action=cambiar&new_status=' + this.value;">
                                            <option value="">Cambiar Estado</option>
                                            <option value="vista" <?php if ($p['estado_postulacion'] == 'vista') echo 'selected'; ?>>Vista</option>
                                            <option value="en_proceso" <?php if ($p['estado_postulacion'] == 'en_proceso') echo 'selected'; ?>>En Proceso</option>
                                            <option value="aceptada" <?php if ($p['estado_postulacion'] == 'aceptada') echo 'selected'; ?>>Aceptar</option>
                                            <option value="rechazada" <?php if ($p['estado_postulacion'] == 'rechazada') echo 'selected'; ?>>Rechazar</option>
                                        </select>
                                        
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>

</body>
</html>