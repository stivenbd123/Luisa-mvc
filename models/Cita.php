<?php

require_once __DIR__ . "/../config/database.php";

class Cita {

    
    private $conn;


    public function __construct() {

        $database = new Database();

        $this->conn = $database->conectar();
    }


   
    public function obtenerTodas() {

        
        $sql = "SELECT
                    
                    c.id_cita,

                    CONCAT(p.primer_nombre, ' ', p.primer_apellido) AS paciente,

                    e.nombre_especialidad,

                    CONCAT(m.primer_nombre, ' ', m.primer_apellido) AS medico,

                    co.nombre AS consultorio,

                    c.fecha,
                    c.hora,

                    c.estado

                
                FROM citas c

                
                INNER JOIN pacientes p    ON c.id_paciente    = p.id_paciente
                INNER JOIN medicos m      ON c.id_medico      = m.id_medico

                INNER JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                INNER JOIN consultorios co  ON c.id_consultorio  = co.id_consultorio

                ORDER BY c.fecha DESC, c.hora DESC";

        
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    
    public function crear($data) {

        
        $sql = "INSERT INTO citas (id_paciente, id_medico, id_consultorio, fecha, hora, estado)
                VALUES             (:id_paciente, :id_medico, :id_consultorio, :fecha, :hora, 'Agendada')";
        
        $stmt = $this->conn->prepare($sql);

        
        $stmt->execute($data);

    
        return $this->conn->lastInsertId();
    }


   
    public function obtenerDetalleCita($id_cita) {

        
        $sql = "SELECT
                    c.id_cita,
                    c.fecha,
                    c.hora,

                    
                    CONCAT(p.primer_nombre, ' ', p.primer_apellido) AS paciente,

                    p.correo_electronico AS correo_paciente,

                    CONCAT(m.primer_nombre, ' ', m.primer_apellido) AS medico,

                    m.correo_electronico AS correo_medico,

                    e.nombre_especialidad,

                    co.nombre AS consultorio

                FROM citas c
                INNER JOIN pacientes p    ON c.id_paciente    = p.id_paciente
                INNER JOIN medicos m      ON c.id_medico      = m.id_medico
                INNER JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                INNER JOIN consultorios co  ON c.id_consultorio  = co.id_consultorio

                .
                WHERE c.id_cita = :id";

        
        $stmt = $this->conn->prepare($sql);

        
        $stmt->execute([':id' => $id_cita]);

       
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    
    public function actualizarEstado($id, $estado) {

        
        $sql = "UPDATE citas SET estado = :estado WHERE id_cita = :id";

        
        $stmt = $this->conn->prepare($sql);

       
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }


    
    public function eliminar($id) {

       
        $sql = "DELETE FROM citas WHERE id_cita = :id";

       
        return $this->conn->prepare($sql)->execute([':id' => $id]);
    }
}

?>