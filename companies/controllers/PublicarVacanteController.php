<?php
require_once __DIR__ . '/../models/VacanteCompanyModel.php';
require_once __DIR__ . '/../models/CompanyModel.php';

class PublicarVacanteController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();

        if (!isset($_SESSION['id_empresa']) || ($_SESSION['rol'] ?? '') !== 'empresa') {
            header("Location: ../login.php"); 
            exit();
        }

        $companyModel = new CompanyModel($this->conexion);
        if ($companyModel->getEstadoValidacion($_SESSION['id_empresa']) !== 'aprobada') {
            die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
                    <h2>Acceso Denegado</h2>
                    <p>Tu empresa aún no ha sido aprobada. Ve al <a href='dashboard.php'>Dashboard</a> para completar tu registro.</p>
                 </div>");
        }

        $id_empresa = $_SESSION['id_empresa'];
        $id_vacante = $_GET['id'] ?? null;
        $mensaje = '';
        $error = false;
        $datos_vacante = null;

        $model = new VacanteCompanyModel($this->conexion);

        if ($id_vacante) {
            try {
                $datos_vacante = $model->getVacanteForEdit($id_vacante, $id_empresa);
                if (!$datos_vacante) {
                    $mensaje = "Error: Vacante no encontrada o no pertenece a tu cuenta.";
                    $error = true;
                    $id_vacante = null;
                }
            } catch (PDOException $e) {
                $mensaje = "Error al cargar los datos: " . $e->getMessage();
                $error = true;
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publicar'])) {
            // Validar CSRF
            if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("Error de validación CSRF.");
            }

            // Sanitización estricta XSS
            $titulo = trim(isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo'], ENT_QUOTES, 'UTF-8') : '');
            $descripcion = trim(isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion'], ENT_QUOTES, 'UTF-8') : '');
            $modalidad = isset($_POST['modalidad']) ? htmlspecialchars($_POST['modalidad'], ENT_QUOTES, 'UTF-8') : '';
            $ubicacion = trim(isset($_POST['ubicacion']) ? htmlspecialchars($_POST['ubicacion'], ENT_QUOTES, 'UTF-8') : '');
            $tipo_contrato = isset($_POST['tipo_contrato']) ? htmlspecialchars($_POST['tipo_contrato'], ENT_QUOTES, 'UTF-8') : '';
            $carrera_afin = isset($_POST['carrera_afin']) ? htmlspecialchars($_POST['carrera_afin'], ENT_QUOTES, 'UTF-8') : '';
            $salario_ofrecido = trim(isset($_POST['salario_ofrecido']) ? htmlspecialchars($_POST['salario_ofrecido'], ENT_QUOTES, 'UTF-8') : '');
            
            if (empty($titulo) || empty($descripcion) || empty($tipo_contrato) || empty($modalidad) || empty($ubicacion) || empty($carrera_afin)) {
                $mensaje = "Error: Faltan campos obligatorios.";
                $error = true;
            } else {
                try {
                    $data = [
                        ':id_empresa' => $id_empresa,
                        ':titulo' => $titulo,
                        ':descripcion' => $descripcion,
                        ':tipo_contrato' => $tipo_contrato,
                        ':modalidad' => $modalidad,
                        ':ubicacion' => $ubicacion,
                        ':carrera_afin' => $carrera_afin,
                        ':salario_ofrecido' => $salario_ofrecido
                    ];

                    if ($id_vacante) {
                        $data[':id_vacante'] = $id_vacante;
                        $model->updateVacante($data);
                        $mensaje = "Vacante actualizada con exito. Seras redirigido en 3 segundos.";
                    } else {
                        $model->insertVacante($data);
                        $mensaje = "¡Vacante publicada con éxito! Serás redirigido en 3 segundos.";
                    }
                    
                    $error = false;
                    header("refresh:3;url=gestion_vacantes.php"); 
                    require_once __DIR__ . '/../views/publicar_vacante.php';
                    exit(); 
                } catch (PDOException $e) {
                    $mensaje = "Error en la base de datos: " . $e->getMessage();
                    $error = true;
                }
            }
        }
        // Generar CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../views/publicar_vacante.php';
    }
}
