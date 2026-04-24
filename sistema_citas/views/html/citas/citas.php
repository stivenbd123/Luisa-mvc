<?php

session_start();

// Se cargan todos los modelos necesarios para esta vista.
require_once "../../../models/Cita.php";
require_once "../../../models/Medico.php";
require_once "../../../models/Especialidad.php";
require_once "../../../models/Paciente.php";
require_once "../../../config/database.php";

// Se instancian todos los modelos.
$citaM         = new Cita();
$medicoM       = new Medico();
$especialidadM = new Especialidad();
$pacienteM     = new Paciente();

// Se obtienen los datos para la tabla y para los <select> del modal.
$citas          = $citaM->obtenerTodas();
$medicos        = $medicoM->obtenerTodos();
$especialidades = $especialidadM->obtenerTodas();
$pacientes      = $pacienteM->obtenerPacientes();

// --- CONSULTORIOS: query directa sin modelo dedicado ---
$db           = new Database();
$conn         = $db->conectar();
$consultorios = $conn->query("SELECT * FROM consultorios ORDER BY nombre ASC")
                      ->fetchAll(PDO::FETCH_ASSOC);

// --- HORAS OCUPADAS POR MÉDICO Y FECHA ---
// Arreglo indexado por 'id_medico_fecha' → array de horas en formato HH:MM.
// JavaScript lo usa para marcar slots como ocupados en el selector visual.
$horasOcupadas = [];

// Solo se consideran citas NO canceladas — una cancelación libera el horario.
$stmtOcupadas = $conn->query(
    "SELECT id_medico, fecha, hora, estado FROM citas WHERE estado NOT IN ('Cancelada','cancelada')"
);

