<?php
// ---------------------------------
// LÓGICA PHP (GESTIONAR ALUMNOS - ADMIN)
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
$alumnos = [];
$error_db = null;

// ✅ CORRECCIÓN: Leer desde $_POST en lugar de $_GET
$search_term = $_POST['search'] ?? ''; 

// 2. Obtener lista de Alumnos (con búsqueda opcional)
try {
    // Usamos 'alumnos' (minúscula) como en tu tabla
    $sql = "SELECT id_alumno, nombre, apellidos, matricula, carrera, semestre, cv_url
            FROM alumnos"; 

    $params = [];
    if (!empty($search_term)) {
        // Buscar por nombre, apellido o matrícula
        $sql .= " WHERE nombre LIKE :search OR apellidos LIKE :search OR matricula LIKE :search";
        $params[':search'] = '%' . $search_term . '%';
    }

    $sql .= " ORDER BY apellidos ASC, nombre ASC"; // Ordenar alfabéticamente

    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = "Error al cargar la lista de alumnos: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Alumnos - Admin</title>

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
                <li><a href="gestionar_vacantes_admin.php"><i class="fas fa-list-alt"></i> Gestionar Vacantes</a></li>
                <li><a href="gestionar_alumnos_admin.php" class="active"><i class="fas fa-users"></i> Gestionar Alumnos</a></li>
            </ul>
        </aside>

        <main class="main-content">

            <h2><i class="fas fa-users"></i> Gestión de Alumnos Registrados</h2>
            <p>Busca por nombre, apellido o matrícula para gestionar perfiles.</p>

            <form action="gestionar_alumnos.php" method="POST" class="admin-search-form">
                <input type="text" name="search" class="search-input"
                       placeholder="Buscar por Nombre o Matrícula..."
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="boton-principal search-button">
                    <i class="fas fa-search"></i> Buscar
                </button>
                 <?php if (!empty($search_term)): ?>
                    <a href="gestionar_alumnos.php" class="btn-secundario-form" style="height: 40px; padding: 0 15px; font-size: 0.9rem;">
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
                            <th>Nombre Completo</th>
                            <th>Matrícula</th>
                            <th>Carrera</th>
                            <th>Semestre</th>
                            <th>CV</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($alumnos)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--color-texto-secundario);">
                                    <?php echo !empty($search_term) ? 'No se encontraron alumnos con ese criterio.' : 'No hay alumnos registrados.'; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($alumnos as $alumno): ?>
                                <tr>
                                    <td data-label="Nombre">
                                        <strong><?php echo htmlspecialchars($alumno['apellidos'] . ', ' . $alumno['nombre']); ?></strong>
                                    </td>
                                    <td data-label="Matrícula"><?php echo htmlspecialchars($alumno['matricula']); ?></td>
                                    <td data-label="Carrera"><?php echo htmlspecialchars($alumno['carrera'] ?? 'N/A'); ?></td>
                                    <td data-label="Semestre">
                                        <?php echo isset($alumno['semestre']) ? htmlspecialchars($alumno['semestre']) . 'mo' : 'N/A'; ?>
                                    </td>
                                    <td data-label="CV">
                                        <?php if (!empty($alumno['cv_url'])): ?>
                                            <a href="../students/CVS/<?php echo rawurlencode(htmlspecialchars($alumno['cv_url'])); ?>" target="_blank" class="action-download">
                                                <i class="fas fa-download"></i> Descargar
                                            </a>
                                        <?php else: ?>
                                            <span style="color: var(--color-texto-secundario); font-style: italic;">No subido</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Acciones">
                                        <a href="ver_perfil_alumno.php?id=<?php echo $alumno['id_alumno']; ?>" class="btn-table-action" style="background-color: var(--color-info);">
                                            <i class="fas fa-eye"></i> Ver Perfil
                                        </a>
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