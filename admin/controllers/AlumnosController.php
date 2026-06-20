<?php
require_once __DIR__ . '/../models/AlumnoModel.php';

class AlumnosController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();

        // CANDADO ESTRICTO: SOLO ESCOLARES
        if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'alumnos') {
            header("Location: ../login.php");
            exit();
        }

        $nombre_admin = $_SESSION['nombre_admin'] ?? 'Servicios Escolares';

        $search_term = $_GET['search'] ?? '';
        $filtro_carrera = $_GET['carrera'] ?? '';

        $model = new AlumnoModel($this->conexion);
        $lista_carreras = $model->getCarreras();
        $alumnos = $model->getAlumnos($search_term, $filtro_carrera);

        // Cargar la vista correspondiente
        require_once __DIR__ . '/../views/alumnos/gestionar_alumnos.php';
    }
}
