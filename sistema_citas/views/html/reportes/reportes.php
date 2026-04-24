<?php
// Inicia sesión para proteger el acceso a la vista.
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes | Sistema de Gestión de Citas Médicas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ================================================
         LIBRERÍAS EXTERNAS (cargadas desde CDN)
         Se cargan en <head> porque reportes.js las usa al hacer clic
         en los botones de exportar — deben estar disponibles antes.
         ================================================ -->

    <!-- jsPDF: genera PDF en el navegador sin necesidad de servidor.
         "umd.min.js" = versión UMD (Universal Module Definition) minificada.
         UMD funciona tanto en módulos como en scripts normales (window.jspdf). -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- jsPDF-AutoTable: plugin de jsPDF para crear tablas formateadas en PDF.
         Debe cargarse DESPUÉS de jsPDF para poder extender su objeto. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    <!-- SheetJS (XLSX): genera archivos Excel (.xlsx) en el navegador.
         "xlsx.full.min.js" = versión completa minificada (soporta todos los formatos). -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <link rel="stylesheet" href="../../../public/css/styleReportes.css">
</head>
<body>

<header>
    🏥 Sistema de Gestión de Citas Médicas — Reportes
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>

<div class="container">

    <!-- ================================================
         PANEL DE FILTROS DE BÚSQUEDA
         4 filtros combinables: nombre, cédula, fecha inicio, fecha fin.
         El controlador reporteController.php aplica solo los que lleguen.
         ================================================ -->
    <div class="filter-card">
        <h3>🔍 Buscar cita para generar reporte</h3>

        <!-- ".filter-grid" = CSS Grid con columnas automáticas (auto-fill).
             Los 4 campos se distribuyen en columnas según el ancho disponible. -->
        <div class="filter-grid">
            <div>
                <!-- "label" pequeño en mayúsculas + input — estilo etiqueta de campo. -->
                <label>Nombre del paciente</label>
                <input type="text" id="inputPaciente" placeholder="Ej: Juan Pérez">
            </div>
            <div>
                <label>Cédula del paciente</label>
                <input type="text" id="inputCedula" placeholder="Ej: 1234567890">
            </div>
            <div>
                <label>Fecha inicio</label>
                <!-- "type='date'" = input nativo de fecha con selector de calendario. -->
                <input type="date" id="fechaInicio">
            </div>
            <div>
                <label>Fecha fin</label>
                <!-- JavaScript inicializa este campo con la fecha de hoy al cargar. -->
                <input type="date" id="fechaFin">
            </div>
        </div>

        <div class="filter-actions">
            <button class="btn btn-primary" onclick="buscar()">🔍 Buscar</button>
            <button class="btn btn-ghost"   onclick="limpiar()">✕ Limpiar</button>
        </div>
    </div>


    <!-- ================================================
         TABLA DE RESULTADOS DE BÚSQUEDA
         "id='seccionTabla'" = referenciada para hacer scroll al volver.
         El tbody se actualiza dinámicamente con JavaScript.
         ================================================ -->
    <div class="table-wrapper" id="seccionTabla">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Paciente</th><th>Cédula</th>
                    <th>Especialidad</th><th>Médico</th><th>Consultorio</th>
                    <th>Fecha</th><th>Hora</th><th>Estado</th><th>Reporte</th>
                </tr>
            </thead>
            <!-- "id='tbodyBusqueda'" = JavaScript reemplaza este contenido con los resultados. -->
            <tbody id="tbodyBusqueda">
                <!-- Estado inicial: mensaje de instrucción. -->
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


    <!-- ================================================
         SECCIÓN DEL REPORTE DE UNA CITA
         "id='seccionReporte'" = oculto por defecto (display:none en CSS).
         JavaScript lo muestra cuando el usuario hace clic en "Ver Reporte".
         Se rellena con los datos de la cita seleccionada.
         ================================================ -->
    <div id="seccionReporte">

        <!-- "id='reporteContenido'" = contenedor del reporte completo.
             jsPDF usa el DOM de esta sección para generar el PDF. -->
        <div class="reporte-card" id="reporteContenido">

            <!-- ENCABEZADO DEL REPORTE -->
            <div class="rep-header">
                <div class="rep-titulo">
                    <h2>🏥 Reporte de Cita Médica</h2>
                    <!-- "id='repFechaGen'" = JavaScript inserta aquí la fecha y hora actual. -->
                    <p id="repFechaGen"></p>
                </div>
                <div class="rep-id">
                    <!-- "id='repIdCita'" = JavaScript inserta el número de cita (ej: "#42"). -->
                    <div class="num" id="repIdCita">—</div>
                    <div class="lbl">N° de Cita</div>
                </div>
            </div>

            <div class="rep-body">

                <!-- ESTADO DE LA CITA -->
                <div class="rep-section">
                    <div class="rep-section-title">Estado de la cita</div>
                    <!-- "id='repEstadoBadge'" = JavaScript asigna texto, emoji y clase CSS
                         según el estado de la cita (agendada, confirmada, etc.). -->
                    <span class="estado-grande" id="repEstadoBadge"></span>
                </div>

                <!-- DATOS DEL PACIENTE -->
                <div class="rep-section">
                    <div class="rep-section-title">👤 Datos del Paciente</div>
                    <div class="rep-grid">
                        <div class="rep-field">
                            <label>Nombre completo</label>
                            <!-- Cada "id='rep...'" es llenado por reportes.js → poblarReporte(). -->
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

                <!-- DATOS DE LA ATENCIÓN MÉDICA -->
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

                <!-- FECHA Y HORA DE LA CITA -->
                <div class="rep-section">
                    <div class="rep-section-title">📅 Fecha y Hora</div>
                    <div class="rep-grid">
                        <!-- "rep-field highlight" = clase que muestra fecha/hora en rojo y más grande. -->
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

            </div><!-- Fin de .rep-body -->

            <!-- PIE DEL REPORTE CON BOTONES DE EXPORTACIÓN -->
            <div class="rep-footer">
                <p>Documento generado automáticamente por el Sistema de Gestión de Citas Médicas.</p>
                <div class="rep-export-btns">
                    <!-- "volverBusqueda()" = oculta el reporte y vuelve a la tabla. -->
                    <button class="btn btn-back" id="btnVolver" onclick="volverBusqueda()">
                        ← Volver
                    </button>
                    <!-- "exportarPDF()" = genera y descarga el PDF usando jsPDF. -->
                    <button class="btn btn-pdf"   onclick="exportarPDF()">📄 PDF</button>
                    <!-- "exportarExcel()" = genera y descarga el Excel usando SheetJS. -->
                    <button class="btn btn-excel" onclick="exportarExcel()">📊 Excel</button>
                    <!-- "window.print()" = abre el diálogo de impresión del navegador.
                         El CSS @media print oculta todo excepto el reporte. -->
                    <button class="btn btn-print" onclick="window.print()">🖨 Imprimir</button>
                </div>
            </div>

        </div><!-- Fin de .reporte-card -->
    </div><!-- Fin de #seccionReporte -->

</div><!-- Fin de .container -->


<!-- Constante del controlador para reportes.js. -->
<script>
    // URL del controlador AJAX — definida aquí para que la ruta sea
    // relativa a este archivo PHP, no a public/js/reportes.js.
    const CTRL = '../../../controllers/reporteController.php';
</script>

<!-- reportes.js cargado DESPUÉS de CTRL y de las librerías del <head>. -->
<script src="../../../public/js/reportes.js"></script>

</body>
</html>