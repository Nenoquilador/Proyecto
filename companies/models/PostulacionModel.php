<?php

class PostulacionModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function getTituloVacante($id_vacante, $id_empresa) {
        $sql = "SELECT titulo FROM Vacantes WHERE id_vacante = :id_vacante AND id_empresa = :id_empresa";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['titulo'] : null;
    }

    public function updateStatus($id_postulacion, $new_status, $id_empresa) {
        $sql = "UPDATE Postulaciones p
                INNER JOIN Vacantes v ON p.id_vacante = v.id_vacante
                SET p.estado_postulacion = :new_status 
                WHERE p.id_postulacion = :id_postulacion AND v.id_empresa = :id_empresa";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':new_status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':id_postulacion', $id_postulacion, PDO::PARAM_INT);
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateNotas($id_postulacion, $notas, $id_empresa) {
        $sql = "UPDATE Postulaciones p
                INNER JOIN Vacantes v ON p.id_vacante = v.id_vacante
                SET p.notas_empresa = :notas 
                WHERE p.id_postulacion = :id_postulacion AND v.id_empresa = :id_empresa";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':notas', $notas, PDO::PARAM_STR);
        $stmt->bindParam(':id_postulacion', $id_postulacion, PDO::PARAM_INT);
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getCarrerasByVacante($id_vacante) {
        $sql = "SELECT DISTINCT a.carrera 
                FROM Postulaciones p
                JOIN Alumnos a ON p.id_alumno = a.id_alumno
                WHERE p.id_vacante = :id_vacante AND a.carrera IS NOT NULL AND a.carrera != ''
                ORDER BY a.carrera";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPostulantesFiltrados($id_vacante, $filtros, $limit, $offset) {
        $sql = "SELECT 
                    p.id_postulacion,
                    p.fecha_postulacion,
                    p.estado_postulacion,
                    p.notas_empresa,
                    a.id_alumno,
                    a.nombre,
                    a.apellidos,
                    a.email,
                    a.matricula,
                    a.carrera,
                    a.cv_url
                FROM 
                    Postulaciones AS p
                JOIN 
                    Alumnos AS a ON p.id_alumno = a.id_alumno
                WHERE 
                    p.id_vacante = :id_vacante";
        
        $params = [':id_vacante' => $id_vacante];

        if (!empty($filtros['carrera'])) {
            $sql .= " AND a.carrera = :carrera";
            $params[':carrera'] = $filtros['carrera'];
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado_postulacion = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $sql .= " ORDER BY p.fecha_postulacion DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPostulantesFiltrados($id_vacante, $filtros) {
        $sql = "SELECT COUNT(*) as total
                FROM Postulaciones AS p
                JOIN Alumnos AS a ON p.id_alumno = a.id_alumno
                WHERE p.id_vacante = :id_vacante";
        
        $params = [':id_vacante' => $id_vacante];

        if (!empty($filtros['carrera'])) {
            $sql .= " AND a.carrera = :carrera";
            $params[':carrera'] = $filtros['carrera'];
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado_postulacion = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? (int)$res['total'] : 0;
    }

    public function getPostulantesByVacante($id_vacante, $search = '') {
        $sql = "SELECT 
                    p.id_postulacion,
                    p.fecha_postulacion,
                    p.estado_postulacion,
                    p.notas_empresa,
                    a.id_alumno,
                    a.nombre,
                    a.apellidos,
                    a.email,
                    a.matricula,
                    a.carrera,
                    a.cv_url
                FROM 
                    Postulaciones AS p
                JOIN 
                    Alumnos AS a ON p.id_alumno = a.id_alumno
                WHERE 
                    p.id_vacante = :id_vacante";

        if (!empty($search)) {
            $sql .= " AND (a.nombre LIKE :search OR a.apellidos LIKE :search OR a.carrera LIKE :search)";
        }

        $sql .= " ORDER BY p.fecha_postulacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_vacante', $id_vacante, PDO::PARAM_INT);
        
        if (!empty($search)) {
            $searchParam = "%$search%";
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
