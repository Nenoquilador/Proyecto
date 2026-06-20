<?php
require_once __DIR__ . '/../models/EmpresaModel.php';

class ExportarEmpresasController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function exportar() {
        session_start();
        if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'vinculacion') {
            header("Location: ../../login.php");
            exit();
        }

        $empresaModel = new EmpresaModel($this->conexion);
        $empresas = $empresaModel->getAllEmpresasForExport();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=empresas_justo_sierra_' . date('Y-m-d') . '.csv');
        $output = fopen('php://output', 'w');

        // Add BOM to fix UTF-8 in Excel
        fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

        fputcsv($output, ['ID', 'Nombre Comercial', 'Razón Social', 'RFC', 'Teléfono', 'Correo Contacto', 'Estado']);

        foreach ($empresas as $emp) {
            fputcsv($output, [
                $emp['id_empresa'],
                $emp['nombre_empresa'],
                $emp['razon_social'],
                $emp['rfc'],
                $emp['telefono_contacto'],
                $emp['email_contacto'],
                $emp['estado_validacion']
            ]);
        }

        fclose($output);
        exit();
    }
}