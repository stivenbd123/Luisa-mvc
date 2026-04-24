<?php

session_start();

require_once "../models/Consultorio.php";

$consultorioModel = new Consultorio();


$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';


if ($accion == 'crear_consultorio') {

    
    $data = [
       
        ':nombre'    => $_POST['nombre'],

        ':direccion' => $_POST['direccion'],

        ':telefono'  => $_POST['telefono']
    ];

   
    $consultorioModel->crear($data);

}


elseif ($accion == 'editar_consultorio') {

    $data = [
        
        ':id_consultorio' => $_POST['id_consultorio'],

        ':nombre'         => $_POST['nombre'],

        ':direccion'      => $_POST['direccion'],

        ':telefono'       => $_POST['telefono']
    ];


    $consultorioModel->actualizar($data);

}



elseif ($accion == 'eliminar_consultorio') {

    $consultorioModel->eliminar($_GET['id']);

}


header("Location: ../views/html/consultorios/consultorio.php");

exit();

?>