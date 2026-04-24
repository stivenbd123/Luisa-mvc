<?php

session_start();

require_once __DIR__ . "/../../../models/Usuario.php";


if(!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador'){
    // Redirige a home.php — dos niveles arriba desde Usuarios/.
    header("Location: ../home.php");
    // Detiene la ejecución para que no se renderice HTML de la vista.
    exit();
}


$usuarioModel = new Usuario();

$usuarios = $usuarioModel->obtenerUsuarios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios | Sistema Médico</title>

    <link rel="stylesheet" href="../../../public/css/styleCRUDUsuarios.css">
</head>
<body>


<header>
   
    <span>Panel de Administración - Usuarios</span>

    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>


<div class="container">

    <?php if(isset($_SESSION['success_crud'])): ?>
        
        <div class="alert success-alert">
            <?php
        
            echo $_SESSION['success_crud'];

            unset($_SESSION['success_crud']);
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_crud'])): ?>
        <div class="alert error-alert">
            <?php
            echo $_SESSION['error_crud'];
            unset($_SESSION['error_crud']);
            ?>
        </div>
    <?php endif; ?>


    <div class="top-bar">
        <h2>Listado de Usuarios</h2>
        <button class="btn btn-primary" onclick="openModal('create')">+ Nuevo Usuario</button>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
               
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php
              
                foreach($usuarios as $u): ?>

                <tr>
                    
                    <td><strong><?= $u['id_usuario']; ?></strong></td>

                    <td><?= $u['primer_nombre']." ".$u['primer_apellido']; ?></td>

                    <td><?= $u['numero_de_cedula']; ?></td>
                    <td><?= $u['correo_electronico']; ?></td>

                    <td>
                        
                        <span class="badge"><?= ucfirst($u['rol']); ?></span>
                    </td>

                    <td>
              
                        <button class="btn btn-warning"
                                onclick="openModal('edit', <?= htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8') ?>)">
                            Editar
                        </button>

                       
                        <a href="../../../controllers/usuarioController.php?eliminar=<?= $u['id_usuario']; ?>"
                           class="btn btn-danger"
                           onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
                            Eliminar
                        </a>
                    </td>
                </tr>

                <?php endforeach; ?>
               
            </tbody>
        </table>
    </div>
   

</div>



<div class="modal" id="userModal">

    <div class="modal-content">

        <h3 id="modalTitle">Nuevo Usuario</h3>
        <hr>

        <form id="userForm" action="../../../controllers/usuarioController.php" method="POST">

        
            <input type="hidden" name="id_usuario" id="form_id_usuario">

            <input type="hidden" name="accion" id="form_accion" value="crear_usuario">

            <div class="row">
                <div class="form-group">
                    <label>Primer Nombre</label>
                    
                    <input type="text" name="primer_nombre" id="primer_nombre" required>
                </div>
                <div class="form-group">
                    <label>Segundo Nombre</label>
                   
                    <input type="text" name="segundo_nombre" id="segundo_nombre">
                </div>
            </div>

            
            <div class="row">
                <div class="form-group">
                    <label>Primer Apellido</label>
                    <input type="text" name="primer_apellido" id="primer_apellido" required>
                </div>
                <div class="form-group">
                    <label>Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" id="segundo_apellido">
                </div>
            </div>

            
            <div class="row">
                <div class="form-group">
                    <label>Cédula</label>
                    <input type="text" name="numero_de_cedula" id="numero_de_cedula" required>
                </div>
                <div class="form-group">
                    <label>Celular</label>
                    <input type="text" name="numero_de_celular" id="numero_de_celular" required>
                </div>
            </div>

          
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="correo_electronico" id="correo_electronico" required>
            </div>

            
            <div class="form-group">
                <label>Rol del Sistema</label>

                
                <select name="rol" id="rol" required>
                   
                    <option value="">Seleccione...</option>
                    <option value="administrador">Administrador</option>
                    <option value="recepcionista">Recepcionista</option>
                </select>
            </div>

            
            <div class="form-group">
                <label>Contraseña</label>

                <input type="password" name="contrasena" id="contrasena">

                <small id="pass_hint" style="display:none; color:#666;">
                    (Dejar en blanco para no cambiar)
                </small>
            </div>

            
            <div class="modal-actions">
                
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-primary">
                    Guardar Usuario
                </button>
            </div>

        </form>
      

    </div>
    

</div>




<script src="../../../public/js/usuarios.js"></script>

</body>
</html>