<?php

session_start();

require_once "../models/Cita.php";

require_once "../models/Paciente.php";

require_once "../config/database.php";

header('Content-Type: application/json');

$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';


$db = new Database();

$conn = $db->conectar();

switch ($accion) {

    case 'buscar':

        $q = trim($_GET['q'] ?? '');

    
        if ($q === '') {

            
            $sql = "SELECT
                        c.id_cita,

                        CONCAT(p.primer_nombre, ' ', p.primer_apellido)  AS paciente,

                        p.numero_de_cedula,
                        e.nombre_especialidad,

                        CONCAT(m.primer_nombre, ' ', m.primer_apellido)  AS medico,

                        co.nombre   AS consultorio,
                        c.fecha,
                        c.hora,
                        c.estado

                   
                    FROM citas c

                    
                    INNER JOIN pacientes p     ON c.id_paciente     = p.id_paciente
                    INNER JOIN medicos m       ON c.id_medico       = m.id_medico

                    INNER JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                    INNER JOIN consultorios co  ON c.id_consultorio  = co.id_consultorio

                    ORDER BY c.fecha DESC, c.hora DESC";

            
            $stmt = $conn->query($sql);

        } else {


            $sql = "SELECT
                        c.id_cita,
                        CONCAT(p.primer_nombre, ' ', p.primer_apellido)  AS paciente,
                        p.numero_de_cedula,
                        e.nombre_especialidad,
                        CONCAT(m.primer_nombre, ' ', m.primer_apellido)  AS medico,
                        co.nombre   AS consultorio,
                        c.fecha,
                        c.hora,
                        c.estado
                    FROM citas c
                    INNER JOIN pacientes p     ON c.id_paciente     = p.id_paciente
                    INNER JOIN medicos m       ON c.id_medico       = m.id_medico
                    INNER JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                    INNER JOIN consultorios co  ON c.id_consultorio  = co.id_consultorio

             
                    WHERE

                        CONCAT(p.primer_nombre, ' ', p.primer_apellido) LIKE :q

                        OR p.numero_de_cedula LIKE :q

                    ORDER BY c.fecha DESC, c.hora DESC";

          
            $stmt = $conn->prepare($sql);

            $like = '%' . $q . '%';

            $stmt->bindParam(':q', $like);

            $stmt->execute();
        }

        
        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['ok' => true, 'data' => $citas]);

        break;


    case 'detalle':

        $id = (int)($_GET['id'] ?? 0);

        
        if (!$id) {
            
            echo json_encode(['ok' => false, 'msg' => 'ID inválido']);

            exit;
        }

     
        $sql = "SELECT
                    c.id_cita,

                    p.primer_nombre, p.segundo_nombre,
                    p.primer_apellido, p.segundo_apellido,
                    p.numero_de_cedula,
                    p.numero_de_celular,

                    p.correo_electronico  AS correo_paciente,

                    e.nombre_especialidad,
                    CONCAT(m.primer_nombre, ' ', m.primer_apellido) AS medico,

                    m.correo_electronico  AS correo_medico,

                    co.nombre    AS consultorio,

                    co.direccion AS dir_consultorio,

                    c.fecha, c.hora, c.estado,

                    c.created_at

                FROM citas c
                INNER JOIN pacientes p     ON c.id_paciente     = p.id_paciente
                INNER JOIN medicos m       ON c.id_medico       = m.id_medico
                INNER JOIN especialidades e ON m.id_especialidad = e.id_especialidad
                INNER JOIN consultorios co  ON c.id_consultorio  = co.id_consultorio

                WHERE c.id_cita = :id";

        $stmt = $conn->prepare($sql);

        $stmt->execute([':id' => $id]);

        $cita = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$cita) {
            echo json_encode(['ok' => false, 'msg' => 'Cita no encontrada']);
           
            exit;
        }

    
        echo json_encode(['ok' => true, 'data' => $cita]);

        break;

    default:
        echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida']);
}

?>