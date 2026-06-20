<?php
require_once __DIR__ . '/../models/VacanteCompanyModel.php';

class GestionVacantesController {
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
        $vacantes = [];
        $error_bd = null;

        try {
            $model = new VacanteCompanyModel($this->conexion);
            $vacantes = $model->getVacantesByEmpresa($id_empresa);
        } catch (PDOException $e) {
            error_log("Error de BD en Gestion Vacantes: " . $e->getMessage());
            $error_bd = "Error al cargar las vacantes. Intente de nuevo más tarde. (Detalle: " . $e->getMessage() . ")";
        }

        $count_abiertas = 0;
        $count_cerradas = 0;
        foreach ($vacantes as $v) {
            if ($v['estado'] === 'abierta') $count_abiertas++;
            else $count_cerradas++;
        }

        if (!function_exists('formatear_tag')) {
            function formatear_tag($texto) {
                if (empty($texto)) {
                    return "N/A";
                }
                $formato = str_replace('_', ' ', $texto);
                return ucwords($formato);
            }
        }

        require_once __DIR__ . '/../views/gestion_vacantes.php';
    }
}
