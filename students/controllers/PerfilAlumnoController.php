<?php
require_once __DIR__ . '/../models/AlumnoModel.php';

class PerfilAlumnoController {
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
        $error_bd = null;
        $perfil = null;
        $nombre_alumno = $_SESSION['nombre_alumno'] ?? 'Alumno';

        try {
            $alumnoModel = new AlumnoModel($this->conexion);
            $perfil = $alumnoModel->obtenerPerfil($id_alumno);
        } catch (PDOException $e) {
            $error_bd = "Error al cargar el perfil: " . $e->getMessage();
        }

        require_once __DIR__ . '/../views/perfil_alumno.php';
    }
}
