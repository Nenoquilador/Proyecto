<?php
class LoginController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function handleRequest() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $mensaje = "";
        $error = false;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // CSRF Check
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!Security::verifyCsrfToken($csrf_token)) {
                $mensaje = "Error de seguridad (CSRF). Por favor, recarga la página e intenta de nuevo.";
                $error = true;
            } else {
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

                // Lógica Anti-Fuerza Bruta
                if (!isset($_SESSION['login_attempts'])) {
                    $_SESSION['login_attempts'] = [];
                }
                if (!isset($_SESSION['lockout'])) {
                    $_SESSION['lockout'] = [];
                }

                if (isset($_SESSION['lockout'][$email]) && time() < $_SESSION['lockout'][$email]) {
                    $minutos_restantes = ceil(($_SESSION['lockout'][$email] - time()) / 60);
                    $mensaje = "Demasiados intentos fallidos. Por favor, intenta de nuevo en $minutos_restantes minuto(s).";
                    $error = true;
                } elseif (empty($email) || empty($password)) {
                    $mensaje = "El usuario y contraseña son obligatorios.";
                    $error = true;
                } else {
                    try {
                        $login_exitoso = false;
                        $dashboard = '';

                        // PRIORITY 1: ADMINISTRADOR
                        $sql_admin = "SELECT id_admin, nombre, password, tipo_admin FROM administradores WHERE email = :email";
                        $stmt_admin = $this->conexion->prepare($sql_admin);
                        $stmt_admin->bindParam(':email', $email);
                        $stmt_admin->execute();
                        $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

                        if ($admin && password_verify($password, $admin['password'])) {
                            session_regenerate_id(true);
                            // Reset de intentos
                            unset($_SESSION['login_attempts'][$email]);
                            unset($_SESSION['lockout'][$email]);
                            
                            $_SESSION['id_admin'] = $admin['id_admin'];
                            $_SESSION['nombre_admin'] = $admin['nombre'];
                            $_SESSION['rol'] = 'admin'; 
                            $_SESSION['tipo_admin'] = $admin['tipo_admin']; 
                            if ($admin['tipo_admin'] === 'alumnos') {
                                $dashboard = 'admin/alumnos/dashboard.php';
                            } else {
                                $dashboard = 'admin/empresas/dashboard.php'; 
                            }
                            $login_exitoso = true;
                        }

                        // PRIORITY 2: ALUMNO
                        if (!$login_exitoso) {
                            $sql_alumno = "SELECT id_alumno, nombre, password FROM alumnos WHERE email = :email";
                            $stmt_alumno = $this->conexion->prepare($sql_alumno);
                            $stmt_alumno->bindParam(':email', $email);
                            $stmt_alumno->execute();
                            $alumno = $stmt_alumno->fetch(PDO::FETCH_ASSOC);

                            if ($alumno && password_verify($password, $alumno['password'])) {
                                session_regenerate_id(true);
                                // Reset de intentos
                                unset($_SESSION['login_attempts'][$email]);
                                unset($_SESSION['lockout'][$email]);
                                
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
                            $stmt_empresa = $this->conexion->prepare($sql_empresa);
                            $stmt_empresa->bindParam(':email', $email);
                            $stmt_empresa->execute();
                            $empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC);

                            if ($empresa && password_verify($password, $empresa['password'])) {
                                session_regenerate_id(true);
                                // Reset de intentos
                                unset($_SESSION['login_attempts'][$email]);
                                unset($_SESSION['lockout'][$email]);
                                
                                $_SESSION['id_empresa'] = $empresa['id_empresa'];
                                $_SESSION['nombre_empresa'] = $empresa['nombre_empresa'];
                                $_SESSION['rol'] = 'empresa'; 
                                $dashboard = 'companies/dashboard.php'; 
                                $login_exitoso = true;
                            }
                        }

                        // REDIRECCIÓN FINAL O FALLO
                        if ($login_exitoso) {
                            header('Location: ' . $dashboard); 
                            exit(); 
                        } elseif (!$error) {
                             // Incrementar intentos fallidos
                             if (!isset($_SESSION['login_attempts'][$email])) {
                                 $_SESSION['login_attempts'][$email] = 0;
                             }
                             $_SESSION['login_attempts'][$email]++;
                             
                             // Bloquear si llega a 5
                             if ($_SESSION['login_attempts'][$email] >= 5) {
                                 $_SESSION['lockout'][$email] = time() + (15 * 60); // Bloqueo por 15 minutos
                                 $mensaje = "Demasiados intentos fallidos. Por favor, intenta de nuevo en 15 minutos.";
                             } else {
                                 $intentos_restantes = 5 - $_SESSION['login_attempts'][$email];
                                 $mensaje = "Credenciales incorrectas. Te quedan $intentos_restantes intento(s).";
                             }
                             
                             $error = true;
                        }

                    } catch(PDOException $e) {
                        error_log("Error de BD en login: " . $e->getMessage());
                        $mensaje = "Ocurrió un error interno. Por favor, intenta de nuevo más tarde.";
                        $error = true;
                    }
                }
            }
        }

        // Cargar vista
        require_once __DIR__ . '/../views/login_view.php';
    }
}
?>
