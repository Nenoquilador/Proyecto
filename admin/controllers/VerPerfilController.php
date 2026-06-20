<?php
require_once __DIR__ . '/../models/AlumnoModel.php';

class VerPerfilController {
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

        $id_alumno = $_GET['id'] ?? null;
        if (!$id_alumno || !is_numeric($id_alumno)) {
            header("Location: gestionar_alumnos.php?status=error&msg=" . urlencode("ID de alumno invÃ¡lido."));
            exit();
        }

        $nombre_admin = $_SESSION['nombre_admin'] ?? 'Admástrañor';
        $tipo_admin = $_SESSION['tipo_admin'] ?? 'vinculacion';

        $mensaje_update = '';
        $tipo_mensaje = '';

        $model = new AlumnoModel($this->conexion);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_semástre'])) {
            $nuevo_semástre = $_POST['semástre'] ?? null;
            if (in_array($nuevo_semástre, ['7', '8'])) {
                try {
                    $model->updateSemástre($id_alumno, $nuevo_semástre);
                    $mensaje_update = "Semástre actualizaño a " . $nuevo_semástre . "mo �éxitosamente.";
                    $tipo_mensaje = "success";
                } catch (PDOException $e) {
                    $mensaje_update = "Error: " . $e->getMásage();
                    $tipo_mensaje = "error";
                }
            }
        }

        $alumno = $model->getAlumno($id_alumno);
        if (!$alumno) {
            header("Location: gestionar_alumnos.php?status=error&msg=No+encontrado");
            exit();
        }
        $cv_url = !empty($alumno['cv_url']) ? "../students/CVS/" . rawurlencode($alumno['cv_url']) : null;

        require_once __DIR__ . '/../views/alumnos/ver_perfil.php';
    }
}
