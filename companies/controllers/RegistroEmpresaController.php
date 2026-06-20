<?php
require_once __DIR__ . '/../models/CompanyModel.php';

class RegistroEmpresaController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();

        $mensaje = "";
        $error = false;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            require_once __DIR__ . '/../../config/Security.php';
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!Security::verifyCsrfToken($csrf_token)) {
                $mensaje = "Error de seguridad (CSRF). Por favor, recarga la página e intenta de nuevo.";
                $error = true;
            } else {
                $nombre_empresa = $_POST['nombre_empresa'] ?? '';
            $email_contacto = $_POST['email_contacto'] ?? '';
            $password_plana = $_POST['password'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $sitio_web = $_POST['sitio_web'] ?? '';
            
            $carreras_array = $_POST['carreras'] ?? [];
            $carreras_afines = implode(', ', $carreras_array);
            
            if (empty($nombre_empresa) || empty($email_contacto) || empty($password_plana) || empty($descripcion)) {
                $mensaje = "Error: Nombre, email, descripción y contraseña son obligatorios.";
                $error = true;
            } else {
                $password_hasheada = password_hash($password_plana, PASSWORD_DEFAULT);
                
                try {
                    $model = new CompanyModel($this->conexion);
                    $model->registrarEmpresa($nombre_empresa, $email_contacto, $password_hasheada, $descripcion, $sitio_web, $carreras_afines);
                    
                    $mensaje = "¡Registro exitoso! <strong>Inicia sesión ahora</strong> con tu correo y contraseña para descargar los formatos necesarios y activar tu cuenta.";
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
        }

        require_once __DIR__ . '/../views/registro_empresa.php';
    }
}
