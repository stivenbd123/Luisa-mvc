<?php

session_start();

require_once "../../../models/Medico.php";
require_once "../../../models/Especialidad.php";

$medicoModel      = new Medico();
$especialidadModel = new Especialidad();


$medicos       = $medicoModel->obtenerTodos();


$especialidades = $especialidadModel->obtenerTodas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Médicos y Especialidades</title>

    
    <link rel="stylesheet" href="../../../public/css/styleCRUDMedicos.css">
</head>
<body>


<header>
    <span>Gestión de Personal Médico</span>
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>


<div class="container">



    <div class="top-bar">
        <h2>Especialidades</h2>
        
        <button class="btn btn-primary" onclick="openEspecialidadModal('crear')">+ Nueva Especialidad</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($especialidades as $e): ?>
            <tr>
                <td><strong><?= $e['id_especialidad'] ?></strong></td>
                <td><?= $e['nombre_especialidad'] ?></td>
                <td>
               
                    <button class="btn btn-warning"
                            onclick='openEspecialidadModal("editar", <?= json_encode($e) ?>)'>
                        Editar
                    </button>

                
                    <a href="../../../controllers/MedicoController.php?accion=eliminar_especialidad&id=<?= $e['id_especialidad'] ?>"
                       class="btn btn-danger"
                       onclick="return confirm('¿Eliminar esta especialidad? Los médicos asociados quedarán sin especialidad.')">
                        Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    

    <div class="top-bar" style="margin-top: 40px;">
        <h2>Médicos Registrados</h2>
        <!-- "openMedicoModal('crear')" = abre el modal de médico en modo crear. -->
        <button class="btn btn-primary" onclick="openMedicoModal('crear')">+ Nuevo Médico</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>Especialidad</th>
                <th>Correo</th>
                <th>Celular</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($medicos as $m): ?>
            <tr>
       
                <td><?= $m['primer_nombre']." ".$m['segundo_nombre']." ".$m['primer_apellido']." ".$m['segundo_apellido'] ?></td>

           
                <td><?= $m['nombre_especialidad'] ?></td>
                <td><?= $m['correo_electronico'] ?></td>
                <td><?= $m['numero_de_celular'] ?></td>
                <td>
                    <button class="btn btn-warning"
                            onclick='openMedicoModal("editar", <?= json_encode($m) ?>)'>
                        Editar
                    </button>
                    <a href="../../../controllers/MedicoController.php?accion=eliminar_medico&id=<?= $m['id_medico'] ?>"
                       class="btn btn-danger"
                       onclick="return confirm('¿Eliminar este médico? Se perderán sus citas asociadas.')">
                        Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>


<div class="modal" id="especialidadModal">
    <div class="modal-content modal-sm">
       
        <h3 id="espTitle">Nueva Especialidad</h3>
        <hr>

        <form action="../../../controllers/MedicoController.php" method="POST">
            
            <input type="hidden" name="accion" id="espAccion" value="crear_especialidad">

            
            <input type="hidden" name="id_especialidad" id="id_especialidad">

            <div class="form-group">
                <label>Nombre de la Especialidad</label>
               
                <input type="text"
                       name="nombre_especialidad"
                       id="nombre_especialidad"
                       required
                       placeholder="Ej: Cardiología">
            </div>

            <div class="modal-actions">
               
                <button type="button" class="btn btn-secondary"
                        onclick="closeEspecialidadModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>



<div class="modal" id="medicoModal">
    <div class="modal-content">
        <h3 id="medTitle">Nuevo Médico</h3>
        <hr>

        <form action="../../../controllers/MedicoController.php" method="POST">
           
            <input type="hidden" name="accion" id="medAccion" value="crear_medico">

            <input type="hidden" name="id_medico" id="id_medico">

           
            <div class="row">
                <div class="form-group">
                    <label>Primer Nombre</label>
                    <input type="text" name="primer_nombre" id="primer_nombre"
                           placeholder="Primer Nombre" required>
                </div>
                <div class="form-group">
                    <label>Segundo Nombre</label>
                    <input type="text" name="segundo_nombre" id="segundo_nombre"
                           placeholder="Segundo Nombre">
                </div>
            </div>

           
            <div class="row">
                <div class="form-group">
                    <label>Primer Apellido</label>
                    <input type="text" name="primer_apellido" id="primer_apellido"
                           placeholder="Primer Apellido" required>
                </div>
                <div class="form-group">
                    <label>Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" id="segundo_apellido"
                           placeholder="Segundo Apellido">
                </div>
            </div>

            
            <div class="form-group">
                <label>Especialidad</label>
                <select name="id_especialidad" id="med_id_especialidad" required>
                    <option value="">Seleccione Especialidad</option>
                    <?php foreach($especialidades as $e): ?>
                        
                        <option value="<?= $e['id_especialidad'] ?>">
                            <?= $e['nombre_especialidad'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

           
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="correo_electronico" id="med_correo"
                       placeholder="correo@ejemplo.com" required>
            </div>

           
            <div class="form-group">
                <label>Celular</label>
                <input type="text" name="numero_de_celular" id="med_celular"
                       placeholder="Ej: 3001234567">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary"
                        onclick="closeMedicoModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>



<script src="../../../public/js/medicos.js"></script>

</body>
</html>