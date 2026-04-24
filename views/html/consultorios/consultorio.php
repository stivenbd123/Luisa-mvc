<?php

session_start();

require_once "../../../models/Consultorio.php";


$consultorioModel = new Consultorio();
$consultorios     = $consultorioModel->obtenerTodos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Consultorios</title>
    <link rel="stylesheet" href="../../../public/css/styleCRUDConsultorios.css">
</head>
<body>


<header>
    <span>Gestión de Consultorios</span>
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>


<div class="container">

   
    <div class="top-bar">
        <h2>Consultorios Registrados</h2>
       
        <button class="btn btn-primary" onclick="openModal('crear')">
            + Nuevo Consultorio
        </button>
    </div>


    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php
           
            foreach($consultorios as $c): ?>

            <tr>
                <td><?= $c['id_consultorio'] ?></td>
                <td><?= $c['nombre'] ?></td>
                <td><?= $c['direccion'] ?></td>
                <td><?= $c['telefono'] ?></td>

                <td>
                   
                    <button class="btn btn-warning"
                            onclick='openModal("editar", <?= json_encode($c) ?>)'>
                        Editar
                    </button>

                   
                    <a href="../../../controllers/consultorioController.php?accion=eliminar_consultorio&id=<?= $c['id_consultorio'] ?>"
                       class="btn btn-danger"
                       onclick="return confirm('¿Eliminar este consultorio? Las citas asignadas a él quedarán sin consultorio.')">
                        Eliminar
                    </a>
                </td>
            </tr>

            <?php endforeach; ?>
        </tbody>
    </table>

</div>




<div class="modal" id="consultorioModal">
    <div class="modal-content">

        
        <h3 id="modalTitle">Nuevo Consultorio</h3>
        <hr>

        <form action="../../../controllers/consultorioController.php" method="POST">

           
            <input type="hidden" name="accion" id="accion">

            <input type="hidden" name="id_consultorio" id="id_consultorio">

          
            <div class="form-group">
                <label>Nombre del Consultorio</label>
               
                <input type="text"
                       name="nombre"
                       id="nombre"
                       placeholder="Ej: Consultorio 1 - Cardiología"
                       required>
            </div>

     
            <div class="form-group">
                <label>Dirección</label>
                <input type="text"
                       name="direccion"
                       id="direccion"
                       placeholder="Ej: Piso 2, Sala B"
                       required>
            </div>

           
            <div class="form-group">
                <label>Teléfono</label>
                
                <input type="text"
                       name="telefono"
                       id="telefono"
                       placeholder="Ej: 6011234567">
            </div>

       
            <div class="modal-actions">
                
                <button type="button" class="btn btn-secondary"
                        onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>

        </form>
    </div>
</div>




<script src="../../../public/js/consultorios.js"></script>

</body>
</html>

<?php

?>