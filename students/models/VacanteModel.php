<?php
class VacanteModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function buscarVacantesActivas($termino_busqueda = '', $tipo_contrato_filtro = '', $carrera_filtro = '') {
        $params = [];
        $where_conditions = ["v.estado = 'abierta'"]; 

        if (!empty($termino_busqueda)) {
            $where_conditions[] = "(v.titulo LIKE :search OR e.nombre_empresa LIKE :search OR v.ubicacion LIKE :search)";
            $params[':search'] = '%' . $termino_busqueda . '%';
        }

        if (!empty($tipo_contrato_filtro)) {
            $where_conditions[] = "v.tipo_contrato = :contrato";
            $params[':contrato'] = $tipo_contrato_filtro;
        }
        
        if (!empty($carrera_filtro)) {
            $where_conditions[] = "v.carrera_afin = :carrera"; 
            $params[':carrera'] = $carrera_filtro;
        }

        $sql = "SELECT id_vacante, titulo, ubicacion, estado, tipo_contrato, modalidad, fecha_publicacion, nombre_empresa
                FROM Vacantes v
                JOIN Empresas e ON v.id_empresa = e.id_empresa";
                
        if (count($where_conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }

        $sql .= " ORDER BY v.fecha_publicacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalle($id_vacante, $id_alumno) {
        $sql = "SELECT 
                    v.titulo, v.descripcion, v.ubicacion, v.modalidad, v.tipo_contrato, v.salario_ofrecido, v.fecha_publicacion,
                    e.nombre_empresa, e.sitio_web,
                    (SELECT COUNT(*) FROM Postulaciones p WHERE p.id_alumno = :id_alumno AND p.id_vacante = :id_vacante) AS ya_postulado
                FROM 
                    Vacantes v
                JOIN 
                    Empresas e ON v.id_empresa = e.id_empresa
                WHERE 
                    v.id_vacante = :id_vacante AND v.estado = 'abierta'";
                
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarVista($id_vacante, $id_alumno) {
        try {
            $sql_crear_tabla = "
                CREATE TABLE IF NOT EXISTS vistas_vacantes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    id_vacante INT NOT NULL,
                    id_alumno INT NULL,
                    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ";
            $this->conexion->exec($sql_crear_tabla);

            $sql_vista = "INSERT INTO vistas_vacantes (id_vacante, id_alumno) VALUES (:id_vacante, :id_alumno)";
            $stmt_vista = $this->conexion->prepare($sql_vista);
            $stmt_vista->execute([
                ':id_vacante' => $id_vacante,
                ':id_alumno' => $id_alumno
            ]);
        } catch (PDOException $e) {
            error_log("Error al registrar vista de vacante: " . $e->getMessage());
        }
    }
}
