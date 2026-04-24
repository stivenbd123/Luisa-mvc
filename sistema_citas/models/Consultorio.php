<?php

require_once __DIR__ . "/../config/database.php";

class Consultorio {

    private $conn;

    public function __construct() {

        $database = new Database();

        $this->conn = $database->conectar();
    }


    public function obtenerTodos() {

        $sql = "SELECT * FROM consultorios ORDER BY id_consultorio DESC";

        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function crear($data) {

        $sql = "INSERT INTO consultorios (nombre,    direccion,    telefono)
                VALUES                   (:nombre,  :direccion,  :telefono)";
     
        $stmt = $this->conn->prepare($sql);

      
        return $stmt->execute($data);
    }


    public function actualizar($data) {

       
        $sql = "UPDATE consultorios SET
                    nombre    = :nombre,
                    direccion = :direccion,
                    telefono  = :telefono

                
                
                WHERE id_consultorio = :id_consultorio";

       
        $stmt = $this->conn->prepare($sql);

     
        return $stmt->execute($data);
    }


    public function eliminar($id) {

        $sql = "DELETE FROM consultorios WHERE id_consultorio = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

}

?>