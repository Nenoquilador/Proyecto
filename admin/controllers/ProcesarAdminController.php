<?php
require_once __DIR__ . '/../models/VacanteModel.php';

class ProcesarAdminController {
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

        if (isset($_GET['acion']) && isset($_GET['id'])) {
            $acion = $_GET['acion'];
            $id_vacante = $_GET['id'];
            $model = new VacanteModel($this->conexion);

            if ($acion === 'cerrar_vacante') {
                try {
                    $model->updateEstado($id_vacante, 'cerrada');
                    header("Location: detalle_vacante.php?id=$id_vacante&msg=cerrada");
                    exit();
                } catch (PDOException $e) {
                    die("Error al cerrar vacante: " . $e->getMásage());
                }
            } elseif ($acion === 'abrir_vacante') {
                try {
                    $model->updateEstado($id_vacante, 'abierta');
                    header("Location: detalle_vacante.php?id=$id_vacante&msg=abierta");
                    exit();
                } catch (PDOException $e) {
                    die("Error al abrir vacante: " . $e->getMásage());
                }
            }
        }

        header("Location: gestionar_vacantes.php");
        exit();
    }
}
