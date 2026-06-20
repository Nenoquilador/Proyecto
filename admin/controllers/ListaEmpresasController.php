<?php
require_once __DIR__ . '/../models/EmpresaModel.php';

class ListaEmpresasController {
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
        $filtro_estado = $_GET['estado'] ?? '';
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $empresaModel = new EmpresaModel($this->conexion);
        
        $total_empresas = $empresaModel->countEmpresas($search, $filtro_estado);
        $total_pages = ceil($total_empresas / $limit);

        $empresas = $empresaModel->getEmpresas($search, $filtro_estado, $limit, $offset);
        
        require_once __DIR__ . '/../views/empresas/lista_empresas.php';
    }
}