<?php
require_once __DIR__ . '/../models/EmpresaModel.php';

class GestionarTramitesController {
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
        
        $total_tramites = $empresaModel->countTramites($search, $filtro_estado);
        $total_pages = ceil($total_tramites / $limit);

        $tramites = $empresaModel->getTramites($search, $filtro_estado, $limit, $offset);

        // Helper function
        function obtenerClaseEstado($estado) {
            switch ($estado) {
                case 'Solicitud Inicial': return 'aprobada'; // Uses green
                case 'Formato Enviado': return 'pendiente'; // Uses amber
                case 'Datos Recibidos': return 'abierta'; // Uses blue
                case 'Validado por Teléfono': return 'cerrada'; // Uses purple
                case 'Aprobado Catálogo': return 'activa'; // Uses success green
                default: return 'rechazada'; // Uses red/grey
            }
        }
        
        require_once __DIR__ . '/../views/empresas/gestionar_tramites.php';
    }
}