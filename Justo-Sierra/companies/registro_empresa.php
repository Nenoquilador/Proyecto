<?php
// ---------------------------------
//  LÓGICA PHP (REGISTRO DE EMPRESA)
// ---------------------------------
session_start();

// RUTA CORREGIDA: Sube un nivel (..) para la conexión en config/
include '../config/conexion.php'; 

$mensaje = "";
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recibir y limpiar datos
    $nombre_empresa = $_POST['nombre_empresa'] ?? '';
    $email_contacto = $_POST['email_contacto'] ?? '';
    $password_plana = $_POST['password'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $sitio_web = $_POST['sitio_web'] ?? '';
    
    // 2. Validación de campos obligatorios
    if (empty($nombre_empresa) || empty($email_contacto) || empty($password_plana) || empty($descripcion)) {
        $mensaje = "Error: Nombre, email, descripción y contraseña son obligatorios.";
        $error = true;
    } else {
        
        // 3. Hasheo de contraseña
        $password_hasheada = password_hash($password_plana, PASSWORD_DEFAULT);
        
        try {
            // 4. Inserción en la tabla Empresas (estado_validacion será 'pendiente' por defecto)
            $sql = "INSERT INTO Empresas (nombre_empresa, email_contacto, password, descripcion, sitio_web) 
                    VALUES (:nombre_empresa, :email_contacto, :password, :descripcion, :sitio_web)";
            
            $stmt = $conexion->prepare($sql);
            
            $stmt->bindParam(':nombre_empresa', $nombre_empresa);
            $stmt->bindParam(':email_contacto', $email_contacto);
            $stmt->bindParam(':password', $password_hasheada);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':sitio_web', $sitio_web);
            
            $stmt->execute();
            
            $mensaje = "¡Registro enviado! Tu cuenta está en revisión. Te notificaremos cuando sea aprobada por Vinculación.";
            $error = false;
            
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { 
                $mensaje = "Error: El email de contacto ya está registrado."; 
            } else { 
                $mensaje = "Error al registrar la empresa: " . $e->getMessage(); 
            } 
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
    <title>Registro de Empresa - Bolsa de Trabajo JS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body class="centrado">

    <div class="form-card"> 
        <h1 style="text-align: center; margin-bottom: 15px;">Registro de Empresa</h1>
        <p style="text-align: center; color: var(--color-texto-secundario); margin-bottom: 25px;">
            Registra tu empresa para acceder al talento de Justo Sierra.
        </p>

        <?php if (!empty($mensaje)): ?>
            <div class='mensaje <?php echo $error ? 'error' : 'exito'; ?>'><?php echo html_entity_decode($mensaje); ?></div>
        <?php endif; ?>

        <form action="registro_empresa.php" method="POST">
            <div>
                <label for="nombre_empresa">Nombre de la Empresa:</label>
                <input type="text" id="nombre_empresa" name="nombre_empresa" required>
            </div>
            <div>
                <label for="email_contacto">Email de Contacto (Será tu usuario):</label>
                <input type="email" id="email_contacto" name="email_contacto" required>
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="descripcion">Descripción de la Empresa (Máx 500 caracteres):</label>
                <textarea id="descripcion" name="descripcion" rows="4" maxlength="500" required></textarea>
            </div>
            <div>
                <label for="sitio_web">Sitio Web (Opcional):</label>
                <input type="url" id="sitio_web" name="sitio_web" placeholder="https://www.ejemplo.com">
            </div>

            <div class="form-actions"> 
                <button type="submit" class="boton-principal">Solicitar Registro</button>
            </div>
        </form>

        <div class="form-footer-link">
            <p>¿Ya tienes cuenta? <a href="../login.php">Iniciar sesión</a></p>
        </div>
    </div>

</body>
</html>