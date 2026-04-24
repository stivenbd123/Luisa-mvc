<?php
session_start();


require_once "../../../config/database.php";

$db   = new Database();
$conn = $db->conectar();

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

   
    <div class="filter-box">
        <h3>🔍 Buscar historial</h3>
        <div class="filter-row">
          
            <input type="text" id="inputNombre" placeholder="Nombre del paciente">

            <input type="text" id="inputCedula" placeholder="Número de cédula">

            <button class="btn btn-primary" onclick="buscar()">Buscar</button>

            <button class="btn btn-secondary" onclick="limpiar()">Limpiar</button>
        </div>
    </div>

   
    <div class="spinner" id="spinner">⏳ Buscando...</div>

   
    <p class="result-info" id="resultInfo"></p>



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
             
                <?php if (empty($citas)): ?>
                    <tr>
                        <td colspan="10" class="no-results">No hay citas registradas.</td>
                    </tr>
                <?php else: ?>

                    <?php foreach ($citas as $c):
                     
                        $est = strtolower($c['estado']);

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
                        
                        <td><?= substr($c['hora'], 0, 5) ?></td>
                        <td>
                            <span class="status <?= $clase ?>"><?= $c['estado'] ?></span>
                        </td>
                        <td>
                           
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




<div class="modal" id="detalleModal">
    <div class="modal-content">
        <h3>📋 Detalle de la Cita</h3>

        
        <div class="detalle-grid" id="detalleContenido">
         
        </div>

        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal()">Cerrar</button>
        </div>
    </div>
</div>



<script>
 
    const CTRL = '../../../controllers/historialController.php';
</script>


<script src="../../../public/js/historial.js"></script>

</body>
</html>