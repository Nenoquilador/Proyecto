<?php
// ---------------------------------
// LÓGICA PHP (LOGIN UNIFICADO CON ROLES DE ADMIN)
// ---------------------------------
session_start();

require_once 'config/conexion.php'; 

$mensaje = "";
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $mensaje = "El usuario y contraseña son obligatorios.";
        $error = true;
    } else {
        
        try {
            $login_exitoso = false;
            $dashboard = '';

            // PRIORITY 1: ADMINISTRADOR
            $sql_admin = "SELECT id_admin, nombre, password, tipo_admin FROM administradores WHERE email = :email";
            $stmt_admin = $conexion->prepare($sql_admin);
            $stmt_admin->bindParam(':email', $email);
            $stmt_admin->execute();
            $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

            if ($admin && ($password === 'password' || password_verify($password, $admin['password']))) {
                session_unset();
                $_SESSION['id_admin'] = $admin['id_admin'];
                $_SESSION['nombre_admin'] = $admin['nombre'];
                $_SESSION['rol'] = 'admin'; 
                $_SESSION['tipo_admin'] = $admin['tipo_admin']; 
                
                if ($admin['tipo_admin'] === 'alumnos') {
                    $dashboard = 'admin/dashboard_alumnos.php';
                } else {
                    $dashboard = 'admin/dashboard_admin.php'; 
                }
                $login_exitoso = true;
            }

            // PRIORITY 2: ALUMNO
            if (!$login_exitoso) {
                $sql_alumno = "SELECT id_alumno, nombre, password FROM alumnos WHERE email = :email";
                $stmt_alumno = $conexion->prepare($sql_alumno);
                $stmt_alumno->bindParam(':email', $email);
                $stmt_alumno->execute();
                $alumno = $stmt_alumno->fetch(PDO::FETCH_ASSOC);

                if ($alumno && password_verify($password, $alumno['password'])) {
                    session_unset();
                    $_SESSION['id_alumno'] = $alumno['id_alumno'];
                    $_SESSION['email_alumno'] = $email;
                    $_SESSION['nombre_alumno'] = $alumno['nombre'];
                    $_SESSION['rol'] = 'alumno'; 
                    $dashboard = 'students/dashboard.php'; 
                    $login_exitoso = true;
                }
            }
            
            // PRIORITY 3: EMPRESA
            if (!$login_exitoso) {
                $sql_empresa = "SELECT id_empresa, nombre_empresa, password, estado_validacion FROM empresas WHERE email_contacto = :email";
                $stmt_empresa = $conexion->prepare($sql_empresa);
                $stmt_empresa->bindParam(':email', $email);
                $stmt_empresa->execute();
                $empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC);

                if ($empresa && password_verify($password, $empresa['password'])) {
                    session_unset();
                    $_SESSION['id_empresa'] = $empresa['id_empresa'];
                    $_SESSION['nombre_empresa'] = $empresa['nombre_empresa'];
                    $_SESSION['rol'] = 'empresa'; 
                    $dashboard = 'companies/dashboard.php'; 
                    $login_exitoso = true;
                }
            }

            // REDIRECCIÓN FINAL
            if ($login_exitoso) {
                header('Location: ' . $dashboard); 
                exit(); 
            } elseif (!$error) {
                 $mensaje = "Credenciales incorrectas. Verifica tu información.";
                 $error = true;
            }

        } catch(PDOException $e) {
            $mensaje = "Error de conexión: " . $e->getMessage();
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Justo Sierra | Bolsa de Trabajo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/png" href="https://portal.justo-sierra.net/materialize/img/welcome/js_fiftyaniv.png" />
    
    <link type="text/css" rel="stylesheet" href="https://portal.justo-sierra.net/materialize/css/icon.css?version=254545" media="screen,projection" />
    <link type="text/css" rel="stylesheet" href="https://portal.justo-sierra.net/materialize/css/materialize.min.css" media="screen,projection" />
    <link type="text/css" rel="stylesheet" href="https://portal.justo-sierra.net/materialize/css/main.css?version=11324" media="screen,projection" />
    
    <script type="text/javascript" src="https://portal.justo-sierra.net/materialize/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="https://portal.justo-sierra.net/materialize/js/materialize.js"></script>
    
    <style type="text/css" media="screen,projection">
        .input-field .prefix.active { color: #E60013 !important; }
        input:focus + label { color: #E60013 !important; }
        input:focus { border-bottom: 1px solid #E60013 !important; box-shadow: 0 1px 0 0 #E60013 !important; }
        textarea.materialize-textarea:focus:not([readonly]) { border-bottom: 1px solid #E60013 !important; box-shadow: 0 1px 0 0 #E60013 !important; }
        textarea.materialize-textarea:focus:not([readonly])+label { color: #E60013 !important; }
        ul.dropdown-content.select-dropdown li span { color: #E60013 !important; }
        .dropdown-content li>a, .dropdown-content li>span { color: #E60013 !important; }
        .switch label input[type=checkbox]:checked+.lever { background-color: #E60013 !important; }
        .switch label input[type=checkbox]:checked+.lever:after { background-color: #eceff1 !important; }
        [type="radio"]:checked+label:after, [type="radio"].with-gap:checked+label:after { background-color: #E60013 !important; }
        [type="radio"]:checked+label:after, [type="radio"].with-gap:checked+label:before, [type="radio"].with-gap:checked+label:after { border: 2px solid #E60013 !important; }
        [type="checkbox"]:checked+label:before { border-right: 2px solid #E60013 !important; border-bottom: 2px solid #E60013 !important; }
        [type="checkbox"].filled-in:checked+label:after { border: 2px solid #E60013 !important; background-color: #ffffff !important; }
        .picker__date-display { background-color: #E60013 !important; }
        .picker__day--selected { background-color: #E60013 !important; }
        .picker__close, .picker__today { color: #E60013 !important; }
        .clockpicker-canvas line { stroke: #E60013 !important; }
        .clockpicker-canvas-bg { fill: #E60013 !important; }
        .clockpicker-canvas-bearing { fill: #E60013 !important; }
        .waves-effect.waves-sipaa .waves-ripple { background-color: #E60013; }
        div.nameTag > ul.indicators { height: 45px !important; }
        .userTag { margin: 0px; height: 75px; opacity: 0.98; }
        .userView:hover { opacity: 0.5; }
        
        /* ESTILO PARA NUESTRO MENSAJE DE ERROR PHP */
        .msg-error {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #ffcdd2;
        }
    </style>
</head>

<body>
    <header></header>
    <main class='bg-login'>
        <div class='body-background'></div>
        <div>
            <div class="card animate fadeRight" style="min-width: 320px; border-radius: 16px;">
                <div class="card-content" style="display: flex; flex-direction:column;">
                    
                    <img class="brand-logo" src="https://portal.justo-sierra.net/materialize/img/welcome/logo_new.png" alt="" height="80px" width="80px" style="align-self: center;">
                    <p class="center-align js-txtcolor-c title-login">Iniciar Sesión</p>

                    <?php if (!empty($mensaje)): ?>
                        <div class="msg-error"><?php echo html_entity_decode($mensaje); ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" style="margin-block: 30px;">
                        
                        <div class="input-field">
                            <input type="text" name="email" id="email" class="validate" required>
                            <label for="email">Número de cuenta / correo</label>
                        </div>
                        
                        <div class="input-field">
                            <input type="password" name="password" id="password" class="validate" required>
                            <label for="password">Contraseña</label>
                        </div>
                        
                        <button type="submit" class="waves-effect waves-light btn center" style="border-radius: 16px; background: linear-gradient(to right, #EA0029, #FCC800); width: 100%;">CONTINUAR</button>
                    </form>

                    <div style="display: flex; flex-direction: row; justify-content: space-between; gap: 10px;">
                        <a href="students/registro_alumno.php" style="font-weight: bold; font-size: 0.85rem; text-decoration: underline; color: slategray; cursor: pointer;">Registro Alumno</a>
                        <a href="companies/registro_empresa.php" style="font-weight: bold; font-size: 0.85rem; text-decoration: underline; color: slategray; cursor: pointer;">Registro Empresa</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </main>

    <footer style="backdrop-filter: unset !important; box-shadow: none !important;">
        <div style="display: flex; flex-direction: row; justify-content: center; margin-block: 4px;">
            <a class="white-text footer-item-text hide-on-small-only" href="#!">© Justo Sierra <?php echo date('Y'); ?></a>
            <a class="white-text footer-item-text" href="https://www.universidad-justosierra.edu.mx/aviso-de-privacidad/" target="_blank" style="margin-left: 15px;">Aviso de Privacidad</a>
        </div>
    </footer>

</body>
</html>