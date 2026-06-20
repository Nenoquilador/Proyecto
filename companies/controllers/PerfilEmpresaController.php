<?php
require_once __DIR__ . '/../models/CompanyModel.php';

class PerfilEmpresaController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();

        if (!isset($_SESSION['id_empresa']) || ($_SESSION['rol'] ?? '') !== 'empresa') {
            header('Location: ../login.php'); 
            exit();
        }

        $id_empresa = $_SESSION['id_empresa'];

        try {
            $model = new CompanyModel($this->conexion);
            $empresa = $model->getPerfil($id_empresa);

            if (!$empresa) {
                session_destroy();
                header('Location: ../login.php?error=Datos de empresa no encontrados');
                exit();
            }
        } catch (PDOException $e) {
            die("Error en la consulta de base de datos: " . $e->getMessage()); 
        }

        $enlace_edicion = "procesar_empresa.php?action=editar_perfil"; 
        
        require_once __DIR__ . '/../views/perfil_empresa.php';
    }
}
