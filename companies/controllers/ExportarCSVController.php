<?php
require_once __DIR__ . '/../models/PostulacionModel.php';

class ExportarCSVController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function exportar() {
        session_start();

        if (!isset($_SESSION['id_empresa'])) {
            header("Location: ../login.php"); 
            exit();
        }

        $id_empresa = $_SESSION['id_empresa'];
        $id_vacante = $_GET['id_vacante'] ?? null;

        if (empty($id_vacante) || !is_numeric($id_vacante)) {
            header("Location: gestion_vacantes.php?error=invalid_id");
            exit();
        }

        $model = new PostulacionModel($this->conexion);
        $vacante_titulo = $model->getTituloVacante($id_vacante, $id_empresa);

        if (!$vacante_titulo) {
            header("Location: gestion_vacantes.php?error=unauthorized");
            exit();
        }

        $postulantes = $model->getPostulantesByVacante($id_vacante);

        // Limpiar el buffer para evitar que se mezcle HTML
        if (ob_get_length()) ob_end_clean();

        // Configurar cabeceras para forzar descarga CSV
        $filename = "postulantes_vacante_" . $id_vacante . "_" . date('Ymd_His') . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Para evitar problemas con caracteres especiales en Excel (BOM UTF-8)
        fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

        // Escribir fila de cabecera
        fputcsv($output, [
            'ID Postulación', 
            'Nombre', 
            'Apellidos', 
            'Matrícula', 
            'Carrera', 
            'Email', 
            'Estado', 
            'Fecha Postulación', 
            'Notas Empresa'
        ]);

        // Escribir datos
        foreach ($postulantes as $row) {
            fputcsv($output, [
                $row['id_postulacion'],
                $row['nombre'],
                $row['apellidos'],
                $row['matricula'],
                $row['carrera'],
                $row['email'],
                $row['estado_postulacion'],
                $row['fecha_postulacion'],
                $row['notas_empresa']
            ]);
        }

        fclose($output);
        exit();
    }
}
