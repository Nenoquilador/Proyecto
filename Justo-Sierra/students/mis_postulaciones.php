<?php
// 1. Iniciar la sesión y aplicar la seguridad
session_start();

if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
    header("Location: ../login.php");
    exit;
}

// Apunta a la conexión en config/
include '../config/conexion.php';

$id_alumno = $_SESSION['id_alumno'];
$nombre_alumno = $_SESSION['nombre_alumno'] ?? 'Alumno';
$error_bd = null;
$postulaciones = [];

// Lógica para obtener todos los datos de postulación del alumno de la BD
try {
    $sql = "SELECT
                p.fecha_postulacion, p.estado_postulacion,
                v.titulo AS titulo_vacante, v.id_vacante,
                e.nombre_empresa
            FROM
                Postulaciones AS p
            JOIN
                Vacantes AS v ON p.id_vacante = v.id_vacante
            JOIN
                Empresas AS e ON v.id_empresa = e.id_empresa
            WHERE
                p.id_alumno = :id_alumno
            ORDER BY
                p.fecha_postulacion DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();
    $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_bd = "Error al cargar tu historial de postulaciones: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Postulaciones - Bolsa JS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    </head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">
            Bolsa de Trabajo <span class="brand-js">Justo Sierra</span>
        </div>
        <div class="navbar-links">
            <span class="welcome-msg">Hola, <?php echo htmlspecialchars($nombre_alumno); ?></span>
            <a href="dashboard.php" class="btn-secondary-nav">Dashboard</a>
            <a href="mis_postulaciones.php" class="btn-secondary-nav">Mis Postulaciones</a>
            <a href="perfil_alumno.php" class="btn-secondary-nav">Mi Perfil</a>
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="dashboard-container">

        <h1>Historial de Postulaciones</h1>

        <?php if ($error_bd): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($error_bd); ?></div>
        <?php elseif (empty($postulaciones)): ?>
            <div class="job-card-empty" style="text-align: left;">
                <h2>No has realizado ninguna postulación.</h2>
                <p>Visita el <a href="dashboard.php">Dashboard</a> para encontrar vacantes.</p>
            </div>
        <?php else: ?>

            <div class="table-responsive">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Vacante</th>
                            <th>Empresa</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postulaciones as $p): ?>
                            <tr>
                                <td data-label="Vacante">
                                    <strong><?php echo htmlspecialchars($p['titulo_vacante']); ?></strong>
                                </td>
                                <td data-label="Empresa"><?php echo htmlspecialchars($p['nombre_empresa']); ?></td>
                                <td data-label="Fecha"><?php echo date('d/m/Y', strtotime($p['fecha_postulacion'])); ?></td>
                                <td data-label="Estado">
                                    <?php
                                        $estado_raw = strtolower($p['estado_postulacion']);
                                        $estado_display = ucfirst(str_replace('_', ' ', $p['estado_postulacion']));
                                    ?>
                                    <span class="status-badge status-<?php echo $estado_raw; ?>">
                                        <?php echo htmlspecialchars($estado_display); ?>
                                    </span>
                                </td>
                                <td data-label="Acción">
                                    <a href="detalle_vacante.php?id=<?php echo $p['id_vacante']; ?>"
                                       class="btn-table-action">
                                        <i class="fas fa-search"></i> Ver Detalle
                                    </a>
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