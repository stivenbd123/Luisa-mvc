<?php
session_start();

require_once __DIR__ . "/../config/database.php";

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

$db   = new Database();
$conn = $db->conectar();

$accion = $_GET['accion'] ?? '';

if ($accion === 'buscar') {

    $paciente    = trim($_GET['paciente']     ?? '');
    $cedula      = trim($_GET['cedula']       ?? '');
    $fechaInicio = trim($_GET['fecha_inicio'] ?? '');
    $fechaFin    = trim($_GET['fecha_fin']    ?? '');

    $where  = ['1=1'];
    $params = [];

    if ($paciente) {
        $where[]        = "CONCAT(p.primer_nombre,' ',p.primer_apellido) LIKE :pac";
        $params[':pac'] = '%' . $paciente . '%';
    }

    if ($cedula) {
        $where[]        = "p.numero_de_cedula LIKE :ced";
        $params[':ced'] = '%' . $cedula . '%';
    }

    if ($fechaInicio) {
        $where[]       = "c.fecha >= :fi";
        $params[':fi'] = $fechaInicio;
    }

    if ($fechaFin) {
        $where[]       = "c.fecha <= :ff";
        $params[':ff'] = $fechaFin;
    }

    $sql = "SELECT
                c.id_cita,
                CONCAT(p.primer_nombre,' ',p.primer_apellido) AS paciente,
                p.numero_de_cedula,
                e.nombre_especialidad,
                CONCAT(m.primer_nombre,' ',m.primer_apellido) AS medico,
                co.nombre  AS consultorio,
                c.fecha,
                c.hora,
                c.estado
            FROM citas c
            INNER JOIN pacientes p      ON c.id_paciente    = p.id_paciente
            INNER JOIN medicos m        ON c.id_medico      = m.id_medico
            INNER JOIN especialidades e ON m.id_especialidad = e.id_especialidad
            INNER JOIN consultorios co  ON c.id_consultorio  = co.id_consultorio
            WHERE " . implode(' AND ', $where) . "
            ORDER BY c.fecha DESC, c.hora DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($accion === 'detalle') {

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
                CONCAT(m.primer_nombre,' ',m.primer_apellido) AS medico,
                m.correo_electronico  AS correo_medico,
                co.nombre    AS consultorio,
                co.direccion AS dir_consultorio,
                c.fecha, c.hora, c.estado,
                c.created_at
            FROM citas c
            INNER JOIN pacientes p      ON c.id_paciente    = p.id_paciente
            INNER JOIN medicos m        ON c.id_medico      = m.id_medico
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
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida']);
?>