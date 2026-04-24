<?php

require_once __DIR__ . "/../config/database.php";

class Medico {

    private $conn;

    public function __construct() {

        $database = new Database();

        $this->conn = $database->conectar();
    }


    public function obtenerTodos() {

        
        $sql = "SELECT m.*, e.nombre_especialidad
                FROM medicos m

            
                INNER JOIN especialidades e ON m.id_especialidad = e.id_especialidad

                
                ORDER BY m.id_medico DESC";

        
        $stmt = $this->conn->query($sql);

       
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    
    public function crear($data) {

        
        $sql = "INSERT INTO medicos (
                    primer_nombre,
                    segundo_nombre,
                    primer_apellido,
                    segundo_apellido,
                    correo_electronico,
                    numero_de_celular,
                    id_especialidad         
                )
                VALUES (
                    :primer_nombre,
                    :segundo_nombre,        
                    :primer_apellido,
                    :segundo_apellido,      
                    :correo_electronico,
                    :numero_de_celular,
                    :id_especialidad        
                )";

        
        $stmt = $this->conn->prepare($sql);

        
        return $stmt->execute($data);
    }


    
    public function actualizar($data) {

        
        $sql = "UPDATE medicos SET
                    primer_nombre      = :primer_nombre,
                    segundo_nombre     = :segundo_nombre,
                    primer_apellido    = :primer_apellido,
                    segundo_apellido   = :segundo_apellido,
                    correo_electronico = :correo_electronico,
                    numero_de_celular  = :numero_de_celular,
                    id_especialidad    = :id_especialidad

                
                WHERE id_medico = :id_medico";

        
        $stmt = $this->conn->prepare($sql);

        
        return $stmt->execute($data);
    }


    public function eliminar($id) {

        
        $sql = "DELETE FROM medicos WHERE id_medico = :id";

       
        return $this->conn->prepare($sql)->execute([':id' => $id]);
    }

}


?>