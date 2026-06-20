<?php
require_once __DIR__ . '/../models/EmpresaModel.php';

class GestionarEmpresaController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'vinculacion') {
            header("Location: ../../login.php");
            exit();
        }

        $nombre_admin = $_SESSION['nombre_admin'] ?? 'Admin Vinculación';
        $id_empresa = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id_empresa <= 0) {
            header("Location: lista_empresas.php");
            exit();
        }

        $empresaModel = new EmpresaModel($this->conexion);
        $mensaje = $_GET['msg'] ?? '';
        $tipo_mensaje = $_GET['status'] ?? '';

        $todas_las_carreras = [
            "Arquitectura",
            "Contaduría Pública",
            "Derecho",
            "Gastronomía",
            "Odontología",
            "Psicología",
            "Ingeniería de Software",
            "Medicina",
            "Nutrición",
            "Pedagogía",
            "Administración de Empresas"
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            
            try {
                if ($accion === 'cambiar_estado') {
                    $estado_nuevo = $_POST['estado_validacion'];
                    $empresaModel->updateEstado($id_empresa, $estado_nuevo);
                    
                    $notas = $_POST['notas_internas'] ?? '';
                    $carreras_post = $_POST['carreras'] ?? [];
                    $carreras_json = json_encode($carreras_post);
                    
                    $empresaModel->updateNotas($id_empresa, $notas, $carreras_json);
                    
                    header("Location: gestionar_empresa.php?id=$id_empresa&status=success&msg=" . urlencode("Cambios guardados correctamente."));
                    exit();
                }
            } catch (Exception $e) {
                $mensaje = "Ocurrió un error: " . $e->getMessage();
                $tipo_mensaje = "error";
            }
        }

        $empresa = $empresaModel->getEmpresaById($id_empresa);
        if (!$empresa) {
            header("Location: lista_empresas.php");
            exit();
        }
        
        $carreras_guardadas = json_decode($empresa['carreras_interes'] ?? '[]', true);
        if (!is_array($carreras_guardadas)) $carreras_guardadas = [];

        require_once __DIR__ . '/../views/empresas/gestionar_empresa.php';
    }
}