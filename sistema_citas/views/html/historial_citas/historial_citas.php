<?php
session_start();

// Conexión directa sin modelo dedicado para la carga inicial.
require_once "../../../config/database.php";

$db   = new Database();
$conn = $db->conectar();

// --- CARGA INICIAL: todas las citas para el primer render ---
// Esta query solo se ejecuta una vez al cargar la página.
// Las búsquedas posteriores no recargan la página — usan AJAX.
$sql = "SELECT
            c.id_cita,
            CONCAT(p.primer_nombre, ' ', p.primer_apellido) AS paciente,
            p.numero_de_cedula,
            e.nombre_especialidad,
            CONCAT(m.primer_nombre, ' ', m.primer_apellido) AS medico,
            co.nombre  AS consultorio,
            c.fecha,
            c.hora,
            c.estado
        FROM citas c
        INNER JOIN pacientes p      ON c.id_paciente    = p.id_paciente
        INNER JOIN medicos m        ON c.id_medico      = m.id_medico
        INNER JOIN especialidades e ON m.id_especialidad = e.id_especialidad
        INNER JOIN consultorios co  ON c.id_consultorio  = co.id_consultorio
        ORDER BY c.fecha DESC, c.hora DESC";

// "->query($sql)->fetchAll()" = se encadena directamente — query sin parámetros.
$citas = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Citas | Sistema de Gestión de Citas Médicas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/css/styleHistorial.css">
</head>
<body>

<header>
    Sistema de Gestión de Citas Médicas — Historial
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>

<div class="container">

    <!-- ================================================
         CAJA DE FILTROS DE BÚSQUEDA
         Los inputs no están en un <form> porque la búsqueda
         se hace con AJAX — no se necesita el submit de formulario.
         JavaScript lee los valores directamente con getElementById.
         ================================================ -->
    <div class="filter-box">
        <h3>🔍 Buscar historial</h3>
        <div class="filter-row">
            <!-- "id='inputNombre'" = JavaScript lo lee con getElementById. -->
            <input type="text" id="inputNombre" placeholder="Nombre del paciente">

            <!-- Búsqueda alternativa por cédula. -->
            <input type="text" id="inputCedula" placeholder="Número de cédula">

            <!-- "onclick='buscar()'" = dispara la búsqueda AJAX sin enviar formulario. -->
            <button class="btn btn-primary" onclick="buscar()">Buscar</button>

            <!-- "onclick='limpiar()'" = vacía los inputs y recarga todas las citas. -->
            <button class="btn btn-secondary" onclick="limpiar()">Limpiar</button>
        </div>
    </div>

    <!-- Spinner de carga — visible solo durante peticiones AJAX. -->
    <!-- JavaScript lo muestra/oculta con mostrarSpinner(true/false). -->
    <div class="spinner" id="spinner">⏳ Buscando...</div>

    <!-- Contador de resultados — JavaScript actualiza este texto. -->
    <p class="result-info" id="resultInfo"></p>


    <!-- ================================================
         TABLA DE HISTORIAL
         "id='tbodyHistorial'" = JavaScript reemplaza el contenido
         de este tbody con las filas de los resultados de búsqueda.
         ================================================ -->
    <div class="table-wrapper">
        <table id="tablaHistorial">
            <thead>
                <tr>
                    <th>ID</th><th>Paciente</th><th>Cédula</th>
                    <th>Especialidad</th><th>Médico</th><th>Consultorio</th>
                    <th>Fecha</th><th>Hora</th><th>Estado</th><th>Detalle</th>
                </tr>
            </thead>

            <tbody id="tbodyHistorial">
                <!-- Renderizado inicial por PHP — reemplazado por JS en búsquedas. -->
                <?php if (empty($citas)): ?>
                    <tr>
                        <td colspan="10" class="no-results">No hay citas registradas.</td>
                    </tr>
                <?php else: ?>

                    <?php foreach ($citas as $c):
                        // "strtolower()" normaliza el estado para el nombre de clase CSS.
                        $est = strtolower($c['estado']);

                        // "in_array()" verifica si el estado existe en los valores permitidos.
                        // Si no existe (estado desconocido), usa 'agendada' como clase por defecto.
                        $clase = in_array($est, ['agendada','confirmada','cancelada','atendida'])
                                 ? $est : 'agendada';
                    ?>
                    <tr>
                        <td><strong><?= $c['id_cita'] ?></strong></td>
                        <td><?= htmlspecialchars($c['paciente']) ?></td>
                        <td><?= htmlspecialchars($c['numero_de_cedula']) ?></td>
                        <td><?= htmlspecialchars($c['nombre_especialidad']) ?></td>
                        <td><?= htmlspecialchars($c['medico']) ?></td>
                        <td><?= htmlspecialchars($c['consultorio']) ?></td>
                        <td><?= $c['fecha'] ?></td>
                        <!-- "substr(hora, 0, 5)" recorta 'HH:MM:SS' a 'HH:MM'. -->
                        <td><?= substr($c['hora'], 0, 5) ?></td>
                        <td>
                            <span class="status <?= $clase ?>"><?= $c['estado'] ?></span>
                        </td>
                        <td>
                            <!-- "verDetalle(id)" = llama la función AJAX en historial.js
                                 que consulta el controlador y abre el modal de detalle. -->
                            <button class="btn btn-primary btn-sm"
                                    onclick="verDetalle(<?= $c['id_cita'] ?>)">Ver</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<!-- Fin de .container -->


<!-- ================================================
     MODAL DE DETALLE DE CITA
     Se llena dinámicamente por JavaScript con los datos
     retornados por el controlador historialController.php.
     Muestra TODOS los campos de la cita, incluyendo
     correos, celular y dirección del consultorio.
     ================================================ -->
<div class="modal" id="detalleModal">
    <div class="modal-content">
        <h3>📋 Detalle de la Cita</h3>

        <!-- "id='detalleContenido'" = JavaScript inyecta aquí el HTML del detalle.
             Usa CSS Grid de 2 columnas para mostrar los campos en pares. -->
        <div class="detalle-grid" id="detalleContenido">
            <!-- Contenido generado por historial.js → verDetalle() -->
        </div>

        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal()">Cerrar</button>
        </div>
    </div>
</div>


<!-- Constante de URL del controlador AJAX — usada por historial.js. -->
<script>
    // "const CTRL" = URL del controlador que responde JSON.
    // Se define aquí (en PHP) en lugar de en el JS externo para que
    // la ruta sea relativa al archivo actual y no a public/js/.
    // historial.js la leerá como variable global.
    const CTRL = '../../../controllers/historialController.php';
</script>

<!-- Script cargado DESPUÉS de la constante CTRL para que historial.js
     pueda usar CTRL desde el momento que se ejecuta. -->
<script src="../../../public/js/historial.js"></script>

</body>
</html>