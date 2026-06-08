<?php
// ---------------------------------
// LÓGICA PHP (CARGAR Y ACTUALIZAR PERFIL DE EMPRESA)
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
$mensaje = '';
$error = false;
$datos_empresa = null;

// --- 2. LÓGICA DE ACTUALIZACIÓN (Si se recibe POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_perfil'])) {
    
    // Recibir y limpiar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rfc = trim($_POST['rfc'] ?? '');
    $sitio_web = trim($_POST['sitio_web'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    
    // Validación mínima
    if (empty($nombre) || empty($email) || empty($rfc)) {
        $mensaje = "Error: Nombre, Email y RFC son obligatorios.";
        $error = true;
    } else {
        try {
            // Manejo del Logo
            $logo_query = "";
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $dir_logos = '../assets/img/logos_empresas/';
                if (!file_exists($dir_logos)) {
                    mkdir($dir_logos, 0777, true);
                }
                $nombre_logo = time() . '_' . basename($_FILES['logo']['name']);
                $ruta_destino = $dir_logos . $nombre_logo;
                $ruta_relativa = 'assets/img/logos_empresas/' . $nombre_logo;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_destino)) {
                    // Actualizamos la ruta del logo
                    $logo_query = ", logo_url = :logo_url";
                }
            }

            // Consulta para actualizar los datos de la empresa
            $sql = "UPDATE Empresas SET 
                        nombre_empresa = :nombre, 
                        email_contacto = :email, 
                        rfc = :rfc, 
                        sitio_web = :sitio_web, 
                        descripcion = :descripcion
                        $logo_query
                    WHERE id_empresa = :id_empresa";
            
            $stmt = $conexion->prepare($sql);
            
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':rfc', $rfc, PDO::PARAM_STR);
            $stmt->bindParam(':sitio_web', $sitio_web, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
            if ($logo_query !== "") {
                $stmt->bindParam(':logo_url', $ruta_relativa, PDO::PARAM_STR);
            }

            $stmt->execute();
            
            // Actualizar el nombre de la empresa en la sesión si cambió
            $_SESSION['nombre_empresa'] = $nombre;

            $mensaje = "¡Perfil actualizado con éxito! Serás redirigido en 2 segundos.";
            $error = false;
            // Redirigir al perfil para ver los cambios
            header("refresh:2;url=perfil_empresa.php"); 
            exit(); 
        } catch (PDOException $e) {
            $mensaje = "Error al actualizar en la base de datos: " . $e->getMessage();
            $error = true;
        }
    }
}

// --- 3. LÓGICA DE CARGA (Para precargar el formulario) ---
try {
    $sql = "SELECT nombre_empresa, email_contacto, rfc, sitio_web, descripcion 
            FROM Empresas 
            WHERE id_empresa = :id_empresa";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
    $stmt->execute();
    $datos_empresa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos_empresa) {
        die("Error: No se pudieron cargar los datos para edición.");
    }
} catch (PDOException $e) {
    die("Error al cargar los datos de edición: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil | <?php echo htmlspecialchars($datos_empresa['nombre_empresa'] ?? 'Empresa'); ?></title>
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="centrado"> 
    
    <div class="form-card" style="max-width: 700px;"> 
        <h1 style="text-align: center;"><i class="fas fa-edit"></i> Editar Perfil de Empresa</h1>
        
        <?php if (!empty($mensaje)): ?>
            <div class='mensaje <?php echo $error ? 'error' : 'exito'; ?>'>
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="procesar_empresa.php" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="nombre">Nombre de la Empresa:</label>
                <input type="text" id="nombre" name="nombre" 
                       value="<?php echo htmlspecialchars($datos_empresa['nombre_empresa'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email">Email de Contacto:</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($datos_empresa['email_contacto'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="rfc">RFC:</label>
                <input type="text" id="rfc" name="rfc" 
                       value="<?php echo htmlspecialchars($datos_empresa['rfc'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="sitio_web">Sitio Web (URL completa):</label>
                <input type="text" id="sitio_web" name="sitio_web" 
                       value="<?php echo htmlspecialchars($datos_empresa['sitio_web'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="logo">Logotipo de la Empresa (PNG/JPG):</label>
                <input type="file" id="logo" name="logo" accept="image/png, image/jpeg" style="padding: 10px; background: white; border-radius: 5px; border: 1px solid #ddd; width: 100%;">
            </div>

            <div class="mb-3">
                <label for="descripcion">Descripción de la Empresa:</label>
                <textarea id="descripcion" name="descripcion" rows="6" required>
                    <?php echo htmlspecialchars($datos_empresa['descripcion'] ?? ''); ?>
                </textarea>
            </div>
            
            <div class="form-actions" style="display: flex; justify-content: space-between;"> 
                <a href="perfil_empresa.php" class="btn-secundario-form" style="padding: 10px 20px;">
                    Cancelar
                </a>
                <button type="submit" name="actualizar_perfil" class="boton-principal">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>

</body>
</html>