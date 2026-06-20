<?php
require_once __DIR__ . '/../models/AlumnoModel.php';
require_once __DIR__ . '/../../config/Security.php';

class RegistroAlumnoController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();
        $mensaje = ""; 
        $error = false; 
        $dominio_requerido = "@ujsierra.com.mx";
        $semestre_default = 7; 

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!Security::verifyCsrfToken($csrf_token)) {
                $mensaje = "Error de seguridad (CSRF). Por favor, recarga la página e intenta de nuevo.";
                $error = true;
            } else {
                $nombre = $_POST['nombre'] ?? ''; 
                $apellidos = $_POST['apellidos'] ?? ''; 
                $email = $_POST['email'] ?? ''; 
                $password_plana = $_POST['password'] ?? ''; 
                $matricula = $_POST['matricula'] ?? '';
                
                if (empty($nombre) || empty($apellidos) || empty($email) || empty($password_plana) || empty($matricula)) {
                    $mensaje = "Error: Todos los campos son obligatorios."; 
                    $error = true;
                } else if (substr(strtolower($email), -strlen($dominio_requerido)) !== strtolower($dominio_requerido)) {
                    $mensaje = "Error: Debes usar un correo institucional con terminación <strong>@ujsierra.com.mx</strong>."; 
                    $error = true;
                } else {
                    try {
                        $alumnoModel = new AlumnoModel($this->conexion);
                        $alumnoModel->registrarAlumno($nombre, $apellidos, $email, $password_plana, $matricula, $semestre_default);
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
        }

        require_once __DIR__ . '/../views/registro_alumno.php';
    }
}
