<?php
class ServicioSocialModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerTramitePorAlumnoYPostulacion($id_alumno, $id_postulacion) {
        $sql = "SELECT t.*, v.titulo as vacante_titulo, e.nombre_empresa as empresa_bd_nombre 
                FROM tramites_servicio_social t
                JOIN postulaciones p ON t.id_postulacion = p.id_postulacion
                JOIN vacantes v ON p.id_vacante = v.id_vacante
                JOIN empresas e ON v.id_empresa = e.id_empresa
                WHERE t.id_alumno = :id_alumno AND t.id_postulacion = :id_postulacion";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->bindParam(':id_postulacion', $id_postulacion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearTramiteInicial($id_alumno, $id_postulacion, $empresa_nombre, $dirigido_a, $cargo_dirigido) {
        $sql = "INSERT INTO tramites_servicio_social 
                (id_alumno, id_postulacion, estado_tramite, empresa_nombre, dirigido_a, cargo_dirigido) 
                VALUES (:id_alumno, :id_postulacion, 'solicitud_creditos', :empresa_nombre, :dirigido_a, :cargo_dirigido)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->bindParam(':id_postulacion', $id_postulacion, PDO::PARAM_INT);
        $stmt->bindParam(':empresa_nombre', $empresa_nombre, PDO::PARAM_STR);
        $stmt->bindParam(':dirigido_a', $dirigido_a, PDO::PARAM_STR);
        $stmt->bindParam(':cargo_dirigido', $cargo_dirigido, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function actualizarEtapa1($id_tramite, $datos, $archivo) {
        $sql = "UPDATE tramites_servicio_social SET 
                estado_tramite = 'etapa_1_documentos_entregados',
                avance_porcentaje = :avance,
                domicilio = :domicilio,
                telefonos = :telefonos,
                programa_ss = :programa,
                duracion_ss = :duracion,
                tareas_especificas = :tareas,
                apoyo_economico = :apoyo,
                archivo_carta_aceptacion = :archivo
                WHERE id_tramite = :id_tramite";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':avance', $datos['avance_porcentaje']);
        $stmt->bindParam(':domicilio', $datos['domicilio']);
        $stmt->bindParam(':telefonos', $datos['telefonos']);
        $stmt->bindParam(':programa', $datos['programa_ss']);
        $stmt->bindParam(':duracion', $datos['duracion_ss']);
        $stmt->bindParam(':tareas', $datos['tareas_especificas']);
        $stmt->bindParam(':apoyo', $datos['apoyo_economico']);
        $stmt->bindParam(':archivo', $archivo);
        $stmt->bindParam(':id_tramite', $id_tramite, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function actualizarEtapa2($id_tramite, $datos, $archivos) {
        $sql = "UPDATE tramites_servicio_social SET 
                estado_tramite = 'etapa_2_liberacion',
                evaluacion_empresa_amabilidad = :amabilidad,
                evaluacion_empresa_ambiente = :ambiente,
                plantel_tramite = :plantel,
                archivo_evaluacion_desempeno = :eval_desempeno,
                archivo_reporte_global = :reporte_global,
                archivo_carta_terminacion = :carta_terminacion
                WHERE id_tramite = :id_tramite";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':amabilidad', $datos['amabilidad'], PDO::PARAM_INT);
        $stmt->bindParam(':ambiente', $datos['ambiente'], PDO::PARAM_INT);
        $stmt->bindParam(':plantel', $datos['plantel']);
        $stmt->bindParam(':eval_desempeno', $archivos['eval_desempeno']);
        $stmt->bindParam(':reporte_global', $archivos['reporte_global']);
        $stmt->bindParam(':carta_terminacion', $archivos['carta_terminacion']);
        $stmt->bindParam(':id_tramite', $id_tramite, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerPostulacionAceptada($id_alumno, $id_postulacion) {
        // Asumiendo que 'en_proceso' es aceptada o tenemos un estado para ello.
        // Aquí permitimos continuar si está en la tabla de postulaciones y pertenece al alumno
        $sql = "SELECT p.*, v.titulo, e.nombre_empresa 
                FROM postulaciones p
                JOIN vacantes v ON p.id_vacante = v.id_vacante
                JOIN empresas e ON v.id_empresa = e.id_empresa
                WHERE p.id_alumno = :id_alumno AND p.id_postulacion = :id_postulacion";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->bindParam(':id_postulacion', $id_postulacion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
