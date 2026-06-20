<?php
class AlumnoModel {
    private $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerPerfil($id_alumno) {
        $sql = "SELECT nombre, apellidos, email, matricula, carrera, semestre, cv_url, perfil_linkedin FROM Alumnos WHERE id_alumno = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_alumno, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarPerfil($id_alumno, $carrera, $cv_url, $perfil_linkedin) {
        $sql = "UPDATE alumnos SET carrera = :carrera, cv_url = :cv, perfil_linkedin = :linkedin WHERE id_alumno = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':carrera', $carrera, PDO::PARAM_STR);
        $stmt->bindParam(':cv', $cv_url, PDO::PARAM_STR);
        $stmt->bindParam(':linkedin', $perfil_linkedin, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_alumno, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function registrarAlumno($nombre, $apellidos, $email, $password_plana, $matricula, $semestre = 7) {
        $password_hasheada = password_hash($password_plana, PASSWORD_DEFAULT);
        $sql = "INSERT INTO Alumnos (nombre, apellidos, email, password, matricula, semestre) 
                VALUES (:nombre, :apellidos, :email, :password, :matricula, :semestre)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre); 
        $stmt->bindParam(':apellidos', $apellidos); 
        $stmt->bindParam(':email', $email); 
        $stmt->bindParam(':password', $password_hasheada); 
        $stmt->bindParam(':matricula', $matricula);
        $stmt->bindParam(':semestre', $semestre, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
