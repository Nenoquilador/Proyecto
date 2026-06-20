<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Alumno - Bolsa de Trabajo Justo Sierra</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css"> 
</head>
<body class="centrado">

    <div class="form-card card animate fadeRight"> 
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
            <?php require_once '../config/Security.php'; echo Security::getCsrfInput(); ?>
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
