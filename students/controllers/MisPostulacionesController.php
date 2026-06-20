<?php
require_once __DIR__ . '/../models/PostulacionModel.php';

class MisPostulacionesController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();

        if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
            header("Location: ../login.php");
            exit;
        }

        $id_alumno = $_SESSION['id_alumno'];
        $nombre_alumno = $_SESSION['nombre_alumno'] ?? 'Alumno';
        $error_bd = null;
        $postulaciones = [];

        try {
            $postulacionModel = new PostulacionModel($this->conexion);
            $postulaciones = $postulacionModel->obtenerMisPostulaciones($id_alumno);
        } catch (PDOException $e) {
            $error_bd = "Error al cargar tu historial de postulaciones: " . $e->getMessage();
        }

        require_once __DIR__ . '/../views/mis_postulaciones.php';
    }
}
