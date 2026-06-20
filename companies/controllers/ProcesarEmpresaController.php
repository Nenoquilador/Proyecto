<?php
require_once __DIR__ . '/../models/CompanyModel.php';
require_once __DIR__ . '/../models/VacanteCompanyModel.php';

class ProcesarEmpresaController {
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

        $id_empresa = $_SESSION['id_empresa'];
        $mensaje = '';
        $error = false;
        $datos_empresa = null;

        $vacanteModel = new VacanteCompanyModel($this->conexion);
        $companyModel = new CompanyModel($this->conexion);

        if (isset($_REQUEST['action'])) {
            if ($_REQUEST['action'] === 'cerrar' && isset($_GET['id_vacante'])) {
                $id_vacante = $_GET['id_vacante'];
                try {
                    $vacanteModel->cerrarVacante($id_vacante, $id_empresa);
                    header("Location: gestion_vacantes.php?msg=cerrada");
                    exit();
                } catch (PDOException $e) {
                    die("Error al cerrar: " . $e->getMessage());
                }
            } elseif ($_REQUEST['action'] === 'duplicar_vacante' && isset($_POST['id_vacante'])) {
                $id_vacante = $_POST['id_vacante'];
                try {
                    $vacanteModel->duplicarVacante($id_vacante, $id_empresa);
                    header("Location: gestion_vacantes.php?msg=duplicada");
                    exit();
                } catch (PDOException $e) {
                    die("Error al duplicar: " . $e->getMessage());
                }
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_perfil'])) {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $rfc = trim($_POST['rfc'] ?? '');
            $sitio_web = trim($_POST['sitio_web'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            
            $carreras_array = $_POST['carreras'] ?? [];
            $carreras_afines = implode(', ', $carreras_array);
            
            if (empty($nombre) || empty($email) || empty($rfc)) {
                $mensaje = "Error: Nombre, Email y RFC son obligatorios.";
                $error = true;
            } else {
                try {
                    $ruta_relativa = null;
                    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                        $dir_logos = '../assets/img/logos_empresas/';
                        if (!file_exists($dir_logos)) mkdir($dir_logos, 0777, true);
                        $nombre_logo = time() . '_' . basename($_FILES['logo']['name']);
                        $ruta_destino = $dir_logos . $nombre_logo;
                        $ruta_relativa = 'assets/img/logos_empresas/' . $nombre_logo;
                        move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_destino);
                    }

                    $ruta_relativa_b = null;
                    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
                        $dir_banners = '../assets/img/banners_empresas/';
                        if (!file_exists($dir_banners)) mkdir($dir_banners, 0777, true);
                        $nombre_banner = time() . '_banner_' . basename($_FILES['banner']['name']);
                        $ruta_destino_b = $dir_banners . $nombre_banner;
                        $ruta_relativa_b = 'assets/img/banners_empresas/' . $nombre_banner;
                        move_uploaded_file($_FILES['banner']['tmp_name'], $ruta_destino_b);
                    }

                    $companyModel->updatePerfil($id_empresa, $nombre, $email, $rfc, $sitio_web, $descripcion, $carreras_afines, $ruta_relativa, $ruta_relativa_b);
                    
                    $_SESSION['nombre_empresa'] = $nombre;

                    $mensaje = "¡Perfil actualizado con éxito! Serás redirigido en 2 segundos.";
                    $error = false;
                    header("refresh:2;url=perfil_empresa.php"); 
                    require_once __DIR__ . '/../views/procesar_empresa.php';
                    exit(); 
                } catch (PDOException $e) {
                    $mensaje = "Error al actualizar en la base de datos: " . $e->getMessage();
                    $error = true;
                }
            }
        }

        try {
            $datos_empresa = $companyModel->getPerfil($id_empresa);
            if (!$datos_empresa) {
                die("Error: No se pudieron cargar los datos para edición.");
            }
        } catch (PDOException $e) {
            die("Error al cargar los datos de edición: " . $e->getMessage());
        }

        require_once __DIR__ . '/../views/procesar_empresa.php';
    }
}
