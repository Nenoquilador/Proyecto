<?php
require_once __DIR__ . '/../models/EmpresaModel.php';

class DetalleTramiteController {
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
        $id_solicitud = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id_solicitud <= 0) {
            header("Location: gestionar_tramites.php");
            exit();
        }

        $empresaModel = new EmpresaModel($this->conexion);
        $mensaje = '';
        $tipo_mensaje = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            
            try {
                if ($accion === 'enviar_formato') {
                    $empresaModel->updateEstadoTramite($id_solicitud, 'Formato Enviado');
                    $mensaje = "Formato marcado como enviado exitosamente.";
                    $tipo_mensaje = "success";
                } elseif ($accion === 'validar_telefono') {
                    $empresaModel->updateEstadoTramite($id_solicitud, 'Validado por Teléfono', true);
                    $mensaje = "Validación telefónica registrada.";
                    $tipo_mensaje = "success";
                } elseif ($accion === 'aprobar_final') {
                    if (isset($_FILES['archivo_catalogo']) && $_FILES['archivo_catalogo']['error'] === UPLOAD_ERR_OK) {
                        $tramite_temp = $empresaModel->getTramiteById($id_solicitud);
                        $id_emp = $tramite_temp['id_empresa'];
                        
                        $uploadDir = __DIR__ . '/../../uploads/sspp_catalogo/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        $fileName = "CATALOGO_E" . $id_emp . "_" . time() . ".pdf";
                        $destPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($_FILES['archivo_catalogo']['tmp_name'], $destPath)) {
                            $ruta_relativa = "uploads/sspp_catalogo/" . $fileName;
                            $empresaModel->finalizarRegistroCatalogo($id_solicitud, $id_emp, $ruta_relativa);
                            $mensaje = "Trámite finalizado y permisos activados.";
                            $tipo_mensaje = "success";
                        } else {
                            $mensaje = "Error al subir el archivo PDF.";
                            $tipo_mensaje = "error";
                        }
                    } else {
                        $mensaje = "Por favor selecciona un archivo válido.";
                        $tipo_mensaje = "error";
                    }
                }
            } catch (Exception $e) {
                $mensaje = "Ocurrió un error: " . $e->getMessage();
                $tipo_mensaje = "error";
            }
        }

        $tramite = $empresaModel->getTramiteById($id_solicitud);
        if (!$tramite) {
            header("Location: gestionar_tramites.php");
            exit();
        }

        require_once __DIR__ . '/../views/empresas/detalle_tramite.php';
    }
}