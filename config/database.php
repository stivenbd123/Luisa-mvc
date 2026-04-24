<?php

// Se declara la clase "Database".
// Una clase es un molde/plantilla que agrupa datos y funciones relacionadas.
class Database {

    private $host = "localhost";

    private $db = "clinica";

    private $user = "root";

    private $pass = "";

    public function conectar() {

        
        try {

           
            $conexion = new PDO(

                $this->user,

                $this->pass
            );

            
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conexion;

       
        } catch(PDOException $e) {

            
            die("Error de conexión: " . $e->getMessage());

        }

    }
    // Fin del método conectar().

}
// Fin de la clase Database.

?>
<?php
// ============================================================
// MODO DE USO (no va en este archivo, es solo referencia):
//
// require_once 'config/database.php'; // Carga este archivo
// $db = new Database();               // Crea una instancia de la clase
// $pdo = $db->conectar();             // Obtiene la conexión PDO activa
// $stmt = $pdo->query("SELECT ...");  // Ya puede hacer consultas SQL
// ============================================================
?>