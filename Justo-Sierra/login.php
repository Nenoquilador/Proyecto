<?php
// ---------------------------------
// LÓGICA PHP (LOGIN UNIFICADO CON BYPASS DE HASH PARA ADMIN)
// ---------------------------------
session_start();

// CORRECCIÓN CLAVE AQUÍ: La ruta es directa desde la raíz.
require_once 'config/conexion.php'; 

$mensaje = "";
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $mensaje = "Error: Email y contraseña son obligatorios.";
        $error = true;
    } else {
        
        try {
            $login_exitoso = false;
            $dashboard = '';

            // PRIORITY 1: ADMINISTRADOR
            $sql_admin = "SELECT id_admin, nombre, password FROM Administradores WHERE email = :email";
            $stmt_admin = $conexion->prepare($sql_admin);
            $stmt_admin->bindParam(':email', $email);
            $stmt_admin->execute();
            $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

            // --- SOLUCIÓN TEMPORAL DE ACCESO AL ADMIN ---
            if ($admin && ($password === 'password' || password_verify($password, $admin['password']))) {
                // Limpiar sesiones previas (buena práctica)
                session_unset();
                $_SESSION['id_admin'] = $admin['id_admin'];
                $_SESSION['nombre_admin'] = $admin['nombre'];
                $_SESSION['rol'] = 'admin'; // Establecer rol para ADMIN
                $dashboard = 'admin/dashboard_admin.php'; 
                $login_exitoso = true;
            }

            // PRIORITY 2: ALUMNO
            if (!$login_exitoso) {
                $sql_alumno = "SELECT id_alumno, nombre, password FROM Alumnos WHERE email = :email";
                $stmt_alumno = $conexion->prepare($sql_alumno);
                $stmt_alumno->bindParam(':email', $email);
                $stmt_alumno->execute();
                $alumno = $stmt_alumno->fetch(PDO::FETCH_ASSOC);

                if ($alumno && password_verify($password, $alumno['password'])) {
                    session_unset();
                    $_SESSION['id_alumno'] = $alumno['id_alumno'];
                    $_SESSION['email_alumno'] = $email;
                    $_SESSION['nombre_alumno'] = $alumno['nombre'];
                    $_SESSION['rol'] = 'alumno'; // Establecer rol para ALUMNO
                    $dashboard = 'students/dashboard.php'; 
                    $login_exitoso = true;
                }
            }
            
            // PRIORITY 3: EMPRESA
            if (!$login_exitoso) {
                $sql_empresa = "SELECT id_empresa, nombre_empresa, password, estado_validacion FROM Empresas WHERE email_contacto = :email";
                $stmt_empresa = $conexion->prepare($sql_empresa);
                $stmt_empresa->bindParam(':email', $email);
                $stmt_empresa->execute();
                $empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC);

                if ($empresa && password_verify($password, $empresa['password'])) {
                    if ($empresa['estado_validacion'] === 'aprobada') {
                        session_unset();
                        $_SESSION['id_empresa'] = $empresa['id_empresa'];
                        $_SESSION['nombre_empresa'] = $empresa['nombre_empresa'];
                        $_SESSION['rol'] = 'empresa'; // Establecer rol para EMPRESA (CRUCIAL)
                        $dashboard = 'companies/dashboard.php'; 
                        $login_exitoso = true;
                    } else {
                        $mensaje = "Tu cuenta está en estado: " . ucfirst($empresa['estado_validacion']) . ". Contacta a Vinculación de Justo Sierra.";
                        $error = true;
                    }
                }
            }


            // REDIRECCIÓN FINAL - SOLUCIÓN PARA QUE MANDE AL DASHBOARD
            if ($login_exitoso) {
                // Redirige inmediatamente
                header('Location: ' . $dashboard); 
                exit(); 
            } elseif (!$error) {
                 $mensaje = "Error: Email o contraseña incorrectos.";
                 $error = true;
            }

        } catch(PDOException $e) {
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
    <title>Iniciar Sesión - Bolsa de Trabajo JS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body class="centrado"> 

    <div class="form-card"> 
        <h1 style="text-align: center; margin-bottom: 15px;">Iniciar Sesión</h1>
        <p style="text-align: center; color: var(--color-texto-secundario); margin-bottom: 25px;">
            Accede con tu cuenta de Alumno, Empresa o Vinculación.
        </p>

        <?php
        if (!empty($mensaje)) {
            $clase_css = $error ? 'error' : 'exito';
            echo "<div class='mensaje $clase_css'>" . html_entity_decode($mensaje) . "</div>";
        }
        ?>

        <form action="login.php" method="POST">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div> 
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-actions"> 
                <button type="submit" class="boton-principal">Acceder</button>
            </div>
        </form>

        <div class="form-footer-link">
            <p>¿Eres Empresa y no tienes cuenta? <a href="companies/registro_empresa.php">Regístrate aquí</a></p>
            <p style="margin-top: 10px;">¿Eres Alumno y necesitas registrarte? <a href="students/registro_alumno.php">Regístrate aquí</a></p>
        </div>
    </div>

</body>
</html>