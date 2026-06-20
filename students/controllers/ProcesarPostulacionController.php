<?php
require_once __DIR__ . '/../models/PostulacionModel.php';

class ProcesarPostulacionController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function procesar() {
        session_start();

        if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
            header("Location: ../login.php"); 
            exit();
        }

        $id_alumno = $_SESSION['id_alumno'];
        $id_vacante = $_GET['id'] ?? null;

        if (!$id_vacante || !is_numeric($id_vacante)) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['exito' => false, 'mensaje' => 'ID de vacante inválido']);
                exit();
            }
            header("Location: dashboard.php");
            exit();
        }

        $mensaje = "Tu postulación ha sido enviada con éxito. Serás redirigido en 3 segundos.";
        $exito = true;

        try {
            $postulacionModel = new PostulacionModel($this->conexion);
            
            if ($postulacionModel->verificarPostulacion($id_alumno, $id_vacante)) {
                $mensaje = "Ya te habías postulado a esta vacante anteriormente. No se procesó una nueva.";
                $exito = true; 
            } else {
                $postulacionModel->insertarPostulacion($id_alumno, $id_vacante);
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['exito' => true, 'mensaje' => $mensaje]);
                exit();
            }
            
            header("refresh:3;url=dashboard.php");

        } catch (PDOException $e) {
            $mensaje = "Error de base de datos al procesar la postulación: " . $e->getMessage();
            $exito = false;
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['exito' => false, 'mensaje' => $mensaje]);
                exit();
            }
            
            header("refresh:5;url=detalle_vacante.php?id=" . $id_vacante);
        }

        require_once __DIR__ . '/../views/procesar_postulacion.php';
    }
}
