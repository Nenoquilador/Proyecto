<?php

class CompanyModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function getEstadoValidacion($id_empresa) {
        $sql = "SELECT estado_validacion FROM empresas WHERE id_empresa = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => $id_empresa]);
        return $stmt->fetchColumn();
    }

    public function getEstadisticas($id_empresa) {
        $stats = [
            'active_vacancies' => 0,
            'total_applications' => 0,
            'closed_vacancies' => 0,
            'meses_data' => []
        ];

        // Vacantes Activas
        $sql_vacantes = "SELECT COUNT(*) FROM vacantes WHERE id_empresa = :id AND estado = 'abierta'";
        $stmt_vacantes = $this->conexion->prepare($sql_vacantes); 
        $stmt_vacantes->execute([':id' => $id_empresa]);
        $stats['active_vacancies'] = $stmt_vacantes->fetchColumn();

        // Postulaciones
        $sql_apps = "SELECT COUNT(p.id_postulacion) FROM postulaciones p JOIN vacantes v ON p.id_vacante = v.id_vacante WHERE v.id_empresa = :id";
        $stmt_apps = $this->conexion->prepare($sql_apps); 
        $stmt_apps->execute([':id' => $id_empresa]);
        $stats['total_applications'] = $stmt_apps->fetchColumn();
        
        // Cerradas
        $sql_cerradas = "SELECT COUNT(*) FROM vacantes WHERE id_empresa = :id AND estado = 'cerrada'";
        $stmt_cerradas = $this->conexion->prepare($sql_cerradas);
        $stmt_cerradas->execute([':id' => $id_empresa]);
        $stats['closed_vacancies'] = $stmt_cerradas->fetchColumn();

        // Datos para Gráfica: Postulaciones por Mes
        $sql_meses = "SELECT MONTH(p.fecha_postulacion) as mes, COUNT(*) as total 
                      FROM postulaciones p 
                      JOIN vacantes v ON p.id_vacante = v.id_vacante 
                      WHERE v.id_empresa = :id 
                      GROUP BY mes 
                      ORDER BY MAX(p.fecha_postulacion) DESC LIMIT 6";
        $stmt_meses = $this->conexion->prepare($sql_meses);
        $stmt_meses->execute([':id' => $id_empresa]);
        $stats['meses_data'] = $stmt_meses->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    public function processUploadSSPP($id_empresa, $ruta_relativa) {
        $sql_check = "SELECT id_solicitud FROM solicitudes_sspp WHERE id_empresa = :id";
        $stmt_check = $this->conexion->prepare($sql_check);
        $stmt_check->execute([':id' => $id_empresa]);
        $solicitud_existente = $stmt_check->fetchColumn();

        if ($solicitud_existente) {
            $sql_upd = "UPDATE solicitudes_sspp SET estado_tramite = 'Datos Recibidos', notas_admin = :ruta WHERE id_solicitud = :id_sol";
            $stmt_upd = $this->conexion->prepare($sql_upd);
            return $stmt_upd->execute([':ruta' => $ruta_relativa, ':id_sol' => $solicitud_existente]);
        } else {
            $sql_ins = "INSERT INTO solicitudes_sspp (id_empresa, estado_tramite, fecha_inicio, notas_admin) 
                        VALUES (:id_empresa, 'Datos Recibidos', CURDATE(), :ruta)";
            $stmt_ins = $this->conexion->prepare($sql_ins);
            return $stmt_ins->execute([':id_empresa' => $id_empresa, ':ruta' => $ruta_relativa]);
        }
    }

    public function getPerfil($id_empresa) {
        $sql = "SELECT id_empresa, nombre_empresa, email_contacto, rfc, descripcion, sitio_web, logo_url, banner_url, carreras_afines, estado_validacion 
                FROM Empresas 
                WHERE id_empresa = :id_empresa";
        $stmt = $this->conexion->prepare($sql); 
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarEmpresa($nombre_empresa, $email_contacto, $password_hasheada, $descripcion, $sitio_web, $carreras_afines) {
        $sql = "INSERT INTO Empresas (nombre_empresa, email_contacto, password, descripcion, sitio_web, carreras_afines) 
                VALUES (:nombre_empresa, :email_contacto, :password, :descripcion, :sitio_web, :carreras_afines)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre_empresa', $nombre_empresa);
        $stmt->bindParam(':email_contacto', $email_contacto);
        $stmt->bindParam(':password', $password_hasheada);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':sitio_web', $sitio_web);
        $stmt->bindParam(':carreras_afines', $carreras_afines);
        return $stmt->execute();
    }

    public function updatePerfil($id_empresa, $nombre, $email, $rfc, $sitio_web, $descripcion, $carreras_afines, $ruta_logo = null, $ruta_banner = null) {
        $logo_query = $ruta_logo ? ", logo_url = :logo_url" : "";
        $banner_query = $ruta_banner ? ", banner_url = :banner_url" : "";

        $sql = "UPDATE Empresas SET 
                    nombre_empresa = :nombre, 
                    email_contacto = :email, 
                    rfc = :rfc, 
                    sitio_web = :sitio_web, 
                    descripcion = :descripcion,
                    carreras_afines = :carreras_afines
                    $logo_query
                    $banner_query
                WHERE id_empresa = :id_empresa";
        
        $stmt = $this->conexion->prepare($sql);
        
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':rfc', $rfc, PDO::PARAM_STR);
        $stmt->bindParam(':sitio_web', $sitio_web, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':carreras_afines', $carreras_afines, PDO::PARAM_STR);
        $stmt->bindParam(':id_empresa', $id_empresa, PDO::PARAM_INT);
        if ($ruta_logo) {
            $stmt->bindParam(':logo_url', $ruta_logo, PDO::PARAM_STR);
        }
        if ($ruta_banner) {
            $stmt->bindParam(':banner_url', $ruta_banner, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }
}
