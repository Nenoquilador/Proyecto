<?php
require_once __DIR__ . '/../models/AlumnoModel.php';

class AlumnosDashboardController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();

        if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'alumnos') {
            header("Location: ../login.php");
            exit();
        }

        $nombre_admin = $_SESSION['nombre_admin'] ?? 'Servicios Escolares';

        $model = new AlumnoModel($this->conexion);
        $count_alumnos = $model->countAlumnos();
        $count_cvs = $model->countCvs();

        require_once __DIR__ . '/../views/alumnos/dashboard.php';
    }
}
