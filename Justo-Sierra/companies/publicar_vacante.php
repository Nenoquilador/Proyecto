<?php
// ---------------------------------
// LÓGICA PHP (CREAR / EDITAR VACANTE)
// ---------------------------------
session_start();

// 1. SEGURIDAD: Verificar que la empresa esté logueada
if (!isset($_SESSION['id_empresa']) || ($_SESSION['rol'] ?? '') !== 'empresa') {
    header("Location: ../login.php"); 
    exit();
}

try {
    require_once '../config/conexion.php'; 
} catch (\Throwable $th) {
    die("Error crítico: No se pudo cargar la configuración de la base de datos.");
}

$id_empresa = $_SESSION['id_empresa'];
$id_vacante = $_GET['id'] ?? null; // Captura el ID si existe (Modo Edición)
$mensaje = '';
$error = false;
$datos_vacante = null;

// --- 2. LÓGICA DE CARGA (Modo Edición) ---
if ($id_vacante) {
    try {
        // Cargar los datos de la vacante, verificando que pertenezca a la empresa logueada
        $sql = "SELECT titulo, descripcion, tipo_contrato, modalidad, ubicacion, salario_ofrecido 
                FROM Vacantes 
                WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        $stmt->execute();
        
        $datos_vacante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$datos_vacante) {
            $mensaje = "Error: Vacante no encontrada o no pertenece a tu cuenta.";
            $error = true;
            $id_vacante = null; // Fuerza a modo Creación si falla la carga
        }
    } catch (PDOException $e) {
        $mensaje = "Error al cargar los datos: " . $e->getMessage();
        $error = true;
    }
}

// --- 3. PROCESAR ENVÍO DEL FORMULARIO (Inserción o Actualización) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publicar'])) {
    
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $modalidad = $_POST['modalidad'] ?? ''; // El campo en el formulario es 'modalidad'
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $tipo_contrato = $_POST['tipo_contrato'] ?? ''; // El campo de tu BD es 'tipo_contrato'
    
    // Asume que si el salario se quitó del formulario, solo usaremos las columnas obligatorias
    
    if (empty($titulo) || empty($descripcion) || empty($tipo_contrato) || empty($modalidad) || empty($ubicacion)) {
        $mensaje = "Error: Faltan campos obligatorios.";
        $error = true;
    } else {
        try {
            if ($id_vacante) {
                // MODO ACTUALIZACIÓN (UPDATE)
                $sql = "UPDATE Vacantes SET 
                            titulo = :titulo, 
                            descripcion = :descripcion, 
                            tipo_contrato = :tipo_contrato, 
                            modalidad = :modalidad, 
                            ubicacion = :ubicacion 
                        WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";
                
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
                $mensaje = "¡Vacante actualizada con éxito! Serás redirigido en 3 segundos.";
            } else {
                // MODO CREACIÓN (INSERT)
                $sql = "INSERT INTO Vacantes (id_empresa, titulo, descripcion, tipo_contrato, modalidad, ubicacion, estado) 
                        VALUES (:id_empresa, :titulo, :descripcion, :tipo_contrato, :modalidad, :ubicacion, 'abierta')";
                
                $stmt = $conexion->prepare($sql);
                $mensaje = "¡Vacante publicada con éxito! Serás redirigido en 3 segundos.";
            }
            
            // Enlazar parámetros comunes
            $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':tipo_contrato', $tipo_contrato, PDO::PARAM_STR); // Usamos el nombre de columna de tu BD
            $stmt->bindParam(':modalidad', $modalidad, PDO::PARAM_STR); // Usamos el nombre de columna de tu BD
            $stmt->bindParam(':ubicacion', $ubicacion, PDO::PARAM_STR);

            $stmt->execute();
            
            $error = false;
            // Redirigir al listado de vacantes después de 3 segundos
            header("refresh:3;url=gestion_vacantes.php"); 
            exit(); // Detenemos la ejecución después de la redirección
        } catch (PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id_vacante ? 'Editar Vacante' : 'Publicar Nueva Vacante'; ?></title>
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="centrado"> 
    
    <div class="form-card" style="max-width: 700px;"> 
        <h1 style="text-align: center;">
            <?php echo $id_vacante ? 'Editar Vacante Existente' : 'Publicar Nueva Vacante'; ?>
        </h1>
        
        <?php if (!empty($mensaje)): ?>
            <div class='mensaje <?php echo $error ? 'error' : 'exito'; ?>'>
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="publicar_vacante.php<?php echo $id_vacante ? '?id=' . $id_vacante : ''; ?>" method="POST">
            
            <div class="mb-3">
                <label for="titulo">Título de la Vacante:</label>
                <input type="text" id="titulo" name="titulo" 
                    value="<?php echo htmlspecialchars($datos_vacante['titulo'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="tipo_contrato">Tipo de Contrato:</label>
                <select id="tipo_contrato" name="tipo_contrato" required>
                    <?php $selected_tc = $datos_vacante['tipo_contrato'] ?? ''; ?>
                    <option value="">-- Seleccione Tipo de Contrato --</option>
                    <option value="tiempo_completo" <?php echo $selected_tc === 'tiempo_completo' ? 'selected' : ''; ?>>Tiempo Completo</option>
                    <option value="medio_tiempo" <?php echo $selected_tc === 'medio_tiempo' ? 'selected' : ''; ?>>Medio Tiempo</option>
                    <option value="practicas" <?php echo $selected_tc === 'practicas' ? 'selected' : ''; ?>>Prácticas / Pasantía</option>
                    <option value="por_proyecto" <?php echo $selected_tc === 'por_proyecto' ? 'selected' : ''; ?>>Por Proyecto</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="modalidad">Modalidad:</label>
                <select id="modalidad" name="modalidad" required>
                    <?php $selected_m = $datos_vacante['modalidad'] ?? ''; ?>
                    <option value="">-- Seleccione Modalidad --</option>
                    <option value="presencial" <?php echo $selected_m === 'presencial' ? 'selected' : ''; ?>>Presencial</option>
                    <option value="remoto" <?php echo $selected_m === 'remoto' ? 'selected' : ''; ?>>Remoto</option>
                    <option value="hibrido" <?php echo $selected_m === 'hibrido' ? 'selected' : ''; ?>>Híbrido</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="ubicacion">Ubicación (Ciudad, Estado o Específico):</label>
                <input type="text" id="ubicacion" name="ubicacion" 
                    value="<?php echo htmlspecialchars($datos_vacante['ubicacion'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="descripcion">Descripción y Requisitos de la Vacante:</label>
                <textarea id="descripcion" name="descripcion" rows="8" required>
                    <?php echo htmlspecialchars($datos_vacante['descripcion'] ?? ''); ?>
                </textarea>
            </div>
            
            <div class="form-actions" style="display: flex; justify-content: space-between;"> 
                <a href="gestion_vacantes.php" class="btn-secundario-form" style="padding: 10px 20px;">
                    Cancelar y Volver
                </a>
                <button type="submit" name="publicar" class="boton-principal">
                    <i class="fas fa-save"></i> 
                    <?php echo $id_vacante ? 'Guardar Cambios' : 'Publicar Vacante'; ?>
                </button>
            </div>
        </form>
    </div>

</body>
</html>