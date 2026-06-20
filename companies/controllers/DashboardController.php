<?php
require_once __DIR__ . '/../models/CompanyModel.php';
require_once __DIR__ . '/../models/AnalyticsModel.php';

class DashboardController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();

        if (!isset($_SESSION['id_empresa']) || ($_SESSION['rol'] ?? '') !== 'empresa') {
            header("Location: ../login.php"); 
            exit();
        }

        $id_empresa = $_SESSION['id_empresa'];
        $nombre_empresa = $_SESSION['nombre_empresa'] ?? 'Empresa Registrada'; 
        
        $active_vacancies = 0;
        $total_applications = 0;
        $closed_vacancies = 0;
        $perfiles_sugeridos = [];
        $error_bd = null; 
        $mensaje_subida = '';

        $model = new CompanyModel($this->conexion);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['formato_sspp'])) {
            if ($_FILES['formato_sspp']['error'] === UPLOAD_ERR_OK) {
                $max_size = 5 * 1024 * 1024; // 5MB
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($_FILES['formato_sspp']['tmp_name']);
                
                $allowed_mimes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg',
                    'image/png'
                ];
                
                if ($_FILES['formato_sspp']['size'] > $max_size) {
                    $mensaje_subida = "<div class='mensaje error'>El archivo supera el límite de 5MB.</div>";
                } elseif (!in_array($mime_type, $allowed_mimes)) {
                    $mensaje_subida = "<div class='mensaje error'>Formato de archivo no permitido. Solo se aceptan documentos e imágenes.</div>";
                } else {
                    $dir_subida = '../archivos_sspp/formatos_empresas/';
                    if (!file_exists($dir_subida)) {
                        mkdir($dir_subida, 0777, true);
                    }
                    
                    $extension = pathinfo($_FILES['formato_sspp']['name'], PATHINFO_EXTENSION);
                    $extension = strtolower($extension);
                    $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                    if (!in_array($extension, $allowed_extensions)) {
                        $extension = 'pdf'; // Fallback seguro
                    }
                    
                    $nombre_archivo = hash('sha256', uniqid('', true)) . '.' . $extension;
                    $ruta_destino = $dir_subida . $nombre_archivo;
                    $ruta_relativa = 'archivos_sspp/formatos_empresas/' . $nombre_archivo;
                    
                    if (move_uploaded_file($_FILES['formato_sspp']['tmp_name'], $ruta_destino)) {
                        try {
                            $model->processUploadSSPP($id_empresa, $ruta_relativa);
                            $mensaje_subida = "<div class='mensaje exito'><i class='fas fa-check-circle'></i> ¡Documento enviado con éxito! Vinculación revisará tu información.</div>";
                        } catch (PDOException $e) {
                            $mensaje_subida = "<div class='mensaje error'>Error en base de datos: " . $e->getMessage() . "</div>";
                        }
                    } else {
                        $mensaje_subida = "<div class='mensaje error'>Error al guardar el archivo. Revisa permisos de carpeta.</div>";
                    }
                }
            } else {
                $mensaje_subida = "<div class='mensaje error'>Error en el archivo subido.</div>";
            }
        }

        try {
            $estado_empresa = $model->getEstadoValidacion($id_empresa);

            if ($estado_empresa === 'aprobada') {
                $stats = $model->getEstadisticas($id_empresa);
                $active_vacancies = $stats['active_vacancies'];
                $total_applications = $stats['total_applications'];
                $closed_vacancies = $stats['closed_vacancies'];
                $meses_data = $stats['meses_data'];

                require_once __DIR__ . '/../models/MatchModel.php';
                $matchModel = new MatchModel($this->conexion);
                $perfiles_sugeridos = $matchModel->getTopMatchesForEmpresa($id_empresa);

                $analyticsModel = new AnalyticsModel($this->conexion);
                $metricas_conversion = $analyticsModel->getMetricasConversion($id_empresa);
                
                $conversion_labels = [];
                $conversion_vistas = [];
                $conversion_postulaciones = [];
                $conversion_tasas = [];

                foreach ($metricas_conversion as $mc) {
                    $conversion_labels[] = $mc['titulo'];
                    $conversion_vistas[] = $mc['total_vistas'];
                    $conversion_postulaciones[] = $mc['total_postulaciones'];
                    $conversion_tasas[] = $mc['tasa_conversion'];
                }

                $json_conversion_labels = json_encode(empty($conversion_labels) ? ['Sin Vacantes'] : $conversion_labels);
                $json_conversion_vistas = json_encode(empty($conversion_vistas) ? [0] : $conversion_vistas);
                $json_conversion_postulaciones = json_encode(empty($conversion_postulaciones) ? [0] : $conversion_postulaciones);
                $json_conversion_tasas = json_encode(empty($conversion_tasas) ? [0] : $conversion_tasas);

                $chart_labels = [];
                $chart_data = [];
                $meses_nombres = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                
                foreach(array_reverse($meses_data) as $row) {
                    $chart_labels[] = $meses_nombres[(int)$row['mes']];
                    $chart_data[] = $row['total'];
                }
                $chart_labels_json = json_encode(empty($chart_labels) ? ['Sin Datos'] : $chart_labels);
                $chart_data_json = json_encode(empty($chart_data) ? [0] : $chart_data);
            }
        } catch (PDOException $e) {
            $error_bd = "Error de conexión: " . $e->getMessage();
        }

        require_once __DIR__ . '/../views/dashboard.php';
    }
}
