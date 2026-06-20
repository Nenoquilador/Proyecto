<?php

class VacanteCompanyModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function getVacantesByEmpresa($id_empresa) {
        $sql = "SELECT v.*, (SELECT COUNT(*) FROM postulaciones WHERE id_vacante = v.id_vacante) as total_postulaciones 
                FROM vacantes v 
                WHERE v.id_empresa = :id_empresa 
                ORDER BY v.fecha_publicacion DESC";
                
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVacanteForEdit($id_vacante, $id_empresa) {
        $sql = "SELECT titulo, descripcion, tipo_contrato, modalidad, ubicacion, salario_ofrecido, carrera_afin 
                FROM Vacantes 
                WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertVacante($data) {
        $sql = "INSERT INTO Vacantes (id_empresa, titulo, descripcion, tipo_contrato, modalidad, ubicacion, carrera_afin, salario_ofrecido, estado) 
                VALUES (:id_empresa, :titulo, :descripcion, :tipo_contrato, :modalidad, :ubicacion, :carrera_afin, :salario_ofrecido, 'abierta')";
        
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute($data);
    }

    public function updateVacante($data) {
        $sql = "UPDATE Vacantes SET 
                    titulo = :titulo, 
                    descripcion = :descripcion, 
                    tipo_contrato = :tipo_contrato, 
                    modalidad = :modalidad, 
                    ubicacion = :ubicacion,
                    carrera_afin = :carrera_afin,
                    salario_ofrecido = :salario_ofrecido
                WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";
        
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute($data);
    }

    public function cerrarVacante($id_vacante, $id_empresa) {
        $sql = "UPDATE vacantes SET estado = 'cerrada' WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':id_vacante' => $id_vacante, ':id_empresa' => $id_empresa]);
    }

    public function duplicarVacante($id_vacante, $id_empresa) {
        $sql = "INSERT INTO vacantes (id_empresa, titulo, descripcion, tipo_contrato, modalidad, salario_ofrecido, carrera_afin, ubicacion, estado, fecha_publicacion) 
                SELECT id_empresa, CONCAT(titulo, ' (Copia)'), descripcion, tipo_contrato, modalidad, salario_ofrecido, carrera_afin, ubicacion, 'abierta', CURRENT_TIMESTAMP 
                FROM vacantes WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':id_vacante' => $id_vacante, ':id_empresa' => $id_empresa]);
    }
}
