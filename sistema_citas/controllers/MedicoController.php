<?php

session_start();

require_once "../models/Medico.php";

require_once "../models/Especialidad.php";

$medicoM = new Medico();

$especialidadM = new Especialidad();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';


if ($accion == 'crear_especialidad') {

    $especialidadM->crear($_POST['nombre_especialidad']);

} elseif ($accion == 'editar_especialidad') {

    $especialidadM->actualizar($_POST['id_especialidad'], $_POST['nombre_especialidad']);

} elseif ($accion == 'eliminar_especialidad') {

    
    $especialidadM->eliminar($_GET['id']);

}


if ($accion == 'crear_medico' || $accion == 'editar_medico') {

    $data = [
       
        ':primer_nombre'      => $_POST['primer_nombre'],

        ':segundo_nombre'     => $_POST['segundo_nombre'] ?? '',

        ':primer_apellido'    => $_POST['primer_apellido'],

        ':segundo_apellido'   => $_POST['segundo_apellido'] ?? '',

        ':correo_electronico' => $_POST['correo_electronico'],

        ':numero_de_celular'  => $_POST['numero_de_celular'],

        ':id_especialidad'    => $_POST['id_especialidad']
    ];

   
    if ($accion == 'crear_medico') {

        $medicoM->crear($data);

    } else {

        $data[':id_medico'] = $_POST['id_medico'];

        $medicoM->actualizar($data);
    }

} elseif ($accion == 'eliminar_medico') {

    $medicoM->eliminar($_GET['id']);

}

header("Location: ../views/html/medicos/CRUD_Medicos_y_Especialidades.php");

exit();

?>