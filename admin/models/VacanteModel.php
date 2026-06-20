<?php

class VacanteModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function updateEstado($id, $estado) {
        $stmt = $this->conexion->prepare("UPDATE vacantes SET estado = :estado WHERE id_vacante = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public function countVacantes($search = '') {
        $sql = "SELECT COUNT(*) FROM vacantes v JOIN empresas e ON v.id_empresa = e.id_empresa";
        if ($search) {
            $sql .= " WHERE v.titulo LIKE :s OR e.nombre_empresa LIKE :s";
        }
        $stmt = $this->conexion->prepare($sql);
        if ($search) {
            $stmt->execute([':s' => "%$search%"]); 
        } else {
            $stmt->execute();
        }
        return $stmt->fetchColumn();
    }

    public function getVacantes($search = '', $limit = 15, $offset = 0) {
        $sql = "SELECT v.id_vacante, v.titulo, e.nombre_empresa, v.estado, v.fecha_publicacion 
                FROM vacantes v JOIN empresas e ON v.id_empresa = e.id_empresa";
        if ($search) {
            $sql .= " WHERE v.titulo LIKE :s OR e.nombre_empresa LIKE :s";
        }
        $sql .= " ORDER BY v.fecha_publicacion DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $this->conexion->prepare($sql);
        if ($search) {
            $stmt->execute([':s' => "%$search%"]); 
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetalleVacante($id_vacante) {
        $sql = "SELECT
            v.id_vacante, v.titulo, v.descripcion, v.ubicacion, v.modalidad, v.tipo_contrato, v.salario_ofrecido, v.fecha_publicacion, v.estado AS estado_vacante, v.carrera_afin,
            e.id_empresa, e.nombre_empresa, e.email_contacto, e.rfc, e.descripcion AS descripcion_empresa, e.sitio_web, e.estado_validacion AS estado_empresa
        FROM vacantes v
        JOIN empresas e ON v.id_empresa = e.id_empresa
        WHERE v.id_vacante = :id_vacante";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_vacante' => $id_vacante]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
