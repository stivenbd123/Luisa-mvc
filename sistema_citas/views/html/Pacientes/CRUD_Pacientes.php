<?php

// Inicia la sesión para leer los mensajes de $_SESSION
// guardados por el controlador (success_paciente, error_paciente).
session_start();

// Carga el modelo Paciente.
// "__DIR__" = ruta absoluta de la carpeta actual (views/html/Pacientes/).
// "/../../../models/Paciente.php" = sube 3 niveles hasta la raíz y entra a models/.
require_once "../../../models/Paciente.php";

// Se instancia el modelo y se obtienen todos los pacientes de la BD.
// El resultado se guarda en $pacientes para iterarlo en la tabla HTML.
$pacienteModel = new Paciente();
$pacientes = $pacienteModel->obtenerPacientes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pacientes | Sistema Médico</title>

    <!-- CSS específico para la vista de pacientes. -->
    <link rel="stylesheet" href="../../../public/css/styleCRUDPacientes.css">
</head>
<body>

<!-- ================================================
     HEADER: barra superior con título y botón de volver
     ================================================ -->
<header>
    <span>Sistema de Gestión de Citas Médicas - Pacientes</span>
    <!-- "../home.php" = sube un nivel desde Pacientes/ hasta html/
         donde vive home.php. -->
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>


<div class="container">

    <!-- ================================================
         ALERTAS DE SESIÓN
         Claves específicas de pacientes: 'success_paciente' y 'error_paciente'.
         Diferentes a las de usuarios ('success_crud', 'error_crud')
         para evitar conflictos si ambas páginas están abiertas.
         ================================================ -->

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


    <!-- ================================================
         BARRA SUPERIOR: título + botón "Nuevo Paciente"
         ================================================ -->
    <div class="top-bar">
        <h2>Listado de Pacientes</h2>

        <!-- "openModal('crear')" = llama a la función en pacientes.js
             con el modo 'crear' — formulario vacío, acción = crear. -->
        <button class="btn btn-primary" onclick="openModal('crear')">+ Nuevo Paciente</button>
    </div>


    <!-- ================================================
         TABLA DE PACIENTES
         ================================================ -->
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
                // Itera sobre el arreglo $pacientes.
                // En cada iteración $p contiene los datos de un paciente.
                foreach($pacientes as $p): ?>
                <tr>
                    <td><strong><?= $p['id_paciente'] ?></strong></td>

                    <!-- Concatena nombre y apellido con espacio. -->
                    <td><?= $p['primer_nombre']." ".$p['primer_apellido'] ?></td>

                    <td><?= $p['numero_de_cedula'] ?></td>
                    <td><?= $p['correo_electronico'] ?></td>
                    <td><?= $p['numero_de_celular'] ?></td>

                    <td>
                        <!-- BOTÓN EDITAR:
                             "openModal('editar', ...)" pasa los datos del paciente como
                             objeto JavaScript usando json_encode().
                             DIFERENCIA con CRUD_Usuarios: aquí se usa comillas simples
                             en el onclick exterior para que json_encode() pueda usar
                             comillas dobles internas sin conflicto.
                             En CRUD_Usuarios se usó htmlspecialchars() como alternativa.
                             RECOMENDACIÓN: usar htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8')
                             para manejar correctamente apellidos con comillas (ej: O'Brien). -->
                        <button class="btn btn-warning"
                                onclick='openModal("editar", <?= json_encode($p) ?>)'>
                            Editar
                        </button>

                        <!-- ENLACE ELIMINAR:
                             "?accion=eliminar&id=ID" = envía AMBOS parámetros por GET.
                             El controlador lee: $accion = $_GET['accion'] y $id = $_GET['id'].
                             DIFERENCIA con CRUD_Usuarios que usaba ?eliminar=ID.
                             confirm() solicita confirmación antes de navegar al enlace. -->
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
<!-- Fin de .container -->


<!-- ================================================
     MODAL: formulario reutilizable para CREAR y EDITAR pacientes.
     Mismo patrón que CRUD_Usuarios pero para pacientes.
     La función openModal() en pacientes.js configura el modo.
     ================================================ -->
<div class="modal" id="pacienteModal">
    <div class="modal-content">

        <!-- JavaScript cambia este texto según el modo:
             'Nuevo Paciente' al crear, 'Editar Paciente' al editar. -->
        <h3 id="modalTitle">Nuevo Paciente</h3>
        <hr>

        <form action="../../../controllers/pacienteController.php" method="POST" id="pacienteForm">

            <!-- CAMPO OCULTO: ACCIÓN
                 Valores posibles: 'crear' o 'editar'.
                 JavaScript lo cambia según el modo del modal.
                 El controlador lo lee con: $accion = $_POST['accion']. -->
            <input type="hidden" name="accion" id="formAccion" value="crear">

            <!-- CAMPO OCULTO: ID DEL PACIENTE
                 Vacío al crear (no existe ID aún).
                 JavaScript lo llena al editar: id_paciente.value = data.id_paciente. -->
            <input type="hidden" name="id_paciente" id="id_paciente">

            <!-- FILA 1: NOMBRES -->
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

            <!-- CÉDULA (ancho completo — campo identificador único) -->
            <div class="form-group">
                <label>Número de Cédula</label>
                <input type="text" name="numero_de_cedula" id="numero_de_cedula" required>
            </div>

            <!-- CORREO (ancho completo) -->
            <div class="form-group">
                <label>Correo Electrónico</label>
                <!-- type="email" = validación de formato de correo por el navegador. -->
                <input type="email" name="correo_electronico" id="correo_electronico" required>
            </div>

            <!-- FILA 3: CELULAR Y DIRECCIÓN (opcionales) -->
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

            <!-- BOTONES DEL MODAL -->
            <div class="modal-actions">
                <!-- type="button" = NO envía el formulario, solo llama a closeModal(). -->
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                <!-- type="submit" = envía el formulario al controlador. -->
                <button type="submit" class="btn btn-primary">Guardar Paciente</button>
            </div>

        </form>
    </div>
</div>
<!-- Fin del modal -->


<!-- Script cargado al final del body para garantizar que todos los
     elementos HTML ya están en el DOM cuando el script los busca. -->
<script src="../../../public/js/pacientes.js"></script>

</body>
</html>