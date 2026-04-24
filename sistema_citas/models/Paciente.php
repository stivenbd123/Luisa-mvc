<?php

require_once __DIR__ . "/../config/database.php";

class Paciente {

    private $conn;


    public function __construct() {

        $database = new Database();

        $this->conn = $database->conectar();
    }


    public function obtenerPacientes() {

        $sql = "SELECT * FROM pacientes ORDER BY id_paciente DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function crearPaciente($data) {

        $sql = "INSERT INTO pacientes (
                    primer_nombre,
                    segundo_nombre,      
                    primer_apellido,
                    segundo_apellido,     
                    numero_de_cedula,     
                    direccion,           
                    numero_de_celular,
                    correo_electronico   
                )
                VALUES (
                    :primer_nombre,
                    :segundo_nombre,
                    :primer_apellido,
                    :segundo_apellido,
                    :numero_de_cedula,
                    :direccion,
                    :numero_de_celular,
                    :correo_electronico
                )";

       
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($data);
    }


    public function actualizarPaciente($data) {

        $sql = "UPDATE pacientes SET
                    primer_nombre      = :primer_nombre,
                    segundo_nombre     = :segundo_nombre,
                    primer_apellido    = :primer_apellido,
                    segundo_apellido   = :segundo_apellido,
                    numero_de_cedula   = :numero_de_cedula,
                    direccion          = :direccion,
                    numero_de_celular  = :numero_de_celular,
                    correo_electronico = :correo_electronico

                
                WHERE id_paciente = :id_paciente";

      
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($data);
    }



    public function eliminarPaciente($id) {

      
        $sql = "DELETE FROM pacientes WHERE id_paciente = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id);

       
        return $stmt->execute();
    }

}

?>