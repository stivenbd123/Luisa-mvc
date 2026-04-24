<?php

session_start();

require_once "../models/Paciente.php";

$pacienteModel = new Paciente();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';


if ($accion == 'crear') {

    $data = [
        
        ':primer_nombre'      => $_POST['primer_nombre'],

        ':segundo_nombre'     => $_POST['segundo_nombre'],

        ':primer_apellido'    => $_POST['primer_apellido'],

        ':segundo_apellido'   => $_POST['segundo_apellido'],

        ':numero_de_cedula'   => $_POST['numero_de_cedula'],

        ':correo_electronico' => $_POST['correo_electronico'],

        ':direccion'          => $_POST['direccion'],

        ':numero_de_celular'  => $_POST['numero_de_celular']
    ];


    if ($pacienteModel->crearPaciente($data)) {

        $_SESSION['success_paciente'] = "Paciente registrado correctamente.";

    } else {

        $_SESSION['error_paciente'] = "Error al registrar paciente.";

    }


    header("Location: ../views/html/Pacientes/CRUD_Pacientes.php");

    
    exit();



} elseif ($accion == 'editar') {

   
    $data = [
        
        ':id_paciente'        => $_POST['id_paciente'],

        
        ':primer_nombre'      => $_POST['primer_nombre'],
        ':segundo_nombre'     => $_POST['segundo_nombre'],
        ':primer_apellido'    => $_POST['primer_apellido'],
        ':segundo_apellido'   => $_POST['segundo_apellido'],
        ':numero_de_cedula'   => $_POST['numero_de_cedula'],
        ':correo_electronico' => $_POST['correo_electronico'],
        ':direccion'          => $_POST['direccion'],
        ':numero_de_celular'  => $_POST['numero_de_celular']
    ];

    
    if ($pacienteModel->actualizarPaciente($data)) {

        
        $_SESSION['success_paciente'] = "Paciente actualizado correctamente.";

    }
   
    header("Location: ../views/html/Pacientes/CRUD_Pacientes.php");
    exit();



} elseif ($accion == 'eliminar') {

    
    $id = $_GET['id'];

    
    if ($pacienteModel->eliminarPaciente($id)) {

       
        $_SESSION['success_paciente'] = "Paciente eliminado.";

    }
    
    header("Location: ../views/html/Pacientes/CRUD_Pacientes.php");
    exit();

}

?>