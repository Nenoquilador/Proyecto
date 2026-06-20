<?php
class PostulacionModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerMisPostulaciones($id_alumno) {
        $sql = "SELECT
                    p.id_postulacion, 
                    p.fecha_postulacion, 
                    p.estado_postulacion,
                    v.titulo AS titulo_vacante, 
                    v.id_vacante,
                    e.nombre_empresa,
                    e.logo_url AS logo_empresa
                FROM
                    Postulaciones AS p
                JOIN
                    Vacantes AS v ON p.id_vacante = v.id_vacante
                JOIN
                    Empresas AS e ON v.id_empresa = e.id_empresa
                WHERE
                    p.id_alumno = :id_alumno
                ORDER BY
                    p.fecha_postulacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verificarPostulacion($id_alumno, $id_vacante) {
        $sql_check = "SELECT COUNT(*) FROM Postulaciones WHERE id_alumno = :id_alumno AND id_vacante = :id_vacante";
        $stmt_check = $this->conexion->prepare($sql_check);
        $stmt_check->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt_check->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
        $stmt_check->execute();
        return $stmt_check->fetchColumn() > 0;
    }

    public function insertarPostulacion($id_alumno, $id_vacante) {
        $sql_insert = "INSERT INTO Postulaciones (id_alumno, id_vacante, estado_postulacion, fecha_postulacion) 
                       VALUES (:id_alumno, :id_vacante, 'enviada', NOW())";
        
        $stmt_insert = $this->conexion->prepare($sql_insert);
        $stmt_insert->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt_insert->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
        return $stmt_insert->execute();
    }
}
