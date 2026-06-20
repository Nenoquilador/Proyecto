<?php
require_once __DIR__ . '/../models/AlumnoModel.php';

class CargaMasivaAlumnosController {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['id_admin']) || ($_SESSION['tipo_admin'] ?? '') !== 'alumnos') {
            header("Location: ../../login.php");
            exit();
        }

        $mensaje = "";
        $error = false;
        $resultados = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
            require_once __DIR__ . '/../../config/Security.php';
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!Security::verifyCsrfToken($csrf_token)) {
                $mensaje = "Error de seguridad (CSRF). Por favor, intenta de nuevo.";
                $error = true;
            } else {
                if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['csv_file']['tmp_name'];
                    
                    if (($handle = fopen($tmpName, 'r')) !== FALSE) {
                        // Skip header
                        $header = fgetcsv($handle, 1000, ",");
                        
                        $sql = "INSERT INTO alumnos (matricula, nombre, apellidos, email, carrera, semestre, password) 
                                VALUES (:matricula, :nombre, :apellidos, :email, :carrera, :semestre, :password)";
                        $stmt = $this->conexion->prepare($sql);
                        
                        $row_num = 2; // Assuming row 1 was header
                        $insertados = 0;
                        $omitidos = 0;
                        
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if (count($data) < 7) {
                                $resultados[] = "Fila $row_num: Omitida (Faltan columnas).";
                                $omitidos++;
                                $row_num++;
                                continue;
                            }
                            
                            $matricula = mb_convert_encoding(trim($data[0]), 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                            $nombre = mb_convert_encoding(trim($data[1]), 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                            $apellidos = mb_convert_encoding(trim($data[2]), 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                            $email = mb_convert_encoding(trim($data[3]), 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                            $carrera = mb_convert_encoding(trim($data[4]), 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                            $semestre = (int)trim($data[5]);
                            $password = mb_convert_encoding(trim($data[6]), 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                            
                            $password_hasheada = password_hash($password, PASSWORD_DEFAULT);
                            
                            try {
                                $stmt->execute([
                                    ':matricula' => $matricula,
                                    ':nombre' => $nombre,
                                    ':apellidos' => $apellidos,
                                    ':email' => $email,
                                    ':carrera' => $carrera,
                                    ':semestre' => $semestre,
                                    ':password' => $password_hasheada
                                ]);
                                $insertados++;
                            } catch (PDOException $e) {
                                if ($e->getCode() == 23000) {
                                    $resultados[] = "Fila $row_num ($email): Omitida (Correo o matrícula ya existen).";
                                } else {
                                    $resultados[] = "Fila $row_num ($email): Error (" . $e->getMessage() . ").";
                                }
                                $omitidos++;
                            }
                            $row_num++;
                        }
                        fclose($handle);
                        
                        $mensaje = "Carga masiva finalizada. Insertados: $insertados, Omitidos: $omitidos.";
                        $error = false;
                    } else {
                        $mensaje = "Error al abrir el archivo CSV.";
                        $error = true;
                    }
                } else {
                    $mensaje = "Error al subir el archivo.";
                    $error = true;
                }
            }
        }

        require_once __DIR__ . '/../views/alumnos/carga_masiva.php';
    }
}
?>
