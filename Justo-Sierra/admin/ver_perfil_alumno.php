<?php
// ---------------------------------
// LÓGICA PHP (VER/ACTUALIZAR PERFIL ALUMNO - ADMIN)
// ---------------------------------
session_start();

// 1. Control de Sesión Admin
if (!isset($_SESSION['id_admin']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Obtener ID del alumno desde URL
$id_alumno = $_GET['id'] ?? null;
if (!$id_alumno || !is_numeric($id_alumno)) {
    header("Location: gestionar_alumnos_admin.php?status=error&msg=" . urlencode("ID de alumno inválido."));
    exit();
}

// Conexión a BD
include '../config/conexion.php';

$nombre_admin = $_SESSION['nombre_admin'] ?? 'Vinculación';
$alumno = null;
$error_db = null;
$cv_url = null;
$mensaje_update = ''; // Para mensajes de actualización
$error_update = false;

// --- INICIO: Lógica para Actualizar Semestre (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_semestre'])) {
    $nuevo_semestre = $_POST['semestre'] ?? null;

    if ($nuevo_semestre === '7' || $nuevo_semestre === '8') {
        try {
            $sql_update = "UPDATE alumnos SET semestre = :semestre WHERE id_alumno = :id_alumno";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bindParam(':semestre', $nuevo_semestre, PDO::PARAM_INT);
            $stmt_update->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
            $stmt_update->execute();

            if ($stmt_update->rowCount() > 0) {
                $mensaje_update = "Semestre actualizado correctamente a " . $nuevo_semestre . "mo.";
                $error_update = false;
            } else {
                $mensaje_update = "No se realizaron cambios en el semestre (quizás ya era ese valor).";
                $error_update = false; // No es un error técnico
            }
        } catch (PDOException $e) {
            $mensaje_update = "Error al actualizar el semestre: " . $e->getMessage();
            $error_update = true;
        }
    } else {
        $mensaje_update = "Error: Valor de semestre inválido.";
        $error_update = true;
    }
}
// --- FIN: Lógica para Actualizar Semestre ---


// 2. Obtener Detalles Completos del Alumno (se ejecuta después de posible update)
try {
    $sql = "SELECT id_alumno, nombre, apellidos, email, matricula, carrera, semestre, cv_url, perfil_linkedin, fecha_registro
            FROM alumnos
            WHERE id_alumno = :id_alumno";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        $error_db = "No se encontró el perfil del alumno solicitado.";
    } else {
        if (!empty($alumno['cv_url'])) {
             $cv_url = "../students/CVS/" . rawurlencode(htmlspecialchars($alumno['cv_url']));
        }
    }

} catch (PDOException $e) {
    $error_db = "Error al cargar el perfil del alumno: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Alumno - <?php echo htmlspecialchars($alumno['nombre'] ?? 'Admin'); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>
        /* Estilos adicionales para la vista de detalle */
        .detail-section { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--color-borde); }
        .detail-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .detail-section h3 { color: var(--color-js-rojo-secundario); font-size: 1.3rem; margin-bottom: 15px; }
        .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px 25px; }
         .detail-item strong { display: block; font-size: 0.9rem; color: var(--color-texto-secundario); font-weight: 600; margin-bottom: 3px; text-transform: uppercase; }
         .detail-item span { font-size: 1rem; color: var(--color-texto-principal); }
         .description-text-admin { white-space: pre-wrap; font-size: 1rem; color: var(--color-texto-principal); background-color: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid var(--color-borde); max-height: 300px; overflow-y: auto; }
        .profile-view-admin { background-color: var(--color-blanco); border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-top: 20px; overflow: hidden; }
        .profile-header-admin { background: var(--gradiente-js-principal); color: var(--color-blanco); padding: 20px 30px; text-align: center; }
         .profile-header-admin h3 { color: var(--color-blanco); margin-bottom: 3px; font-size: 1.6rem;}
         .profile-header-admin p { color: var(--color-blanco); opacity: 0.9; font-size: 1rem; margin: 0;}
        .profile-content-admin { padding: 30px; }
        .profile-grid-admin { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; }
         .profile-field-admin strong { display: block; font-size: 0.85rem; color: var(--color-texto-secundario); font-weight: 600; margin-bottom: 5px; text-transform: uppercase; }
         .profile-field-admin span { font-size: 1rem; color: var(--color-texto-principal); display: block; padding-bottom: 8px; border-bottom: 1px dashed var(--color-borde); }
         .profile-field-admin a { color: var(--color-js-rojo-principal); font-weight: 500; }
         .profile-field-admin a:hover { text-decoration: underline; }
         .back-button-container { margin-top: 30px; }
         /* Estilos para el formulario de semestre */
         .semestre-update-form { display: flex; align-items: center; gap: 10px; margin-top: 5px; }
         .semestre-update-form select { height: 38px; padding: 5px 10px; font-size: 0.9rem; }
         .semestre-update-form button { height: 38px; padding: 5px 15px; font-size: 0.9rem; }
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
                <li><a href="gestionar_vacantes_admin.php"><i class="fas fa-list-alt"></i> Gestionar Vacantes</a></li>
                <li><a href="gestionar_alumnos.php" class="active"><i class="fas fa-users"></i> Gestionar Alumnos</a></li>
            </ul>
        </aside>

        <main class="main-content">

            <h2><i class="fas fa-user-graduate"></i> Perfil del Alumno</h2>

            <?php if (!empty($mensaje_update)): ?>
                <div class="mensaje <?php echo $error_update ? 'error' : 'exito'; ?>">
                    <?php echo htmlspecialchars($mensaje_update); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_db): ?>
                <div class="mensaje error"><?php echo htmlspecialchars($error_db); ?></div>
                 <div class="back-button-container">
                     <a href="gestionar_alumnos_admin.php" class="btn-secundario-form">
                         <i class="fas fa-arrow-left"></i> Regresar a Gestión
                     </a>
                 </div>
            <?php elseif ($alumno): ?>

                <div class="profile-view-admin">
                    <div class="profile-header-admin">
                        <h3><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']); ?></h3>
                        <p><?php echo htmlspecialchars($alumno['email']); ?></p>
                    </div>

                    <div class="profile-content-admin">
                         <div class="profile-grid-admin">
                            <div class="profile-field-admin">
                                <strong>Matrícula</strong>
                                <span><?php echo htmlspecialchars($alumno['matricula']); ?></span>
                            </div>
                            <div class="profile-field-admin">
                                <strong>Carrera</strong>
                                <span><?php echo htmlspecialchars($alumno['carrera'] ?? 'N/A'); ?></span>
                            </div>
                            
                            <div class="profile-field-admin">
                                <strong>Semestre</strong>
                                <span>
                                    <?php echo isset($alumno['semestre']) ? htmlspecialchars($alumno['semestre']) . 'mo' : 'N/A'; ?>
                                </span>
                                <form action="ver_perfil_alumno.php?id=<?php echo $id_alumno; ?>" method="POST" class="semestre-update-form">
                                    <select name="semestre" class="filter-select" required>
                                        <option value="7" <?php echo ($alumno['semestre'] ?? '') == 7 ? 'selected' : ''; ?>>7mo</option>
                                        <option value="8" <?php echo ($alumno['semestre'] ?? '') == 8 ? 'selected' : ''; ?>>8vo</option>
                                    </select>
                                    <button type="submit" name="actualizar_semestre" class="boton-principal">Actualizar</button>
                                </form>
                            </div>
                            
                             <div class="profile-field-admin">
                                <strong>Fecha Registro</strong>
                                <span><?php echo date('d/m/Y', strtotime($alumno['fecha_registro'])); ?></span>
                            </div>
                             <div class="profile-field-admin">
                                <strong>Perfil LinkedIn</strong>
                                <span>
                                    <?php if (!empty($alumno['perfil_linkedin'])): ?>
                                        <a href="<?php echo htmlspecialchars($alumno['perfil_linkedin']); ?>" target="_blank">
                                           <i class="fab fa-linkedin"></i> Ver Perfil
                                        </a>
                                    <?php else: ?>
                                        No Proporcionado
                                    <?php endif; ?>
                                </span>
                            </div>
                             <div class="profile-field-admin">
                                <strong>Currículum Vitae (CV)</strong>
                                <span>
                                    <?php if ($cv_url): ?>
                                        <a href="<?php echo $cv_url; ?>" target="_blank" class="action-download">
                                            <i class="fas fa-download"></i> Descargar CV
                                        </a>
                                    <?php else: ?>
                                        No Subido
                                    <?php endif; ?>
                                </span>
                            </div>
                         </div>
                    </div>
                </div>

                <div class="back-button-container">
                    <a href="gestionar_alumnos.php" class="btn-secundario-form">
                        <i class="fas fa-arrow-left"></i> Regresar a Gestión
                    </a>
                </div>

            <?php endif; ?>

        </main>
    </div>

</body>
</html>