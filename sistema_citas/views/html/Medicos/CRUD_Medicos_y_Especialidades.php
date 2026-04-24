<?php

session_start();

// Se cargan AMBOS modelos porque esta vista gestiona dos entidades.
require_once "../../../models/Medico.php";
require_once "../../../models/Especialidad.php";

// Se instancian ambos modelos.
$medicoModel      = new Medico();
$especialidadModel = new Especialidad();

// Se obtienen los datos de las dos tablas para poblar ambas tablas HTML.
// $medicos incluye el nombre de especialidad via JOIN (ver Medico::obtenerTodos).
$medicos       = $medicoModel->obtenerTodos();

// $especialidades se usa en DOS lugares:
// 1. Para poblar la tabla de especialidades.
// 2. Para poblar el <select> dentro del modal de médicos.
$especialidades = $especialidadModel->obtenerTodas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Médicos y Especialidades</title>

    <!-- CSS propio del módulo de médicos (color morado #6f42c1). -->
    <link rel="stylesheet" href="../../../public/css/styleCRUDMedicos.css">
</head>
<body>

<!-- ================================================
     HEADER
     ================================================ -->
<header>
    <span>Gestión de Personal Médico</span>
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>


<div class="container">

    <!-- ================================================
         SECCIÓN 1: ESPECIALIDADES
         Tabla pequeña en la parte superior de la página.
         ================================================ -->

    <div class="top-bar">
        <h2>Especialidades</h2>
        <!-- "openEspecialidadModal('crear')" = abre el modal de especialidad
             en modo crear — formulario vacío. Definido en medicos.js. -->
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
                    <!-- "openEspecialidadModal('editar', datos)" = abre el modal
                         en modo editar pasando los datos de la especialidad como JSON. -->
                    <button class="btn btn-warning"
                            onclick='openEspecialidadModal("editar", <?= json_encode($e) ?>)'>
                        Editar
                    </button>

                    <!-- Elimina la especialidad vía GET con su ID.
                         confirm() solicita confirmación antes de ejecutar. -->
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


    <!-- ================================================
         SECCIÓN 2: MÉDICOS
         Tabla principal debajo de la de especialidades.
         ================================================ -->

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
                <!-- Se concatenan los 4 campos de nombre del médico.
                     Los campos opcionales (segundo_nombre, segundo_apellido)
                     pueden agregar un espacio extra si están vacíos.
                     MEJORA: usar trim() para eliminar espacios dobles:
                     trim($m['primer_nombre']." ".$m['segundo_nombre']..." ") -->
                <td><?= $m['primer_nombre']." ".$m['segundo_nombre']." ".$m['primer_apellido']." ".$m['segundo_apellido'] ?></td>

                <!-- "nombre_especialidad" viene del JOIN en Medico::obtenerTodos(). -->
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
<!-- Fin de .container -->


<!-- ================================================
     MODAL 1: ESPECIALIDAD (crear y editar)
     Modal más pequeño — solo tiene un campo: nombre.
     ================================================ -->
<div class="modal" id="especialidadModal">
    <div class="modal-content modal-sm">
        <!-- "id='espTitle'" = JavaScript cambia este texto según el modo. -->
        <h3 id="espTitle">Nueva Especialidad</h3>
        <hr>

        <form action="../../../controllers/MedicoController.php" method="POST">
            <!-- "accion" = 'crear_especialidad' o 'editar_especialidad'. -->
            <input type="hidden" name="accion" id="espAccion" value="crear_especialidad">

            <!-- ID de la especialidad — vacío al crear, lleno al editar. -->
            <input type="hidden" name="id_especialidad" id="id_especialidad">

            <div class="form-group">
                <label>Nombre de la Especialidad</label>
                <!-- "placeholder='Nombre'" = texto de guía dentro del input
                     que desaparece al escribir. No reemplaza al label. -->
                <input type="text"
                       name="nombre_especialidad"
                       id="nombre_especialidad"
                       required
                       placeholder="Ej: Cardiología">
            </div>

            <div class="modal-actions">
                <!-- "type='button'" = NO envía el formulario. -->
                <button type="button" class="btn btn-secondary"
                        onclick="closeEspecialidadModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>


<!-- ================================================
     MODAL 2: MÉDICO (crear y editar)
     Modal más grande — tiene 6 campos más el select de especialidad.
     ================================================ -->
<div class="modal" id="medicoModal">
    <div class="modal-content">
        <h3 id="medTitle">Nuevo Médico</h3>
        <hr>

        <form action="../../../controllers/MedicoController.php" method="POST">
            <!-- "accion" = 'crear_medico' o 'editar_medico'. -->
            <input type="hidden" name="accion" id="medAccion" value="crear_medico">

            <!-- ID del médico — vacío al crear, lleno al editar. -->
            <input type="hidden" name="id_medico" id="id_medico">

            <!-- FILA: NOMBRES -->
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

            <!-- FILA: APELLIDOS -->
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

            <!-- SELECT DE ESPECIALIDAD
                 "id='med_id_especialidad'" = JavaScript lo precarga en modo editar.
                 Las opciones se generan dinámicamente desde $especialidades
                 que PHP ya cargó al inicio de la vista. -->
            <div class="form-group">
                <label>Especialidad</label>
                <select name="id_especialidad" id="med_id_especialidad" required>
                    <option value="">Seleccione Especialidad</option>
                    <?php foreach($especialidades as $e): ?>
                        <!-- "value='ID'" = lo que se envía al controlador ($_POST['id_especialidad']).
                             El texto visible es el nombre de la especialidad. -->
                        <option value="<?= $e['id_especialidad'] ?>">
                            <?= $e['nombre_especialidad'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- CORREO -->
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="correo_electronico" id="med_correo"
                       placeholder="correo@ejemplo.com" required>
            </div>

            <!-- CELULAR -->
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


<!-- Script cargado al final del body — el DOM ya existe cuando el script lo lee. -->
<script src="../../../public/js/medicos.js"></script>

</body>
</html>