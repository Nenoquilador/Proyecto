<?php
// ---------------------------------
// LÓGICA PHP (DASHBOARD ADMINISTRADOR)
// ---------------------------------
session_start();

// --- INICIO DEL BYPASS TEMPORAL ---
if (isset($_GET['bypass']) && $_GET['bypass'] === 'true') {
    $_SESSION['id_admin'] = 999;
    $_SESSION['nombre_admin'] = 'Admin Bypass (Temporal)';
    $_SESSION['rol'] = 'admin'; // Es importante definir el rol también
    header("Location: dashboard_admin.php");
    exit();
}
// --- FIN DEL BYPASS TEMPORAL ---

// 1. Control de Sesión Admin
if (!isset($_SESSION['id_admin']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// RUTA CORREGIDA: Apunta a la conexión en config/
include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Vinculación Universitaria';
$empresas_pendientes = [];
$error_db = null;

// Manejo de mensajes de estado después de gestionar_empresa.php
$status_msg = $_GET['msg'] ?? null;
$status_type = $_GET['status'] ?? null; // 'success' or 'error'

// 2. Obtener Empresas Pendientes de Aprobación
try {
    // Asegúrate que tu tabla Empresas tiene estado_validacion y fecha_registro
    $sql = "SELECT id_empresa, nombre_empresa, email_contacto, fecha_registro
            FROM Empresas
            WHERE estado_validacion = 'pendiente'
            ORDER BY fecha_registro ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $empresas_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_db = "Error al cargar empresas pendientes: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Vinculación - Bolsa de Trabajo JS</title>

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
                <li><a href="dashboard_admin.php" class="active"><i class="fas fa-building"></i> Empresas Pendientes</a></li>
                <li><a href="gestionar_vacantes_admin.php"><i class="fas fa-list-alt"></i> Gestionar Vacantes</a></li>
                <li><a href="gestionar_alumnos.php"><i class="fas fa-users"></i> Gestionar Alumnos</a></li>
                </ul>
        </aside>

        <main class="main-content">

            <?php if ($status_msg): ?>
                <div class="mensaje <?php echo ($status_type === 'success') ? 'exito' : 'error'; ?>">
                    <?php echo htmlspecialchars(urldecode($status_msg)); ?>
                </div>
            <?php endif; ?>

            <section>
                <h2><i class="fas fa-clock"></i> Empresas Pendientes de Aprobación</h2>
                <p>Las siguientes empresas se han registrado y requieren tu validación.</p>

                <?php if ($error_db): ?>
                    <div class="mensaje error"><?php echo htmlspecialchars($error_db); ?></div>
                <?php endif; ?>

                <?php if (empty($empresas_pendientes)): ?>
                    <div class="mensaje exito">🎉 <strong>No hay empresas pendientes</strong> de aprobación en este momento.</div>
                <?php else: ?>
                    <div class="pending-companies-list">
                        <?php foreach ($empresas_pendientes as $empresa): ?>
                            <div class="company-card">
                                <div class="company-info">
                                    <strong><?php echo htmlspecialchars($empresa['nombre_empresa']); ?></strong>
                                    <small><i class="fas fa-envelope"></i> Email: <?php echo htmlspecialchars($empresa['email_contacto']); ?></small>
                                    <small><i class="far fa-calendar-alt"></i> Registro: <?php echo date("d/m/Y H:i", strtotime($empresa['fecha_registro'])); ?></small>
                                </div>
                                <div class="company-actions">
                                    <a href="gestionar_empresa.php?id=<?php echo $empresa['id_empresa']; ?>&accion=aprobar" class="btn-aprobar">
                                        <i class="fas fa-check"></i> Aprobar
                                    </a>
                                    <a href="gestionar_empresa.php?id=<?php echo $empresa['id_empresa']; ?>&accion=rechazar" class="btn-rechazar">
                                        <i class="fas fa-times"></i> Rechazar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        </main>
    </div>

</body>
</html>