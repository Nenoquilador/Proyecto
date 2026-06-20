<?php
require_once __DIR__ . '/../models/VacanteModel.php';

class DetalleVacanteController {
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

        $id_vacante = $_GET['id'] ?? null;
        if (!$id_vacante || !is_numeric($id_vacante)) {
            header("Location: dashboard.php");
            exit();
        }

        $id_alumno = $_SESSION['id_alumno'];
        $vacante = null;
        $error_bd = null;

        try {
            $vacanteModel = new VacanteModel($this->conexion);
            $vacante = $vacanteModel->obtenerDetalle($id_vacante, $id_alumno);

            if (!$vacante) {
                $error_bd = "Vacante no encontrada o cerrada.";
            } else {
                $vacanteModel->registrarVista($id_vacante, $id_alumno);
            }
        } catch (PDOException $e) {
            $error_bd = "Error al cargar la vacante: " . $e->getMessage();
        }

        require_once __DIR__ . '/../views/detalle_vacante.php';
    }
}
