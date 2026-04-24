<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes | Sistema de Gestión de Citas Médicas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <link rel="stylesheet" href="../../../public/css/styleReportes.css">
</head>
<body>

<header>
    🏥 Sistema de Gestión de Citas Médicas — Reportes
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>

<div class="container">

    <div class="filter-card">
        <h3>🔍 Buscar cita para generar reporte</h3>
        <div class="filter-grid">
            <div>
                <label>Nombre del paciente</label>
                <input type="text" id="inputPaciente" placeholder="Ej: Juan Pérez">
            </div>
            <div>
                <label>Cédula del paciente</label>
                <input type="text" id="inputCedula" placeholder="Ej: 1234567890">
            </div>
            <div>
                <label>Fecha inicio</label>
                <input type="date" id="fechaInicio">
            </div>
            <div>
                <label>Fecha fin</label>
                <!-- Sin value por defecto — así no filtra citas futuras -->
                <input type="date" id="fechaFin">
            </div>
        </div>
        <div class="filter-actions">
            <button class="btn btn-primary" onclick="buscar()">🔍 Buscar</button>
            <button class="btn btn-ghost"   onclick="limpiar()">✕ Limpiar</button>
        </div>
    </div>

    <div class="table-wrapper" id="seccionTabla">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Paciente</th><th>Cédula</th>
                    <th>Especialidad</th><th>Médico</th><th>Consultorio</th>
                    <th>Fecha</th><th>Hora</th><th>Estado</th><th>Reporte</th>
                </tr>
            </thead>
            <tbody id="tbodyBusqueda">
                <tr>
                    <td colspan="10">
                        <div class="state-box">
                            <div class="icon">🔎</div>
                            Use los filtros para buscar la cita y luego presione
                            <strong>Ver Reporte</strong>.
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="seccionReporte">
        <div class="reporte-card" id="reporteContenido">

            <div class="rep-header">
                <div class="rep-titulo">
                    <h2>🏥 Reporte de Cita Médica</h2>
                    <p id="repFechaGen"></p>
                </div>
                <div class="rep-id">
                    <div class="num" id="repIdCita">—</div>
                    <div class="lbl">N° de Cita</div>
                </div>
            </div>

            <div class="rep-body">

                <div class="rep-section">
                    <div class="rep-section-title">Estado de la cita</div>
                    <span class="estado-grande" id="repEstadoBadge"></span>
                </div>

                <div class="rep-section">
                    <div class="rep-section-title">👤 Datos del Paciente</div>
                    <div class="rep-grid">
                        <div class="rep-field">
                            <label>Nombre completo</label>
                            <span id="repNombrePaciente">—</span>
                        </div>
                        <div class="rep-field">
                            <label>Cédula</label>
                            <span id="repCedula">—</span>
                        </div>
                        <div class="rep-field">
                            <label>Celular</label>
                            <span id="repCelular">—</span>
                        </div>
                        <div class="rep-field">
                            <label>Correo electrónico</label>
                            <span id="repCorreoPaciente">—</span>
                        </div>
                    </div>
                </div>

                <div class="rep-section">
                    <div class="rep-section-title">🩺 Datos de la Atención</div>
                    <div class="rep-grid">
                        <div class="rep-field">
                            <label>Especialidad</label>
                            <span id="repEspecialidad">—</span>
                        </div>
                        <div class="rep-field">
                            <label>Médico tratante</label>
                            <span id="repMedico">—</span>
                        </div>
                        <div class="rep-field">
                            <label>Correo del médico</label>
                            <span id="repCorreoMedico">—</span>
                        </div>
                        <div class="rep-field">
                            <label>Consultorio</label>
                            <span id="repConsultorio">—</span>
                        </div>
                        <div class="rep-field">
                            <label>Dirección</label>
                            <span id="repDireccion">—</span>
                        </div>
                    </div>
                </div>

                <div class="rep-section">
                    <div class="rep-section-title">📅 Fecha y Hora</div>
                    <div class="rep-grid">
                        <div class="rep-field highlight">
                            <label>Fecha</label>
                            <span id="repFecha">—</span>
                        </div>
                        <div class="rep-field highlight">
                            <label>Hora</label>
                            <span id="repHora">—</span>
                        </div>
                        <div class="rep-field">
                            <label>Fecha de registro</label>
                            <span id="repCreatedAt">—</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="rep-footer">
                <p>Documento generado automáticamente por el Sistema de Gestión de Citas Médicas.</p>
                <div class="rep-export-btns">
                    <button class="btn btn-back"  onclick="volverBusqueda()">← Volver</button>
                    <button class="btn btn-pdf"   onclick="exportarPDF()">📄 PDF</button>
                    <button class="btn btn-excel" onclick="exportarExcel()">📊 Excel</button>
                    <button class="btn btn-print" onclick="window.print()">🖨 Imprimir</button>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    <?php
   
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'];
    $base     = rtrim(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])))), '/');
    ?>
    const CTRL = '<?= $protocol . "://" . $host . $base ?>/controllers/reporteController.php';
</script>

<script src="../../../public/js/reportes.js"></script>

</body>
</html>