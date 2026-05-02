<?php
class Database {
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Ruta absoluta al archivo sqlite que vamos a crear
            $db_file = __DIR__ . '/../medisys.sqlite';
            $this->conn = new PDO("sqlite:" . $db_file);
            // Configurar PDO para que maneje los errores
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Que devuelva los datos como arrays por defecto
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>