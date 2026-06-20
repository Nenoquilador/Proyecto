<?php

class EmpresaModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function getEstadisticas() {
        return [
            'count_alumnos' => $this->conexion->query("SELECT COUNT(*) FROM alumnos")->fetchColumn(),
            'count_vacantes' => $this->conexion->query("SELECT COUNT(*) FROM vacantes WHERE estado = 'abierta'")->fetchColumn(),
            'count_pendientes' => $this->conexion->query("SELECT COUNT(*) FROM empresas WHERE estado_validacion = 'pendiente'")->fetchColumn(),
            'count_sspp' => $this->conexion->query("SELECT COUNT(*) FROM solicitudes_sspp WHERE estado_tramite != 'Aprobado Catálogo'")->fetchColumn(),
            'count_emp_total' => $this->conexion->query("SELECT COUNT(*) FROM empresas")->fetchColumn(),
            'count_emp_aprobadas' => $this->conexion->query("SELECT COUNT(*) FROM empresas WHERE estado_validacion = 'aprobada'")->fetchColumn(),
            'count_emp_rechazadas' => $this->conexion->query("SELECT COUNT(*) FROM empresas WHERE estado_validacion = 'rechazada'")->fetchColumn(),
            'count_vacantes_cerradas' => $this->conexion->query("SELECT COUNT(*) FROM vacantes WHERE estado = 'cerrada'")->fetchColumn(),
            'count_catalogo_sspp' => $this->conexion->query("SELECT COUNT(*) FROM empresas WHERE es_catalogo_sspp = 1")->fetchColumn()
        ];
    }

    public function getDatosGraficoVacantes() {
        return $this->conexion->query("SELECT estado, COUNT(*) as total FROM vacantes GROUP BY estado")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDatosGraficoCrecimientoEmpresas() {
        return $this->conexion->query("
            SELECT DATE_FORMAT(fecha_registro, '%Y-%m') as mes, COUNT(*) as total 
            FROM empresas 
            WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY mes
            ORDER BY mes ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendientes() {
        return $this->conexion->query("SELECT id_empresa, nombre_empresa, email_contacto, fecha_registro FROM empresas WHERE estado_validacion = 'pendiente' ORDER BY fecha_registro ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTramitesEnProceso() {
        return $this->conexion->query("SELECT s.id_solicitud, e.nombre_empresa, s.estado_tramite, s.fecha_inicio FROM solicitudes_sspp s JOIN empresas e ON s.id_empresa = e.id_empresa WHERE s.estado_tramite != 'Aprobado Catálogo' ORDER BY s.fecha_inicio DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActivas() {
        return $this->conexion->query("SELECT id_empresa, nombre_empresa, email_contacto, es_catalogo_sspp, vigencia_sspp FROM empresas WHERE estado_validacion = 'aprobada' ORDER BY nombre_empresa ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAlertasRenovacion() {
        return $this->conexion->query("SELECT id_empresa, nombre_empresa, email_contacto, vigencia_sspp FROM empresas WHERE estado_validacion = 'aprobada' AND es_catalogo_sspp = 1 AND vigencia_sspp IS NOT NULL AND vigencia_sspp BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY) ORDER BY vigencia_sspp ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countEmpresas($search = '', $filtro_estado = '') {
        $sql = "SELECT COUNT(*) FROM empresas WHERE 1=1";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (nombre_empresa LIKE :s OR email_contacto LIKE :s)";
            $params[':s'] = "%$search%";
        }
        if (!empty($filtro_estado)) {
            $sql .= " AND estado_validacion = :e";
            $params[':e'] = $filtro_estado;
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function getEmpresas($search = '', $filtro_estado = '', $limit = 15, $offset = 0) {
        $sql = "SELECT id_empresa, nombre_empresa, email_contacto, estado_validacion, es_catalogo_sspp, vigencia_sspp, fecha_registro, carreras_afines FROM empresas WHERE 1=1";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (nombre_empresa LIKE :s OR email_contacto LIKE :s)";
            $params[':s'] = "%$search%";
        }
        if (!empty($filtro_estado)) {
            $sql .= " AND estado_validacion = :e";
            $params[':e'] = $filtro_estado;
        }
        $sql .= " ORDER BY fecha_registro DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmpresaById($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM empresas WHERE id_empresa = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateNotas($id, $notas, $carreras) {
        $stmt = $this->conexion->prepare("UPDATE empresas SET notas_internas = :notas, carreras_afines = :carreras WHERE id_empresa = :id");
        return $stmt->execute([':notas' => $notas, ':carreras' => $carreras, ':id' => $id]);
    }

    public function updateEstado($id, $estado) {
        $stmt = $this->conexion->prepare("UPDATE empresas SET estado_validacion = :estado WHERE id_empresa = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public function getUltimoTramite($id_empresa) {
        $stmt = $this->conexion->prepare("SELECT id_solicitud, estado_tramite FROM solicitudes_sspp WHERE id_empresa = :id ORDER BY id_solicitud DESC LIMIT 1");
        $stmt->execute([':id' => $id_empresa]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function iniciarTramite($id_empresa, $ruta_relativa) {
        $sql = "INSERT INTO solicitudes_sspp (id_empresa, estado_tramite, fecha_inicio, archivo_solicitud_dir) VALUES (:id, 'Solicitud Inicial', CURDATE(), :ruta)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':id' => $id_empresa, ':ruta' => $ruta_relativa]);
    }

    public function getAllEmpresasForExport() {
        return $this->conexion->query("SELECT id_empresa, nombre_empresa, rfc, email_contacto, sitio_web, estado_validacion, es_catalogo_sspp, vigencia_sspp, fecha_registro, carreras_afines FROM empresas ORDER BY id_empresa ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countTramites($search = '', $filtro_estado = '') {
        $sql = "SELECT COUNT(*) FROM solicitudes_sspp s JOIN empresas e ON s.id_empresa = e.id_empresa WHERE 1=1";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (e.nombre_empresa LIKE :s OR e.email_contacto LIKE :s)";
            $params[':s'] = "%$search%";
        }
        if (!empty($filtro_estado)) {
            $sql .= " AND s.estado_tramite = :e";
            $params[':e'] = $filtro_estado;
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function getTramites($search = '', $filtro_estado = '', $limit = 15, $offset = 0) {
        $sql = "SELECT s.id_solicitud, s.estado_tramite, s.fecha_inicio, s.fecha_validacion, 
                       e.nombre_empresa, e.email_contacto, e.id_empresa 
                FROM solicitudes_sspp s 
                JOIN empresas e ON s.id_empresa = e.id_empresa 
                WHERE 1=1";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (e.nombre_empresa LIKE :s OR e.email_contacto LIKE :s)";
            $params[':s'] = "%$search%";
        }
        if (!empty($filtro_estado)) {
            $sql .= " AND s.estado_tramite = :e";
            $params[':e'] = $filtro_estado;
        }
        $sql .= " ORDER BY s.fecha_inicio DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTramiteById($id) {
        $stmt = $this->conexion->prepare("SELECT s.*, e.nombre_empresa, e.email_contacto, e.id_empresa FROM solicitudes_sspp s JOIN empresas e ON s.id_empresa = e.id_empresa WHERE s.id_solicitud = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateEstadoTramite($id_solicitud, $estado, $validar = false) {
        if ($validar) {
            $sql = "UPDATE solicitudes_sspp SET estado_tramite = :estado, fecha_validacion = CURDATE() WHERE id_solicitud = :id";
        } else {
            $sql = "UPDATE solicitudes_sspp SET estado_tramite = :estado WHERE id_solicitud = :id";
        }
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':estado' => $estado, ':id' => $id_solicitud]);
    }

    public function finalizarRegistroCatalogo($id_solicitud, $id_empresa, $ruta_relativa) {
        $this->conexion->beginTransaction();
        try {
            $stmt_sol = $this->conexion->prepare("UPDATE solicitudes_sspp SET estado_tramite = 'Aprobado Catálogo', archivo_catalogo_generado = :ruta, fecha_vencimiento = DATE_ADD(CURDATE(), INTERVAL 3 YEAR) WHERE id_solicitud = :id");
            $stmt_sol->execute([':ruta' => $ruta_relativa, ':id' => $id_solicitud]);
            
            $stmt_emp = $this->conexion->prepare("UPDATE empresas SET estado_validacion = 'aprobada', es_catalogo_sspp = 1, vigencia_sspp = DATE_ADD(CURDATE(), INTERVAL 3 YEAR) WHERE id_empresa = :id_emp");
            $stmt_emp->execute([':id_emp' => $id_empresa]);
            
            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }
}
