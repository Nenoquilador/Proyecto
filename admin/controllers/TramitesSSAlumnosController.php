<?php
require_once __DIR__ . '/../models/TramitesSSAlumnosModel.php';

class TramitesSSAlumnosController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();
        
        if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'alumnos') {
            header("Location: ../../login.php");
            exit;
        }

        $model = new TramitesSSAlumnosModel($this->conexion);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $id_tramite = $_POST['id_tramite'] ?? null;

            if ($accion === 'validar_pago' && $id_tramite) {
                $model->validarPagoYActivarEtapa1($id_tramite);
                header("Location: tramites_ss.php?msg=pago_validado");
                exit;
            }
        }

        $tramites = $model->obtenerTodosLosTramites();

        require_once __DIR__ . '/../views/alumnos/tramites_ss.php';
    }
}
