<?php

session_start();

require_once "../models/Cita.php";

require_once "../models/Medico.php";

require_once "../models/Especialidad.php";

require_once "../config/database.php";

require_once "../config/mail.php";


$citaM = new Cita();

$medicoM = new Medico();

$especialidadM = new Especialidad();


$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';


if ($accion == 'crear_cita') {

    $data = [
       
        ':id_paciente'    => $_POST['id_paciente'],

        ':id_medico'      => $_POST['id_medico'],

        ':id_consultorio' => $_POST['id_consultorio'],

        ':fecha'          => $_POST['fecha'],

        ':hora'           => $_POST['hora'],
    ];


    $id_cita = $citaM->crear($data);

    $cita = $citaM->obtenerDetalleCita($id_cita);

    
    if (!$cita) {
       
        die("Error: no se pudo obtener la cita");
    }

   
    $mensaje = "
Nueva Cita Médica

Paciente: {$cita['paciente']}
Médico: {$cita['medico']}
Especialidad: {$cita['nombre_especialidad']}
Fecha: {$cita['fecha']}
Hora: {$cita['hora']}
Consultorio: {$cita['consultorio']}
";

  
    enviarCorreo($cita['correo_paciente'], $cita['paciente'], "Cita Médica Agendada", $mensaje);

    enviarCorreo($cita['correo_medico'], $cita['medico'], "Nueva Cita Asignada", $mensaje);

    header("Location: ../views/html/citas/citas.php");

    exit;


} elseif ($accion == 'cambiar_estado') {

    $citaM->actualizarEstado($_GET['id'], $_GET['estado']);

    header("Location: ../views/html/citas/citas.php");

    exit;


} elseif ($accion == 'eliminar_cita') {

    $citaM->eliminar($_GET['id']);

    header("Location: ../views/html/citas/citas.php");

    exit;
}



header("Location: ../views/html/citas/citas.php");

exit;

?>