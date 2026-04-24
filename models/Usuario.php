<?php

require_once __DIR__ . "/../config/database.php";

class Usuario {

    private $conn;

    public function __construct() {

        $database = new Database();

        $this->conn = $database->conectar();
    }

    public function correoExiste($correo) {

        $sql = "SELECT id_usuario FROM usuarios WHERE correo_electronico = :correo LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":correo", $correo);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


  
    public function crearUsuario($data) {


        $sql = "INSERT INTO usuarios (
                    primer_nombre,
                    segundo_nombre,       
                    primer_apellido,
                    segundo_apellido,    
                    numero_de_cedula,
                    correo_electronico,  
                    direccion,            
                    numero_de_celular,
                    contraseña,          
                    rol                 
                )
                VALUES (
                    :primer_nombre,
                    :segundo_nombre,
                    :primer_apellido,
                    :segundo_apellido,
                    :numero_de_cedula,
                    :correo_electronico,
                    :direccion,
                    :numero_de_celular,
                    :contrasena,        
                    :rol                 
                )";

        
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($data);
    }


   
    public function login($correo) {

  
        $sql = "SELECT * FROM usuarios WHERE correo_electronico = :correo LIMIT 1";

        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":correo", $correo);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function obtenerUsuarios() {

        $sql = "SELECT * FROM usuarios ORDER BY id_usuario DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function obtenerPorId($id) {

        $sql = "SELECT * FROM usuarios WHERE id_usuario = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


   
    public function actualizarUsuario($data, $nueva_contrasena = null) {

       
        $sql = "UPDATE usuarios SET
                    primer_nombre      = :primer_nombre,
                    segundo_nombre     = :segundo_nombre,
                    primer_apellido    = :primer_apellido,
                    segundo_apellido   = :segundo_apellido,
                    numero_de_cedula   = :numero_de_cedula,
                    correo_electronico = :correo_electronico,
                    direccion          = :direccion,
                    numero_de_celular  = :numero_de_celular,
                    rol                = :rol";


        
        if ($nueva_contrasena !== null) {

            $sql .= ", contraseña = :contrasena";
        }
       

       
        $sql .= " WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($sql);


        $stmt->bindParam(':primer_nombre',      $data[':primer_nombre']);
        $stmt->bindParam(':segundo_nombre',     $data[':segundo_nombre']);
        $stmt->bindParam(':primer_apellido',    $data[':primer_apellido']);
        $stmt->bindParam(':segundo_apellido',   $data[':segundo_apellido']);
        $stmt->bindParam(':numero_de_cedula',   $data[':numero_de_cedula']);
        $stmt->bindParam(':correo_electronico', $data[':correo_electronico']);
        $stmt->bindParam(':direccion',          $data[':direccion']);
        $stmt->bindParam(':numero_de_celular',  $data[':numero_de_celular']);
        $stmt->bindParam(':rol',                $data[':rol']);

    
        $stmt->bindParam(':id_usuario',         $data[':id_usuario']);


        if ($nueva_contrasena !== null) {

            $stmt->bindParam(':contrasena', $nueva_contrasena);
        }

        
        return $stmt->execute();
    }


  
    public function eliminarUsuario($id) {

        $sql = "DELETE FROM usuarios WHERE id_usuario = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

}


?>