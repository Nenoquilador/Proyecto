<?php
require_once __DIR__ . '/../models/PostulacionModel.php';

class VerPostulacionesController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();

        if (!isset($_SESSION['id_empresa'])) {
            header("Location: ../login.php"); 
            exit();
        }

        $id_empresa = $_SESSION['id_empresa'];
        $id_vacante = $_GET['id_vacante'] ?? null;
        $postulantes = [];
        $vacante_titulo = '';
        $error_db = null;

        $mensaje = '';
        $tipo_mensaje = '';

        if (empty($id_vacante) || !is_numeric($id_vacante)) {
            header("Location: gestion_vacantes.php?error=invalid_id");
            exit();
        }

        $model = new PostulacionModel($this->conexion);

        try {
            $vacante_titulo = $model->getTituloVacante($id_vacante, $id_empresa);

            if (!$vacante_titulo) {
                header("Location: gestion_vacantes.php?error=unauthorized");
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['ajax_action'])) {
                    header('Content-Type: application/json');
                    $id_postulacion = $_POST['id_postulacion'] ?? null;
                    
                    if ($_POST['ajax_action'] == 'update_status') {
                        $new_status = $_POST['new_status'] ?? null;
                        if ($id_postulacion && $new_status) {
                            $success = $model->updateStatus($id_postulacion, $new_status, $id_empresa);
                            echo json_encode(['success' => $success]);
                            exit();
                        }
                    } elseif ($_POST['ajax_action'] == 'update_notes') {
                        $notas = $_POST['notas'] ?? '';
                        if ($id_postulacion) {
                            $success = $model->updateNotas($id_postulacion, $notas, $id_empresa);
                            echo json_encode(['success' => $success]);
                            exit();
                        }
                    }
                    echo json_encode(['success' => false]);
                    exit();
                } elseif (isset($_POST['action'])) {
                    $id_postulacion = $_POST['id_postulacion'] ?? null;
                    $new_status = $_POST['new_status'] ?? null;
                    if ($id_postulacion && $new_status) {
                        if ($model->updateStatus($id_postulacion, $new_status, $id_empresa)) {
                            $mensaje = "Estado actualizado correctamente";
                            $tipo_mensaje = "success";
                        } else {
                            $mensaje = "Error al actualizar el estado";
                            $tipo_mensaje = "error";
                        }
                    }
                }
            }

            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) $page = 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $filtros = [
                'carrera' => $_GET['carrera'] ?? '',
                'estado' => $_GET['estado'] ?? ''
            ];

            $postulantes = $model->getPostulantesFiltrados($id_vacante, $filtros, $limit, $offset);
            $total_postulantes = $model->countPostulantesFiltrados($id_vacante, $filtros);
            $total_pages = ceil($total_postulantes / $limit);
            
            $carreras_disponibles = $model->getCarrerasByVacante($id_vacante);

        } catch (PDOException $e) {
            $error_db = "Error al cargar los postulantes: " . $e->getMessage();
        }

        require_once __DIR__ . '/../views/ver_postulaciones.php';
    }
}