if ($stmtOcupadas) {
    foreach ($stmtOcupadas->fetchAll(PDO::FETCH_ASSOC) as $row) {

        // Clave única: 'id_medico_fecha' — ej: '5_2024-03-15'
        $key = $row['id_medico'] . '_' . $row['fecha'];

        // "substr($row['hora'], 0, 5)" = recorta TIME de MySQL de 'HH:MM:SS' a 'HH:MM'.
        // Garantiza coincidencia con el formato que genera JavaScript (sin segundos).
        $horasOcupadas[$key][] = substr($row['hora'], 0, 5);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Citas | Sistema de Gestión de Citas Médicas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/css/styleCRUDcitas.css">
</head>
<body>

<header>
    Sistema de Gestión de Citas Médicas - Citas
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>

<div class="container">

    <div class="top-bar">
        <h2>Agendamiento de Citas</h2>
        <button class="btn btn-primary" onclick="openModal()">+ Nueva Cita</button>
    </div>

    <!-- ================================================
         TABLA DE CITAS
         ================================================ -->
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Paciente</th><th>Especialidad</th><th>Médico</th>
                <th>Consultorio</th><th>Fecha</th><th>Hora</th>
                <th>Estado</th><th>Cambiar Estado</th><th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($citas as $c): ?>
            <tr>
                <td><strong><?= $c['id_cita'] ?></strong></td>

                <!-- "htmlspecialchars()" = previene XSS convirtiendo caracteres
                     peligrosos como < > & en entidades HTML seguras (&lt; &gt; &amp;).
                     Aplicado a todos los datos que vienen de la base de datos. -->
                <td><?= htmlspecialchars($c['paciente']) ?></td>
                <td><?= htmlspecialchars($c['nombre_especialidad']) ?></td>
                <td><?= htmlspecialchars($c['medico']) ?></td>
                <td><?= htmlspecialchars($c['consultorio']) ?></td>
                <td><?= $c['fecha'] ?></td>

                <!-- "substr(hora, 0, 5)" = recorta 'HH:MM:SS' a 'HH:MM' para la vista. -->
                <td><?= substr($c['hora'], 0, 5) ?></td>

                <!-- BADGE DE ESTADO CON COLOR DINÁMICO -->
                <td>
                    <?php
                    // "strtolower()" = normaliza el estado a minúsculas para buscar en el arreglo.
                    $estado = strtolower($c['estado']);
                    $clases = ['agendada'=>'agendada','confirmada'=>'confirmada',
                               'cancelada'=>'cancelada','atendida'=>'atendida'];
                    // "?? 'agendada'" = clase por defecto si el estado no existe en el arreglo.
                    $clase  = $clases[$estado] ?? 'agendada';
                    ?>
                    <!-- "status {$clase}" = la clase base define el badge,
                         la clase dinámica define el color según el estado. -->
                    <span class="status <?= $clase ?>"><?= $c['estado'] ?></span>
                </td>

                <!-- SELECT DE CAMBIO DE ESTADO EN LA TABLA
                     "onchange='cambiarEstado(id, this.value)'" = al cambiar
                     el select, JavaScript redirige sin necesidad de botón aparte.
                     "selected" marca la opción que coincide con el estado actual. -->
                <td>
                    <select class="estado-select"
                            onchange="cambiarEstado(<?= $c['id_cita'] ?>, this.value)">
                        <?php foreach(['Agendada','Confirmada','Cancelada','Atendida'] as $est): ?>
                            <option value="<?= $est ?>"
                                <?= $c['estado'] == $est ? 'selected' : '' ?>>
                                <?= $est ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>

                <td>
                    <a href="../../../controllers/citaController.php?accion=eliminar_cita&id=<?= $c['id_cita'] ?>"
                       class="btn btn-danger"
                       onclick="return confirm('¿Eliminar esta cita? Esta acción no se puede deshacer.')">
                        Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>

            <!-- Fila vacía cuando no hay citas. "colspan='10'" abarca las 10 columnas. -->
            <?php if(empty($citas)): ?>
            <tr>
                <td colspan="10" style="color:#999; padding:20px;">No hay citas registradas.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<!-- ================================================
     MODAL: AGENDAR NUEVA CITA
     Cascada de selección:
     1. Especialidad → filtra médicos.
     2. Médico + Fecha → muestra slots disponibles.
     3. Clic en slot → habilita "Guardar".
     ================================================ -->
<div class="modal" id="citaModal">
    <div class="modal-content">
        <h3>Agendar Nueva Cita</h3>

        <form action="../../../controllers/citaController.php" method="POST" id="formCita">
            <input type="hidden" name="accion" value="crear_cita">

            <!-- "id='horaSeleccionada'" = JavaScript llena este campo oculto
                 cuando el usuario hace clic en un botón de slot. -->
            <input type="hidden" name="hora" id="horaSeleccionada">

            <!-- PACIENTE -->
            <div class="form-group">
                <label>Paciente</label>
                <select name="id_paciente" required>
                    <option value="">Seleccione Paciente</option>
                    <?php foreach($pacientes as $p): ?>
                        <option value="<?= $p['id_paciente'] ?>">
                            <?= htmlspecialchars($p['primer_nombre'].' '.$p['primer_apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ESPECIALIDAD (solo filtro — sin name, no se envía al servidor) -->
            <div class="form-group">
                <label>Especialidad</label>
                <select id="filtroEspecialidad" onchange="filtrarMedicos()">
                    <option value="">Seleccione Especialidad</option>
                    <?php foreach($especialidades as $e): ?>
                        <option value="<?= $e['id_especialidad'] ?>">
                            <?= htmlspecialchars($e['nombre_especialidad']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- MÉDICO (dependiente de especialidad — se repobla con JS) -->
            <div class="form-group">
                <label>Médico</label>
                <select name="id_medico" id="selectMedico" required onchange="actualizarSlots()">
                    <option value="">— Seleccione primero una especialidad —</option>
                </select>
            </div>

            <!-- CONSULTORIO -->
            <div class="form-group">
                <label>Consultorio</label>
                <select name="id_consultorio" required>
                    <option value="">Seleccione Consultorio</option>
                    <?php foreach($consultorios as $c): ?>
                        <option value="<?= $c['id_consultorio'] ?>">
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- FECHA
                 "min=HOY" = no permite fechas pasadas.
                 "max=HOY+90días" = máximo 3 meses de anticipación.
                 PHP genera ambos valores dinámicamente. -->
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" id="inputFecha" required
                       min="<?= date('Y-m-d') ?>"
                       max="<?= date('Y-m-d', strtotime('+90 days')) ?>"
                       onchange="actualizarSlots()">
                <span class="horario-info">Solo días hábiles (lunes a viernes)</span>
            </div>

            <!-- CONTENEDOR DE SLOTS DE HORA
                 JavaScript llena este div con botones de horario disponibles. -->
            <div class="form-group">
                <label>Hora disponible</label>
                <div class="slots-container" id="slotsContainer">
                    <span class="slots-vacio">
                        Seleccione médico y fecha para ver horarios disponibles
                    </span>
                </div>
                <span class="horario-info">
                    Jornada: 7:00 am – 12:00 m | 2:00 pm – 6:00 pm · Turnos cada 20 min
                </span>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn" onclick="closeModal()">Cancelar</button>
                <!-- "disabled" = botón bloqueado hasta que el usuario seleccione un slot. -->
                <button type="submit" class="btn btn-primary" id="btnGuardar" disabled>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>


<!-- ================================================
     PUENTE DE DATOS PHP → JAVASCRIPT
     Variables globales que citas.js necesita pero que
     solo PHP puede calcular (vienen de la BD).
     Se declaran ANTES de cargar citas.js.
     ================================================ -->
<script>
// Arreglo de médicos: id, nombre y especialidad para el filtrado dinámico.
// PHP genera el JavaScript de inicialización del arreglo.
const todosMedicos = [
    <?php foreach($medicos as $m): ?>
    {
        id: <?= $m['id_medico'] ?>,
        // ENT_QUOTES escapa también comillas simples dentro del string JS.
        nombre: "<?= htmlspecialchars($m['primer_nombre'].' '.$m['primer_apellido'], ENT_QUOTES) ?>",
        especialidad: <?= $m['id_especialidad'] ?>
    },
    <?php endforeach; ?>
];

// Horas ocupadas por médico y fecha.
// Formato: { '5_2024-03-15': ['09:00', '09:20'], ... }
// citas.js lo usa para calcular qué slots están disponibles.
const horasOcupadas = <?= json_encode($horasOcupadas) ?>;
</script>

<!-- citas.js cargado DESPUÉS del bloque anterior para que las
     constantes todosMedicos y horasOcupadas ya estén definidas. -->
<script src="../../../public/js/citas.js"></script>

</body>
</html>