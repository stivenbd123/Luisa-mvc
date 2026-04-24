<?php

session_start();

// Carga el modelo Consultorio.
// "__DIR__" + "/../../../" = sube 3 niveles desde consultorios/ hasta la raíz.
require_once "../../../models/Consultorio.php";

// Se instancia el modelo y se obtienen todos los consultorios.
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

<!-- ================================================
     HEADER
     ================================================ -->
<header>
    <span>Gestión de Consultorios</span>
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>


<div class="container">

    <!-- ================================================
         BARRA SUPERIOR
         ================================================ -->
    <div class="top-bar">
        <h2>Consultorios Registrados</h2>
        <!-- "openModal('crear')" = abre el modal en modo crear.
             Definido en consultorios.js. -->
        <button class="btn btn-primary" onclick="openModal('crear')">
            + Nuevo Consultorio
        </button>
    </div>


    <!-- ================================================
         TABLA DE CONSULTORIOS
         ================================================ -->
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
            // Itera sobre el arreglo de consultorios retornado por el modelo.
            // $c contiene los datos de cada consultorio en cada iteración.
            foreach($consultorios as $c): ?>

            <tr>
                <td><?= $c['id_consultorio'] ?></td>
                <td><?= $c['nombre'] ?></td>
                <td><?= $c['direccion'] ?></td>
                <td><?= $c['telefono'] ?></td>

                <td>
                    <!-- BOTÓN EDITAR:
                         json_encode($c) convierte el arreglo PHP a JSON para JavaScript.
                         Las comillas simples externas del onclick permiten que json_encode
                         use comillas dobles internamente sin conflicto. -->
                    <button class="btn btn-warning"
                            onclick='openModal("editar", <?= json_encode($c) ?>)'>
                        Editar
                    </button>

                    <!-- ENLACE ELIMINAR:
                         Envía accion=eliminar_consultorio e id por GET.
                         confirm() solicita confirmación antes de navegar. -->
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
<!-- Fin de .container -->


<!-- ================================================
     MODAL: crear y editar consultorio.
     Un solo modal con 3 campos: nombre, dirección, teléfono.
     JavaScript lo configura según el modo (crear/editar).
     ================================================ -->
<div class="modal" id="consultorioModal">
    <div class="modal-content">

        <!-- "id='modalTitle'" = JavaScript cambia este texto según el modo. -->
        <h3 id="modalTitle">Nuevo Consultorio</h3>
        <hr>

        <form action="../../../controllers/consultorioController.php" method="POST">

            <!-- CAMPO OCULTO: ACCIÓN
                 Valores: 'crear_consultorio' o 'editar_consultorio'.
                 El controlador lee: $accion = $_POST['accion']. -->
            <input type="hidden" name="accion" id="accion">

            <!-- CAMPO OCULTO: ID DEL CONSULTORIO
                 Vacío al crear. JavaScript lo llena al editar
                 con data.id_consultorio para el WHERE del UPDATE. -->
            <input type="hidden" name="id_consultorio" id="id_consultorio">

            <!-- NOMBRE DEL CONSULTORIO -->
            <div class="form-group">
                <label>Nombre del Consultorio</label>
                <!-- "placeholder" = texto guía que desaparece al escribir.
                     No reemplaza al label — sirve como ejemplo del formato esperado. -->
                <input type="text"
                       name="nombre"
                       id="nombre"
                       placeholder="Ej: Consultorio 1 - Cardiología"
                       required>
            </div>

            <!-- DIRECCIÓN -->
            <div class="form-group">
                <label>Dirección</label>
                <input type="text"
                       name="direccion"
                       id="direccion"
                       placeholder="Ej: Piso 2, Sala B"
                       required>
            </div>

            <!-- TELÉFONO (opcional — sin required) -->
            <div class="form-group">
                <label>Teléfono</label>
                <!-- Sin "required" = el teléfono es opcional.
                     Si se deja vacío, el modelo guarda '' o null según la BD. -->
                <input type="text"
                       name="telefono"
                       id="telefono"
                       placeholder="Ej: 6011234567">
            </div>

            <!-- BOTONES DEL MODAL -->
            <div class="modal-actions">
                <!-- "type='button'" = NO envía el formulario. -->
                <button type="button" class="btn btn-secondary"
                        onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>

        </form>
    </div>
</div>
<!-- Fin del modal -->


<!-- Script al final del body — DOM ya construido cuando el script corre. -->
<script src="../../../public/js/consultorios.js"></script>

</body>
</html>

<?php

?>