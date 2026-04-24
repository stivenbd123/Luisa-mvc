<?php

require_once __DIR__ . "/../config/database.php";


class Especialidad {

    private $conn;


    public function __construct() {

        $database = new Database();

        $this->conn = $database->conectar();
    }


    public function obtenerTodas() {

        $sql = "SELECT * FROM especialidades ORDER BY nombre_especialidad ASC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function crear($nombre) {

        $sql = "INSERT INTO especialidades (nombre_especialidad) VALUES (:nombre)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([':nombre' => $nombre]);
    }


    public function actualizar($id, $nombre) {


        $sql = "UPDATE especialidades SET nombre_especialidad = :nombre WHERE id_especialidad = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([':nombre' => $nombre, ':id' => $id]);
    }


   
    public function eliminar($id) {

        $sql = "DELETE FROM especialidades WHERE id_especialidad = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

}

?>