<?php

session_start();


require_once "../../../models/Paciente.php";

$pacienteModel = new Paciente();
$pacientes = $pacienteModel->obtenerPacientes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pacientes | Sistema Médico</title>

    
    <link rel="stylesheet" href="../../../public/css/styleCRUDPacientes.css">
</head>
<body>


<header>
    <span>Sistema de Gestión de Citas Médicas - Pacientes</span>
  
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>


<div class="container">

    

    <?php if(isset($_SESSION['success_paciente'])): ?>
        <div class="alert success-alert">
            <?php echo $_SESSION['success_paciente']; unset($_SESSION['success_paciente']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_paciente'])): ?>
        <div class="alert error-alert">
            <?php echo $_SESSION['error_paciente']; unset($_SESSION['error_paciente']); ?>
        </div>
    <?php endif; ?>


   
    <div class="top-bar">
        <h2>Listado de Pacientes</h2>

        <button class="btn btn-primary" onclick="openModal('crear')">+ Nuevo Paciente</button>
    </div>


 
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Cédula</th>
                    <th>Correo</th>
                    <th>Celular</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
              
                foreach($pacientes as $p): ?>
                <tr>
                    <td><strong><?= $p['id_paciente'] ?></strong></td>

                  
                    <td><?= $p['primer_nombre']." ".$p['primer_apellido'] ?></td>

                    <td><?= $p['numero_de_cedula'] ?></td>
                    <td><?= $p['correo_electronico'] ?></td>
                    <td><?= $p['numero_de_celular'] ?></td>

                    <td>
                        
                        <button class="btn btn-warning"
                                onclick='openModal("editar", <?= json_encode($p) ?>)'>
                            Editar
                        </button>

                        
                        <a href="../../../controllers/pacienteController.php?accion=eliminar&id=<?= $p['id_paciente'] ?>"
                           class="btn btn-danger"
                           onclick="return confirm('¿Está seguro de eliminar este paciente?')">
                            Eliminar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>




<div class="modal" id="pacienteModal">
    <div class="modal-content">

        
        <h3 id="modalTitle">Nuevo Paciente</h3>
        <hr>

        <form action="../../../controllers/pacienteController.php" method="POST" id="pacienteForm">

            <input type="hidden" name="accion" id="formAccion" value="crear">

            <input type="hidden" name="id_paciente" id="id_paciente">

           
            <div class="row">
                <div class="form-group">
                    <label>Primer Nombre</label>
                    <input type="text" name="primer_nombre" id="primer_nombre" required>
                </div>
                <div class="form-group">
                    <label>Segundo Nombre</label>
                    <!-- Sin required = campo opcional. -->
                    <input type="text" name="segundo_nombre" id="segundo_nombre">
                </div>
            </div>

            <!-- FILA 2: APELLIDOS -->
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

            
            <div class="form-group">
                <label>Número de Cédula</label>
                <input type="text" name="numero_de_cedula" id="numero_de_cedula" required>
            </div>

            
            <div class="form-group">
                <label>Correo Electrónico</label>
                <!-- type="email" = validación de formato de correo por el navegador. -->
                <input type="email" name="correo_electronico" id="correo_electronico" required>
            </div>

         
            <div class="row">
                <div class="form-group">
                    <label>Celular</label>
                    <input type="text" name="numero_de_celular" id="numero_de_celular">
                </div>
                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" name="direccion" id="direccion">
                </div>
            </div>

           
            <div class="modal-actions">
                
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                
                <button type="submit" class="btn btn-primary">Guardar Paciente</button>
            </div>

        </form>
    </div>
</div>



<script src="../../../public/js/pacientes.js"></script>

</body>
</html>