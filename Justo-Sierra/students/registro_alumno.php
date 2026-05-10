<?php
// ---------------------------------
//  LÓGICA PHP (REGISTRO DE ALUMNO)
// ---------------------------------
session_start();

// CORRECCIÓN CLAVE: Sube un nivel (..) para entrar a la carpeta 'config/'
include '../config/conexion.php'; 

$mensaje = ""; 
$error = false; 
$dominio_requerido = "@ujsierra.com.mx";
// Valor por defecto para semestre (7mo semestre)
$semestre_default = 7; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'] ?? ''; 
    $apellidos = $_POST['apellidos'] ?? ''; 
    $email = $_POST['email'] ?? ''; 
    $password_plana = $_POST['password'] ?? ''; 
    $matricula = $_POST['matricula'] ?? '';
    
    // 1. Validación de campos vacíos
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($password_plana) || empty($matricula)) {
        $mensaje = "Error: Todos los campos son obligatorios."; 
        $error = true;
        
    // 2. Validación de dominio de correo institucional
    } else if (substr(strtolower($email), -strlen($dominio_requerido)) !== strtolower($dominio_requerido)) {
        $mensaje = "Error: Debes usar un correo institucional con terminación <strong>@ujsierra.com.mx</strong>."; 
        $error = true;
    } else {
        
        // 3. Hasheo de contraseña y preparación de la BD
        $password_hasheada = password_hash($password_plana, PASSWORD_DEFAULT);
        
        try {
            // Se inserta la matrícula y el valor por defecto de semestre
            $sql = "INSERT INTO Alumnos (nombre, apellidos, email, password, matricula, semestre) 
                    VALUES (:nombre, :apellidos, :email, :password, :matricula, :semestre)";
            
            $stmt = $conexion->prepare($sql);
            
            $stmt->bindParam(':nombre', $nombre); 
            $stmt->bindParam(':apellidos', $apellidos); 
            $stmt->bindParam(':email', $email); 
            $stmt->bindParam(':password', $password_hasheada); 
            $stmt->bindParam(':matricula', $matricula);
            $stmt->bindParam(':semestre', $semestre_default, PDO::PARAM_INT); // Se inserta el valor '7'
            
            $stmt->execute();
            
            // Redirección en caso de éxito al login en la raíz
            header("Location: ../login.php?registration=success");
            exit;
            
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { 
                $mensaje = "Error: El correo electrónico o la matrícula ya están registrados."; 
            } else { 
                $mensaje = "Error al registrar el alumno: " . $e->getMessage(); 
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
    <title>Registro de Alumno - Bolsa de Trabajo Justo Sierra</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body class="centrado">

    <div class="form-card"> 
        <h1 style="text-align: center; margin-bottom: 15px;">Registro de Alumno</h1>
        <p style="text-align: center; color: var(--color-texto-secundario); margin-bottom: 25px;">
            Crea tu perfil para acceder a las oportunidades.
        </p>

        <?php
        // Mostrar mensaje de éxito o error
        if (!empty($mensaje)) {
            $clase_css = $error ? 'error' : 'exito';
            echo "<div class='mensaje $clase_css'>" . html_entity_decode($mensaje) . "</div>";
        }
        ?>

        <form action="registro_alumno.php" method="POST">
            <div><label for="nombre">Nombre(s):</label><input type="text" id="nombre" name="nombre" required></div>
            <div><label for="apellidos">Apellidos:</label><input type="text" id="apellidos" name="apellidos" required></div>
            <div><label for="email">Email Institucional (@ujsierra.com.mx):</label><input type="email" id="email" name="email" required></div>
            <div><label for="password">Contraseña:</label><input type="password" id="password" name="password" required></div>
            <div><label for="matricula">Matrícula:</label><input type="text" id="matricula" name="matricula" required></div>

            <div class="form-actions"> 
                <button type="submit" class="boton-principal">Crear Cuenta</button>
            </div>
        </form>

        <div class="form-footer-link">
            <p>¿Ya tienes cuenta? <a href="../login.php">Inicia sesión aquí</a></p>
        </div>
    </div>

</body>
</html>