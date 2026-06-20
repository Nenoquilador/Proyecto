<?php
require_once __DIR__ . '/../models/VacanteModel.php';

class DashboardController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();

        if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
            header("Location: ../login.php"); 
            exit();
        }

        $nombre_alumno = $_SESSION['nombre_alumno'] ?? 'Alumno';
        $vacantes = [];
        $error_busqueda = null;

        // LÓGICA DE BÚSQUEDA (GET)
        $termino_busqueda = $_GET['search'] ?? '';
        $tipo_contrato_filtro = $_GET['contrato'] ?? '';
        $carrera_filtro = $_GET['carrera'] ?? ''; 

        try {
            $vacanteModel = new VacanteModel($this->conexion);
            $vacantes = $vacanteModel->buscarVacantesActivas($termino_busqueda, $tipo_contrato_filtro, $carrera_filtro);
        } catch (PDOException $e) {
            error_log("Error de BD al buscar vacantes: " . $e->getMessage());
            $error_busqueda = "Error al conectar con el catálogo de vacantes. Por favor, intente más tarde.";
        }

        // Pasar variables a la vista
        require_once __DIR__ . '/../views/dashboard.php';
    }
}
