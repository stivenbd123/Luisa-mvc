<?php

session_start();

require_once "../../../models/Cita.php";
require_once "../../../models/Medico.php";
require_once "../../../models/Especialidad.php";
require_once "../../../models/Paciente.php";
require_once "../../../config/database.php";


$citaM         = new Cita();
$medicoM       = new Medico();
$especialidadM = new Especialidad();
$pacienteM     = new Paciente();


$citas          = $citaM->obtenerTodas();
$medicos        = $medicoM->obtenerTodos();
$especialidades = $especialidadM->obtenerTodas();
$pacientes      = $pacienteM->obtenerPacientes();


$db           = new Database();
$conn         = $db->conectar();
$consultorios = $conn->query("SELECT * FROM consultorios ORDER BY nombre ASC")
                      ->fetchAll(PDO::FETCH_ASSOC);


$horasOcupadas = [];


$stmtOcupadas = $conn->query(
    "SELECT id_medico, fecha, hora, estado FROM citas WHERE estado NOT IN ('Cancelada','cancelada')"
);

if ($stmtOcupadas) {
    foreach ($stmtOcupadas->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $key = $row['id_medico'] . '_' . $row['fecha'];

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

               
                <td><?= htmlspecialchars($c['paciente']) ?></td>
                <td><?= htmlspecialchars($c['nombre_especialidad']) ?></td>
                <td><?= htmlspecialchars($c['medico']) ?></td>
                <td><?= htmlspecialchars($c['consultorio']) ?></td>
                <td><?= $c['fecha'] ?></td>

               
                <td><?= substr($c['hora'], 0, 5) ?></td>

               
                <td>
                    <?php
                    
                    $estado = strtolower($c['estado']);
                    $clases = ['agendada'=>'agendada','confirmada'=>'confirmada',
                               'cancelada'=>'cancelada','atendida'=>'atendida'];
                    
                    $clase  = $clases[$estado] ?? 'agendada';
                    ?>
                    
                    <span class="status <?= $clase ?>"><?= $c['estado'] ?></span>
                </td>


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

          
            <?php if(empty($citas)): ?>
            <tr>
                <td colspan="10" style="color:#999; padding:20px;">No hay citas registradas.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>



<div class="modal" id="citaModal">
    <div class="modal-content">
        <h3>Agendar Nueva Cita</h3>

        <form action="../../../controllers/citaController.php" method="POST" id="formCita">
            <input type="hidden" name="accion" value="crear_cita">

            
            <input type="hidden" name="hora" id="horaSeleccionada">

      
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

          
            <div class="form-group">
                <label>Médico</label>
                <select name="id_medico" id="selectMedico" required onchange="actualizarSlots()">
                    <option value="">— Seleccione primero una especialidad —</option>
                </select>
            </div>

    
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

          
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" id="inputFecha" required
                       min="<?= date('Y-m-d') ?>"
                       max="<?= date('Y-m-d', strtotime('+90 days')) ?>"
                       onchange="actualizarSlots()">
                <span class="horario-info">Solo días hábiles (lunes a viernes)</span>
            </div>

            
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


<script>

const todosMedicos = [
    <?php foreach($medicos as $m): ?>
    {       
        id: <?= $m['id_medico'] ?>,
        
        nombre: "<?= htmlspecialchars($m['primer_nombre'].' '.$m['primer_apellido'], ENT_QUOTES) ?>",
        especialidad: <?= $m['id_especialidad'] ?>
    },
    <?php endforeach; ?>
];


const horasOcupadas = <?= json_encode($horasOcupadas) ?>;
</script>


<script src="../../../public/js/citas.js"></script>

</body>
</html>