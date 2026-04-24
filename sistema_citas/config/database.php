<?php

class Database {

    private $host = "localhost";
    private $db = "sistema_citas";
    private $user = "root";
    private $pass = "";

    public function conectar() {

        
        try {

            $conexion = new PDO(

                "mysql:host=$this->host;dbname=$this->db",

                $this->user,

                $this->pass
            );

            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conexion;

    
        } catch(PDOException $e) {

           
            die("Error de conexión: " . $e->getMessage());

        }

    }
    

}


?>
