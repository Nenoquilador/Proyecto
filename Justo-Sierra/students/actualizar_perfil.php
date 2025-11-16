<?php
// 1. Iniciar la sesión y aplicar la seguridad
session_start();

if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
    header("Location: ../login.php");
    exit;
}

include '../config/conexion.php';

$id_alumno = $_SESSION['id_alumno'];
$nombre_alumno = $_SESSION['nombre_alumno'] ?? 'Alumno';
$error_bd = null;
$mensaje = '';
$error = false;
$perfil = null;
$cv_actual_url = null;

// Lógica para obtener datos actuales del alumno
try {
    // ✅ CORRECCIÓN: Se añade 'perfil_linkedin' a la consulta
    $sql = "SELECT nombre, apellidos, email, matricula, carrera, semestre, cv_url, perfil_linkedin FROM alumnos WHERE id_alumno = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();
    $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$perfil) {
        die("Error: No se pudo cargar el perfil del alumno.");
    }

    if (!empty($perfil['cv_url'])) {
        // Ruta apunta a CVS dentro de students
        $cv_actual_url = "CVS/" . rawurlencode(htmlspecialchars($perfil['cv_url']));
    }

} catch (PDOException $e) {
    $error_bd = "Error al cargar el perfil: " . $e->getMessage();
}

// Lógica para procesar el formulario (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_cambios'])) {

    $carrera = $_POST['carrera'] ?? $perfil['carrera'];
    // ✅ CORRECCIÓN: Se captura el dato de LinkedIn
    $perfil_linkedin = trim($_POST['perfil_linkedin'] ?? ($perfil['perfil_linkedin'] ?? null));
    $cv_nuevo_nombre = $perfil['cv_url']; // Usar cv_url

    // Lógica de subida de archivo CV
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "CVS/"; // Ruta dentro de students
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $original_filename = basename($_FILES["cv_file"]["name"]);
        $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $safe_filename = preg_replace('/[^A-Za-z0-9_\-.]/', '_', pathinfo($original_filename, PATHINFO_FILENAME));
        $cv_nuevo_nombre = $id_alumno . "_" . time() . "_" . $safe_filename . "." . $file_extension;
        $target_file = $target_dir . $cv_nuevo_nombre;
        $uploadOk = 1;

        // Validaciones
        if ($_FILES["cv_file"]["size"] > 5 * 1024 * 1024) { // 5MB
            $mensaje = "Error: El archivo es demasiado grande (Máx 5MB)."; $error = true; $uploadOk = 0;
        }
        if ($file_extension != "pdf") {
            $mensaje = "Error: Solo se permiten archivos PDF."; $error = true; $uploadOk = 0;
        }

        // Intentar mover el archivo subido
        if ($uploadOk == 1) {
             $cv_anterior_path = !empty($perfil['cv_url']) ? $target_dir . $perfil['cv_url'] : null;
            if ($cv_anterior_path && file_exists($cv_anterior_path) && $perfil['cv_url'] !== $cv_nuevo_nombre) {
                @unlink($cv_anterior_path);
            }
            if (!move_uploaded_file($_FILES["cv_file"]["tmp_name"], $target_file)) {
                $mensaje = "Error al subir el archivo CV. Verifica permisos en la carpeta 'CVS'."; $error = true;
                $cv_nuevo_nombre = $perfil['cv_url']; // Revertir
            }
        }
    } elseif (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] != UPLOAD_ERR_NO_FILE) {
        $mensaje = "Error al intentar subir el archivo CV. Código: " . $_FILES['cv_file']['error']; $error = true;
    }

    // Si no hubo error de subida, actualizamos la BD
    if (!$error) {
        try {
            // ✅ CORRECCIÓN: Se añade 'perfil_linkedin' al UPDATE
            $sql_update = "UPDATE alumnos SET carrera = :carrera, cv_url = :cv, perfil_linkedin = :linkedin WHERE id_alumno = :id";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bindParam(':carrera', $carrera, PDO::PARAM_STR);
            $stmt_update->bindParam(':cv', $cv_nuevo_nombre, PDO::PARAM_STR);
            $stmt_update->bindParam(':linkedin', $perfil_linkedin, PDO::PARAM_STR); // Bind LinkedIn
            $stmt_update->bindParam(':id', $id_alumno, PDO::PARAM_INT);
            $stmt_update->execute();

            $mensaje = "Perfil y CV actualizados correctamente."; $error = false;
            // Recargar datos
            $perfil['carrera'] = $carrera;
            $perfil['cv_url'] = $cv_nuevo_nombre;
            $perfil['perfil_linkedin'] = $perfil_linkedin; // Actualizar perfil local
             if (!empty($perfil['cv_url'])) {
                $cv_actual_url = "CVS/" . rawurlencode(htmlspecialchars($perfil['cv_url']));
             } else { $cv_actual_url = null; }

        } catch (PDOException $e) {
            $mensaje = "Error al actualizar la base de datos: " . $e->getMessage(); $error = true;
        }
    }
}

