<?php
require_once __DIR__ . '/../models/EmpresaModel.php';

class EmpresasDashboardController {
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
        $empresaModel = new EmpresaModel($this->conexion);

        // Fetch basic stats
        $estadisticas = $empresaModel->getEstadisticas();
        
        $saludo = "Hola";
        
        // Find $count_alumnos safely
        try {
            $stmt_alumnos = $this->conexion->query("SELECT COUNT(*) FROM alumnos");
            $count_alumnos = $stmt_alumnos ? $stmt_alumnos->fetchColumn() : 0;
        } catch (PDOException $e) {
            $count_alumnos = 0;
        }
        
        $count_vacantes = $estadisticas['vacantes_activas'] ?? 0;
        $count_pendientes = $estadisticas['empresas_pendientes'] ?? 0;
        $count_sspp = $estadisticas['tramites_activos'] ?? 0;

        $empresas_activas = $empresaModel->getActivas();
        if (!$empresas_activas) $empresas_activas = [];

        // Formatting data for Chart.js
        $vacantesDataRaw = $empresaModel->getDatosGraficoVacantes();
        $vacantesJson = json_encode(array_map(function($v) {
            return [
                'estado' => $v['estado'] ?? 'abierta',
                'total' => $v['total'] ?? 0
            ];
        }, $vacantesDataRaw));

        $crecimientoDataRaw = $empresaModel->getDatosGraficoCrecimientoEmpresas();
        $empresasJson = json_encode(array_map(function($c) {
            return [
                'mes' => $c['mes'] ?? 'N/A',
                'total' => $c['total'] ?? 0
            ];
        }, $crecimientoDataRaw));

        require_once __DIR__ . '/../views/empresas/dashboard.php';
    }
}