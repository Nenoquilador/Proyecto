<?php

class AnalyticsModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
        $this->initTable();
    }

    private function initTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS vistas_vacantes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_vacante INT NOT NULL,
                id_alumno INT NULL,
                fecha DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        try {
            $this->conexion->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creando vistas_vacantes: " . $e->getMessage());
        }
    }

    public function registrarVista($id_vacante, $id_alumno = null) {
        $sql = "INSERT INTO vistas_vacantes (id_vacante, id_alumno) VALUES (:id_vacante, :id_alumno)";
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':id_vacante' => $id_vacante,
                ':id_alumno' => $id_alumno
            ]);
        } catch (PDOException $e) {
            error_log("Error registrando vista: " . $e->getMessage());
        }
    }

    public function getMetricasConversion($id_empresa) {
        $sql = "
            SELECT 
                v.id_vacante,
                v.titulo,
                (SELECT COUNT(*) FROM vistas_vacantes vv WHERE vv.id_vacante = v.id_vacante) AS total_vistas,
                (SELECT COUNT(*) FROM postulaciones p WHERE p.id_vacante = v.id_vacante) AS total_postulaciones
            FROM vacantes v
            WHERE v.id_empresa = :id_empresa
        ";
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_empresa' => $id_empresa]);
            $metricas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcular tasa de conversión
            foreach ($metricas as &$metrica) {
                $vistas = (int)$metrica['total_vistas'];
                $postulaciones = (int)$metrica['total_postulaciones'];
                $tasa = ($vistas > 0) ? round(($postulaciones / $vistas) * 100, 2) : 0;
                $metrica['tasa_conversion'] = $tasa;
            }
            return $metricas;
        } catch (PDOException $e) {
            error_log("Error obteniendo metricas: " . $e->getMessage());
            return [];
        }
    }
}
?>
