<?php

session_start();

require_once "../models/Usuario.php";


$usuarioModel = new Usuario();



if (isset($_POST['register'])) {

    $correo = $_POST['correo_electronico'];

   
    if ($usuarioModel->correoExiste($correo)) {

        $_SESSION['error_register'] = "El correo ya está registrado.";

        header("Location: ../views/html/auth/register.php");
        exit();

    } else {

        $data = [
            ':primer_nombre'      => $_POST['primer_nombre'],
            ':segundo_nombre'     => $_POST['segundo_nombre']    ?? null,
            ':primer_apellido'    => $_POST['primer_apellido'],
            ':segundo_apellido'   => $_POST['segundo_apellido']  ?? null,
            ':numero_de_cedula'   => $_POST['numero_de_cedula'],
            ':correo_electronico' => $correo,
            ':direccion'          => $_POST['direccion']         ?? null,
            ':numero_de_celular'  => $_POST['numero_de_celular'],
            ':contrasena'         => password_hash($_POST['contrasena'], PASSWORD_DEFAULT),

            ':rol'                => 'recepcionista'
        ];

        $usuarioModel->crearUsuario($data);

        $_SESSION['success_register'] = "Usuario registrado correctamente.";

        header("Location: ../views/html/auth/register.php");
        exit();
    }
}


if (isset($_POST['login'])) {

    $correo   = $_POST['correo_electronico'];
    $password = $_POST['contrasena'];
    $usuario = $usuarioModel->login($correo);

    if ($usuario && password_verify($password, $usuario['contraseña'])) {

        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre']     = $usuario['primer_nombre'];
        $_SESSION['rol']        = $usuario['rol'];
        header("Location: ../views/html/home.php");
        exit();

    } else {

        $_SESSION['error_login'] = "Correo o contraseña incorrectos.";
        header("Location: ../views/html/auth/login.php");
        exit();
    }
}


$accion = $_POST['accion'] ?? '';


if ($accion == 'crear_usuario') {

    $correo = $_POST['correo_electronico'];

    if ($usuarioModel->correoExiste($correo)) {

    
        $_SESSION['error_crud'] = "El correo ya está registrado.";

    } else {

      
        $data = [
            ':primer_nombre'      => $_POST['primer_nombre'],
            ':segundo_nombre'     => $_POST['segundo_nombre']   ?? null,
            ':primer_apellido'    => $_POST['primer_apellido'],
            ':segundo_apellido'   => $_POST['segundo_apellido'] ?? null,
            ':numero_de_cedula'   => $_POST['numero_de_cedula'],
            ':correo_electronico' => $correo,
            ':direccion'          => $_POST['direccion']        ?? null,
            ':numero_de_celular'  => $_POST['numero_de_celular'],

            ':contrasena'         => password_hash($_POST['contrasena'], PASSWORD_DEFAULT),

            ':rol'                => $_POST['rol']
        ];

        if ($usuarioModel->crearUsuario($data)) {

            $_SESSION['success_crud'] = "Usuario creado exitosamente.";

        } else {

            $_SESSION['error_crud'] = "Error al guardar el usuario en la BD.";
        }
    }

   
    header("Location: ../views/html/usuarios/CRUD_Usuarios.php");
    exit();



} elseif ($accion == 'editar_usuario') {

    $contrasena_hash = null;

    if (!empty($_POST['contrasena'])) {

        $contrasena_hash = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    }
   
    $data = [
       
        ':id_usuario'         => $_POST['id_usuario'],
        ':primer_nombre'      => $_POST['primer_nombre'],
        ':segundo_nombre'     => $_POST['segundo_nombre']    ?? null,
        ':primer_apellido'    => $_POST['primer_apellido'],
        ':segundo_apellido'   => $_POST['segundo_apellido']  ?? null,
        ':numero_de_cedula'   => $_POST['numero_de_cedula'],
        ':correo_electronico' => $_POST['correo_electronico'],
        ':direccion'          => $_POST['direccion']         ?? null,
        ':numero_de_celular'  => $_POST['numero_de_celular'],
        ':rol'                => $_POST['rol']
    ];

    if ($usuarioModel->actualizarUsuario($data, $contrasena_hash)) {

        $_SESSION['success_crud'] = "Usuario actualizado correctamente.";

    } else {

        $_SESSION['error_crud'] = "Error al actualizar el usuario.";
    }

    header("Location: ../views/html/usuarios/CRUD_Usuarios.php");
    exit();
}

if (isset($_GET['eliminar'])) {

    if ($usuarioModel->eliminarUsuario($_GET['eliminar'])) {

        $_SESSION['success_crud'] = "Usuario eliminado correctamente.";

    } else {

        $_SESSION['error_crud'] = "No se pudo eliminar el usuario.";
    }

    header("Location: ../views/html/usuarios/CRUD_Usuarios.php");
    exit();
}

?>