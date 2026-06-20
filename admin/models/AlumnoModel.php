<?php

class AlumnoModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function getCarreras() {
        try {
            return $this->conexion->query("SELECT DISTINCT carrera FROM alumnos WHERE carrera != ''")->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAlumnos($search_term = '', $filtro_carrera = '') {
        $sql = "SELECT id_alumno, nombre, apellidos, matricula, carrera, email, cv_url FROM alumnos WHERE 1=1";
        $params = [];
        
        if (!empty($search_term)) {
            $sql .= " AND (nombre LIKE :search OR apellidos LIKE :search OR matricula LIKE :search)";
            $params[':search'] = "%$search_term%";
        }
        
        if (!empty($filtro_carrera)) {
            $sql .= " AND carrera = :carrera";
            $params[':carrera'] = $filtro_carrera;
        }
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAlumnos() {
        try {
            return $this->conexion->query("SELECT COUNT(*) FROM alumnos")->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countCvs() {
        try {
            return $this->conexion->query("SELECT COUNT(*) FROM alumnos WHERE cv_url IS NOT NULL AND cv_url != ''")->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function getAlumno($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM alumnos WHERE id_alumno = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSemestre($id, $semestre) {
        $stmt = $this->conexion->prepare("UPDATE alumnos SET semestre = :semestre WHERE id_alumno = :id");
        return $stmt->execute([':semestre' => $semestre, ':id' => $id]);
    }
}
