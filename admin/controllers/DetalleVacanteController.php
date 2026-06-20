<?php
require_once __DIR__ . '/../models/VacanteModel.php';

class DetalleVacanteController {
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
        $id_vacante = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id_vacante <= 0) {
            header("Location: gestionar_vacantes.php");
            exit();
        }

        $vacanteModel = new VacanteModel($this->conexion);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            if ($accion === 'cerrar_vacante') {
                $vacanteModel->updateEstado($id_vacante, 'cerrada');
                // Could set a flash message here
                header("Location: DetalleVacanteController.php?id=" . $id_vacante);
                exit();
            }
        }

        $vacante = $vacanteModel->getDetalleVacante($id_vacante);
        if (!$vacante) {
            header("Location: gestionar_vacantes.php");
            exit();
        }

        require_once __DIR__ . '/../views/empresas/detalle_vacante.php';
    }
}