// Lista de carreras
$carreras_ejemplo = [
    'administracion' => 'Administración', 'derecho' => 'Derecho', 'contaduria' => 'Contaduría',
    'sistemas' => 'Ing. en Sistemas Computacionales', 'psicologia' => 'Psicología',
    'diseno_grafico' => 'Diseño Gráfico', 'arquitectura' => 'Arquitectura', 'mercadotecnia' => 'Mercadotecnia'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Perfil y CV - Bolsa JS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand"> Bolsa de Trabajo <span class="brand-js">Justo Sierra</span> </div>
        <div class="navbar-links">
            <span class="welcome-msg">Hola, <?php echo htmlspecialchars($nombre_alumno); ?></span>
            <a href="dashboard.php" class="btn-secondary-nav">Dashboard</a>
            <a href="mis_postulaciones.php" class="btn-secondary-nav">Mis Postulaciones</a>
            <a href="perfil_alumno.php" class="btn-secondary-nav">Mi Perfil</a>
            <a href="../logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="form-card profile-edit-card">
            <h1 style="text-align: center; margin-bottom: 30px;">Actualizar Perfil y CV</h1>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $error ? 'error' : 'exito'; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>
            <?php if ($error_bd): ?>
                 <div class="mensaje error"><?php echo htmlspecialchars($error_bd); ?></div>
            <?php endif; ?>

            <form action="actualizar_perfil.php" method="POST" enctype="multipart/form-data">
                <h2>Datos Académicos</h2>
                <div class="mb-3">
                    <label for="carrera">Carrera:</label>
                    <select id="carrera" name="carrera" class="filter-select" style="width: 100%;" required>
                        <option value="">-- Selecciona tu Carrera --</option>
                        <?php foreach ($carreras_ejemplo as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo ($perfil['carrera'] ?? '') === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="perfil_linkedin">Perfil LinkedIn (URL completa, opcional):</label>
                    <input type="text" id="perfil_linkedin" name="perfil_linkedin"
                           value="<?php echo htmlspecialchars($perfil['perfil_linkedin'] ?? ''); ?>"
                           placeholder="Ej: https://www.linkedin.com/in/tu-usuario">
                </div>

                <h2 style="margin-top: 30px;">Currículum Vitae (CV)</h2>
                <div class="mb-3">
                    <label for="cv_file">Subir CV (Solo archivo PDF, máx 5MB):</label>
                    <div class="custom-file-input-wrapper">
                        <button type="button" class="custom-file-input-button"> <i class="fas fa-upload"></i> Seleccionar archivo </button>
                        <input type="file" id="cv_file" name="cv_file" accept=".pdf">
                        <span class="file-name-display">Sin archivo seleccionado</span>
                    </div>

                    <?php if ($cv_actual_url): ?>
                        <a href="<?php echo $cv_actual_url; ?>" target="_blank" class="current-cv-link"> <i class="fas fa-check-circle"></i> **CV Actual** Ver archivo subido </a>
                    <?php else: ?>
                        <p class="current-cv-link" style="color: var(--color-texto-secundario); font-weight: normal;"> <i class="fas fa-times-circle" style="color: var(--color-error);"></i> No tienes un CV subido. </p>
                    <?php endif; ?>
                    <p class="cv-upload-note">*Al subir un nuevo archivo PDF, el anterior será reemplazado automáticamente.*</p>
                </div>

                <div class="form-actions profile-edit-actions">
                    <a href="perfil_alumno.php" class="btn-secundario-form">Volver al Perfil</a>
                    <button type="submit" name="guardar_cambios" class="boton-principal"> <i class="fas fa-save"></i> Guardar Cambios </button>
                </div>
            </form>
        </div>
    </div>

   <script>
        const customButton = document.querySelector('.custom-file-input-button');
        const fileInput = document.getElementById('cv_file');
        const fileNameDisplay = document.querySelector('.file-name-display');

        customButton.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                fileNameDisplay.textContent = 'Sin archivo seleccionado';
            }
        });
    </script>

</body>
</html>