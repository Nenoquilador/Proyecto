<?php
class TramitesSSAlumnosModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerTodosLosTramites() {
        try {
            $sql = "SELECT t.*, a.nombre, a.apellidos, a.matricula, a.carrera,
                           v.titulo as vacante_titulo, e.nombre_empresa as empresa_bd_nombre
                    FROM tramites_servicio_social t
                    JOIN alumnos a ON t.id_alumno = a.id_alumno
                    JOIN postulaciones p ON t.id_postulacion = p.id_postulacion
                    JOIN vacantes v ON p.id_vacante = v.id_vacante
                    JOIN empresas e ON v.id_empresa = e.id_empresa
                    ORDER BY t.fecha_solicitud DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error en DB: " . $e->getMessage());
        }
    }

    public function validarPagoYActivarEtapa1($id_tramite) {
        $sql = "UPDATE tramites_servicio_social 
                SET estado_tramite = 'pago_validado_escolares', 
                    fecha_pago_validado = NOW() 
                WHERE id_tramite = :id_tramite";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tramite', $id_tramite, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
