<?php
class MatchModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function getTopMatchesForEmpresa($id_empresa) {
        // Encontrar perfiles de alumnos que coincidan con las vacantes activas y que no se hayan postulado
        $sql = "
            SELECT 
                a.id_alumno,
                a.nombre,
                a.apellidos,
                a.carrera,
                a.email,
                a.cv_url,
                v.titulo AS vacante_recomendada,
                v.id_vacante
            FROM 
                Alumnos a
            JOIN 
                Vacantes v ON (a.carrera LIKE CONCAT('%', v.carrera_afin, '%') OR v.carrera_afin LIKE CONCAT('%', a.carrera, '%') OR a.carrera = v.carrera_afin)
            WHERE 
                v.id_empresa = :id_empresa
                AND v.estado = 'activa'
                AND a.id_alumno NOT IN (
                    SELECT p.id_alumno 
                    FROM Postulaciones p 
                    WHERE p.id_vacante = v.id_vacante
                )
            GROUP BY a.id_alumno, v.id_vacante
            ORDER BY 
                v.fecha_publicacion DESC
            LIMIT 5
        ";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
