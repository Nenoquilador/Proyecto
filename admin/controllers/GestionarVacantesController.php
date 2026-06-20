<?php
require_once __DIR__ . '/../models/VacanteModel.php';

class GestionarVacantesController {
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
        $search = $_GET['search'] ?? '';
        $estado = $_GET['estado'] ?? '';
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $vacanteModel = new VacanteModel($this->conexion);
        
        $total_vacantes = $vacanteModel->countVacantes($search);
        $total_pages = ceil($total_vacantes / $limit);

        // Fetch vacantes - notice no $estado param here based on VacanteModel.php
        $vacantes = $vacanteModel->getVacantes($search, $limit, $offset);
        
        // Manual filtering by estado if needed
        if ($estado !== '') {
            $vacantes = array_filter($vacantes, function($v) use ($estado) {
                return $v['estado'] === $estado;
            });
        }
        
        require_once __DIR__ . '/../views/empresas/gestionar_vacantes.php';
    }
}