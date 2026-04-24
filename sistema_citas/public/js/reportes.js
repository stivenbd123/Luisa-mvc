// ============================================================
// INICIALIZACIÓN AL CARGAR EL SCRIPT
// ============================================================

// Se inicializa el campo "Fecha fin" con la fecha de hoy.
// "new Date().toISOString()" = fecha en formato ISO: "2024-03-15T10:30:00.000Z".
// ".split('T')[0]" = toma solo la parte de la fecha: "2024-03-15".
// Este formato YYYY-MM-DD es el que acepta input type="date".
document.getElementById('fechaFin').value = new Date().toISOString().split('T')[0];

// Se agrega el listener de "Enter en cualquier input del filtro".
// "document.querySelectorAll('.filter-grid input')" = selecciona TODOS los inputs
// dentro de .filter-grid (los 4 campos: nombre, cédula, fecha inicio, fecha fin).
// ".forEach(inp => ...)" = agrega el listener a cada uno individualmente.
document.querySelectorAll('.filter-grid input').forEach(inp => {
    inp.addEventListener('keydown', e => {
        if (e.key === 'Enter') buscar();
    });
});


// ============================================================
// VARIABLE DE ESTADO
// Almacena los datos de la cita actualmente mostrada en el reporte.
// Necesaria para exportarPDF() y exportarExcel() que la usan después.
// "let" en lugar de "const" porque su valor cambia cada vez que
// el usuario ve un reporte diferente.
// ============================================================

let citaActual = null;


// ============================================================
// FUNCIÓN: buscar()
// Construye la URL con los filtros activos y hace una petición AJAX.
// Solo incluye los parámetros que el usuario llenó (filtros opcionales).
// ============================================================

function buscar() {

    // Se leen y limpian los 4 valores de filtro.
    const paciente    = document.getElementById('inputPaciente').value.trim();
    const cedula      = document.getElementById('inputCedula').value.trim();
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin    = document.getElementById('fechaFin').value;

    const tbody = document.getElementById('tbodyBusqueda');

    // Se muestra el estado de "cargando" mientras espera la respuesta.
    tbody.innerHTML = `<tr><td colspan="10"><div class="state-box"><div class="icon">⏳</div>Buscando...</div></td></tr>`;

    // Se oculta el reporte anterior si había uno visible.
    document.getElementById('seccionReporte').style.display = 'none';

    // --- CONSTRUCCIÓN DE PARÁMETROS DE URL ---
    // "new URLSearchParams({ accion: 'buscar' })" = crea un objeto de parámetros URL.
    // Es la forma moderna y segura de construir query strings.
    // Alternativa manual: "?accion=buscar&paciente=..." — más propenso a errores.
    const params = new URLSearchParams({ accion: 'buscar' });

    // Se agregan solo los parámetros con valor — el controlador ignora los ausentes.
    // "params.append('clave', valor)" = agrega un parámetro al objeto.
    if (paciente)    params.append('paciente',     paciente);
    if (cedula)      params.append('cedula',       cedula);
    if (fechaInicio) params.append('fecha_inicio', fechaInicio);
    if (fechaFin)    params.append('fecha_fin',    fechaFin);

    // "fetch(`${CTRL}?${params}`)" = convierte URLSearchParams a string automáticamente.
    // Resultado: "...reporteController.php?accion=buscar&paciente=juan&fecha_fin=2024-03-15"
    fetch(`${CTRL}?${params}`)
        .then(r => r.json())
        .then(res => {
            if (!res.ok) { alert('Error al buscar.'); return; }
            renderTabla(res.data);
        })
        .catch(() => {
            tbody.innerHTML = `<tr><td colspan="10"><div class="state-box">❌ Error de conexión.</div></td></tr>`;
        });
}


// ============================================================
// FUNCIÓN: limpiar()
// Vacía todos los filtros y restaura el estado inicial de la tabla.
// ============================================================

function limpiar() {
    document.getElementById('inputPaciente').value = '';
    document.getElementById('inputCedula').value   = '';
    document.getElementById('fechaInicio').value   = '';

    // Se restaura la fecha fin a hoy (mismo valor inicial).
    document.getElementById('fechaFin').value = new Date().toISOString().split('T')[0];

    // Se restaura el mensaje de instrucción inicial en el tbody.
    document.getElementById('tbodyBusqueda').innerHTML =
        `<tr><td colspan="10"><div class="state-box"><div class="icon">🔎</div>Use los filtros para buscar la cita y luego presione <strong>Ver Reporte</strong>.</div></td></tr>`;

    // Se oculta el reporte.
    document.getElementById('seccionReporte').style.display = 'none';
}


// ============================================================
// FUNCIÓN: renderTabla(citas)
// Genera las filas HTML de los resultados de búsqueda.
// ============================================================

// Mapa de estado → clase CSS para los badges de color.
const claseEstado = {
    agendada:   'agendada',
    confirmada: 'confirmada',
    cancelada:  'cancelada',
    atendida:   'atendida'
};

function renderTabla(citas) {
    const tbody = document.getElementById('tbodyBusqueda');

    if (!citas || !citas.length) {
        tbody.innerHTML = `<tr><td colspan="10"><div class="state-box"><div class="icon">🔎</div>No se encontraron citas con esos criterios.</div></td></tr>`;
        return;
    }

    tbody.innerHTML = citas.map(c => {
        const est   = c.estado.toLowerCase();
        const clase = claseEstado[est] ?? 'agendada';

        return `
        <tr id="fila-${c.id_cita}">
            <td><strong>${c.id_cita}</strong></td>
            <td style="text-align:left;">${esc(c.paciente)}</td>
            <td>${esc(c.numero_de_cedula)}</td>
            <td>${esc(c.nombre_especialidad)}</td>
            <td>${esc(c.medico)}</td>
            <td>${esc(c.consultorio)}</td>
            <td>${formatFecha(c.fecha)}</td>
            <td>${c.hora ? c.hora.substring(0,5) : ''}</td>
            <td><span class="badge ${clase}">${c.estado}</span></td>
            <td>
                <button class="btn btn-select"
                        onclick="verReporte(${c.id_cita})">
                    📋 Ver Reporte
                </button>
            </td>
        </tr>`;
    }).join('');
}


// ============================================================
// FUNCIÓN: verReporte(id)
// Obtiene el detalle completo de una cita y construye el reporte.
// Parámetro: id (number) = ID de la cita a mostrar.
// ============================================================

function verReporte(id) {

    // Se resalta la fila seleccionada en la tabla.
    // Primero se remueve "seleccionada" de todas las filas...
    document.querySelectorAll('#tbodyBusqueda tr')
            .forEach(tr => tr.classList.remove('seleccionada'));

    // ...luego se agrega solo a la fila del ID elegido.
    // "id='fila-${id}'" = cada fila tiene un id único generado en renderTabla().
    const fila = document.getElementById(`fila-${id}`);
    if (fila) fila.classList.add('seleccionada');

    // Petición AJAX al controlador para obtener los datos completos de la cita.
    fetch(`${CTRL}?accion=detalle&id=${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.ok) { alert('Error: ' + res.msg); return; }

            // Se llena el reporte con los datos.
            poblarReporte(res.data);

            // Se muestra la sección del reporte.
            const sec = document.getElementById('seccionReporte');
            sec.style.display = 'block';

            // "sec.scrollIntoView({ behavior: 'smooth', block: 'start' })" =
            // hace scroll suave hacia la sección del reporte.
            // "behavior: 'smooth'" = animación de desplazamiento.
            // "block: 'start'" = alinea la sección al inicio del viewport.
            sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(() => alert('Error de conexión al cargar el reporte.'));
}


// ============================================================
// FUNCIÓN: poblarReporte(c)
// Llena todos los campos del reporte con los datos de la cita.
// Parámetro: c (object) = datos completos de la cita (del controlador).
// ============================================================

function poblarReporte(c) {

    // Se guarda la cita en la variable de estado global.
    // exportarPDF() y exportarExcel() la usarán después.
    citaActual = c;

    // Construcción del nombre completo del paciente.
    // ".filter(Boolean)" elimina campos vacíos o null.
    const nombreCompleto = [c.primer_nombre, c.segundo_nombre,
                            c.primer_apellido, c.segundo_apellido]
                           .filter(Boolean).join(' ');

    // Conversión de formatos para mostrar al usuario.
    const hora12 = formatHora(c.hora);     // "14:20:00" → "2:20 PM"
    const fecha  = formatFecha(c.fecha);   // "2024-03-15" → "15/03/2024"
    const est    = c.estado.toLowerCase();

    // Emojis por estado para el badge grande del reporte.
    const iconEst = { agendada:'🕐', confirmada:'✅', cancelada:'❌', atendida:'🩺' };

    // Se llena cada campo del reporte usando textContent (sin HTML).
    // "textContent" es más seguro que innerHTML para datos del usuario.
    document.getElementById('repFechaGen').textContent =
        'Generado: ' + new Date().toLocaleString('es-CO');
    document.getElementById('repIdCita').textContent         = '#' + c.id_cita;
    document.getElementById('repNombrePaciente').textContent = nombreCompleto;
    document.getElementById('repCedula').textContent         = c.numero_de_cedula  || '—';
    document.getElementById('repCelular').textContent        = c.numero_de_celular || '—';
    document.getElementById('repCorreoPaciente').textContent = c.correo_paciente   || '—';
    document.getElementById('repEspecialidad').textContent   = c.nombre_especialidad;
    document.getElementById('repMedico').textContent         = 'Dr./Dra. ' + c.medico;
    document.getElementById('repCorreoMedico').textContent   = c.correo_medico     || '—';
    document.getElementById('repConsultorio').textContent    = c.consultorio;
    document.getElementById('repDireccion').textContent      = c.dir_consultorio   || '—';
    document.getElementById('repFecha').textContent          = fecha;
    document.getElementById('repHora').textContent           = hora12;
    document.getElementById('repCreatedAt').textContent      = c.created_at        || '—';

    // Se configura el badge de estado con emoji, texto y clase CSS de color.
    const badge        = document.getElementById('repEstadoBadge');
    badge.textContent  = (iconEst[est] ?? '📋') + ' ' + c.estado;
    badge.className    = 'estado-grande ' + (est in iconEst ? est : 'agendada');
    // "est in iconEst" = verifica si la clave "est" existe en el objeto iconEst.
    // Si el estado es desconocido, usa la clase 'agendada' como fallback.
}


// ============================================================
// FUNCIÓN: volverBusqueda()
// Oculta el reporte y hace scroll de vuelta a la tabla.
// ============================================================

function volverBusqueda() {
    document.getElementById('seccionReporte').style.display = 'none';
    document.getElementById('seccionTabla').scrollIntoView({ behavior: 'smooth' });
}


// ============================================================
// FUNCIÓN: exportarPDF()
// Genera un archivo PDF con los datos de la cita usando jsPDF.
// jsPDF dibuja el PDF píxel a píxel con comandos de dibujo.
// NO usa el HTML de la página — construye el PDF desde cero.
// ============================================================

function exportarPDF() {
    if (!citaActual) return;
    const c = citaActual;

    // --- INICIALIZACIÓN DE JSPDF ---
    // "window.jspdf" = el objeto global expuesto por jsPDF UMD.
    // "{ jsPDF }" = desestructuración — extrae la clase jsPDF del objeto.
    const { jsPDF } = window.jspdf;

    // Se crea una nueva instancia del documento PDF.
    // "orientation: 'portrait'" = orientación vertical (A4 estándar).
    // "unit: 'mm'" = unidades en milímetros (página A4 = 210×297mm).
    // "format: 'a4'" = tamaño de hoja A4.
    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

    const nombreCompleto = [c.primer_nombre, c.segundo_nombre,
                            c.primer_apellido, c.segundo_apellido]
                           .filter(Boolean).join(' ');
    const hora12 = formatHora(c.hora);
    const fecha  = formatFecha(c.fecha);

    // --- ENCABEZADO ROJO ---
    // "setFillColor(r, g, b)" = color de relleno en RGB.
    // 220,53,69 = el rojo #dc3545 del sistema.
    doc.setFillColor(220, 53, 69);

    // "rect(x, y, width, height, 'F')" = rectángulo relleno.
    // 'F' = "fill" (relleno), 'S' = "stroke" (solo borde), 'FD' = ambos.
    // Coordenadas (0,0) = esquina superior izquierda de la página.
    doc.rect(0, 0, 210, 30, 'F');

    // "setTextColor(r, g, b)" = color del texto siguiente.
    doc.setTextColor(255, 255, 255);  // Blanco para el encabezado.
    doc.setFontSize(15);
    doc.setFont('helvetica', 'bold');

    // "text(texto, x, y, { align })" = escribe texto en las coordenadas dadas.
    // "align: 'center'" con x=105 = centra el texto horizontalmente (A4 = 210mm, mitad=105).
    doc.text('REPORTE DE CITA MÉDICA', 105, 13, { align: 'center' });
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text('Sistema de Gestión de Citas Médicas', 105, 20, { align: 'center' });
    doc.text('Generado: ' + new Date().toLocaleString('es-CO'), 105, 26, { align: 'center' });

    // N° de cita y estado.
    doc.setTextColor(220, 53, 69);
    doc.setFontSize(11);
    doc.setFont('helvetica', 'bold');
    doc.text(`N° de Cita: #${c.id_cita}`, 14, 40);
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(80);
    doc.text(`Estado: ${c.estado}`, 14, 48);

    // --- FUNCIONES INTERNAS DE SECCIÓN Y CAMPO ---
    // Se usan "let y" para rastrear la posición vertical actual en la página.
    let y = 58;

    // "seccion(titulo)" = dibuja una barra de sección coloreada con título.
    const seccion = (titulo) => {
        doc.setFillColor(248, 215, 218);  // Rosa muy claro (#f8d7da).
        doc.rect(14, y, 182, 7, 'F');     // Barra de fondo.
        doc.setTextColor(180, 30, 45);
        doc.setFontSize(9);
        doc.setFont('helvetica', 'bold');
        doc.text(titulo.toUpperCase(), 17, y + 5);
        y += 12;  // Avanza la posición vertical.
    };

    // "campo(label, valor)" = escribe un par etiqueta-valor en el PDF.
    const campo = (label, valor) => {
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(8);
        doc.setTextColor(150);  // Gris para la etiqueta.
        doc.text(label + ':', 17, y);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(50);   // Oscuro para el valor.
        // "String(valor || '—')" = convierte a string y usa '—' si es null/vacío.
        doc.text(String(valor || '—'), 60, y);
        y += 7;  // Avanza para el siguiente campo.
    };

    // --- SECCIONES DEL DOCUMENTO ---
    seccion('Datos del Paciente');
    campo('Nombre completo', nombreCompleto);
    campo('Cédula',          c.numero_de_cedula);
    campo('Celular',         c.numero_de_celular);
    campo('Correo',          c.correo_paciente);

    y += 4;  // Espacio entre secciones.
    seccion('Datos de la Atención');
    campo('Especialidad',    c.nombre_especialidad);
    campo('Médico tratante', 'Dr./Dra. ' + c.medico);
    campo('Correo médico',   c.correo_medico);
    campo('Consultorio',     c.consultorio);
    campo('Dirección',       c.dir_consultorio);

    y += 4;
    seccion('Fecha y Hora');
    campo('Fecha',            fecha);
    campo('Hora',             hora12);
    campo('Fecha de registro', c.created_at);

    // --- PIE DE PÁGINA ---
    doc.setFillColor(248, 249, 250);  // Gris muy claro.
    doc.rect(0, 272, 210, 25, 'F');
    doc.setDrawColor(233, 236, 239);  // "setDrawColor" = color de líneas.
    doc.line(0, 272, 210, 272);       // "line(x1,y1,x2,y2)" = línea horizontal.
    doc.setFontSize(7);
    doc.setTextColor(180);
    doc.text('Documento generado automáticamente. No requiere firma.', 105, 282, { align: 'center' });

    // "doc.save(nombre.pdf)" = descarga el PDF en el navegador del usuario.
    // No se envía al servidor — todo se generó en el cliente.
    doc.save(`cita_${c.id_cita}_${fechaArchivo()}.pdf`);
}


// ============================================================
// FUNCIÓN: exportarExcel()
// Genera un archivo Excel (.xlsx) usando SheetJS.
// Construye el contenido como un arreglo de arreglos (filas y columnas).
// ============================================================

function exportarExcel() {
    if (!citaActual) return;
    const c = citaActual;

    const nombreCompleto = [c.primer_nombre, c.segundo_nombre,
                            c.primer_apellido, c.segundo_apellido]
                           .filter(Boolean).join(' ');

    // --- DATOS DEL EXCEL COMO ARREGLO DE ARREGLOS ---
    // Cada elemento del arreglo externo es una FILA.
    // Cada elemento del arreglo interno es una CELDA de esa fila.
    // Un arreglo interno vacío [] = fila vacía (espacio en blanco).
    const filas = [
        ['REPORTE DE CITA MÉDICA'],                          // Fila 1: título
        ['Sistema de Gestión de Citas Médicas'],             // Fila 2: subtítulo
        ['Generado:', new Date().toLocaleString('es-CO')],   // Fila 3: fecha generación
        [],                                                   // Fila vacía
        ['N° DE CITA',       '#' + c.id_cita],
        ['ESTADO',           c.estado],
        [],
        ['— DATOS DEL PACIENTE —'],
        ['Nombre completo',  nombreCompleto],
        ['Cédula',           c.numero_de_cedula  || '—'],
        ['Celular',          c.numero_de_celular || '—'],
        ['Correo',           c.correo_paciente   || '—'],
        [],
        ['— DATOS DE LA ATENCIÓN —'],
        ['Especialidad',     c.nombre_especialidad],
        ['Médico tratante',  'Dr./Dra. ' + c.medico],
        ['Correo médico',    c.correo_medico      || '—'],
        ['Consultorio',      c.consultorio],
        ['Dirección',        c.dir_consultorio    || '—'],
        [],
        ['— FECHA Y HORA —'],
        ['Fecha',            formatFecha(c.fecha)],
        ['Hora',             formatHora(c.hora)],
        ['Fecha de registro', c.created_at        || '—'],
    ];

    // --- CREACIÓN DEL LIBRO EXCEL ---
    // "XLSX.utils.book_new()" = crea un nuevo libro Excel vacío (como un archivo .xlsx nuevo).
    const wb = XLSX.utils.book_new();

    // "XLSX.utils.aoa_to_sheet(filas)" = convierte "Array Of Arrays" a una hoja de cálculo.
    // "aoa" = Array Of Arrays — el formato de datos que usamos arriba.
    const ws = XLSX.utils.aoa_to_sheet(filas);

    // "ws['!cols']" = configuración de anchos de columna.
    // "wch" = "width in characters" — ancho en caracteres.
    ws['!cols'] = [{ wch: 22 }, { wch: 36 }];  // Columna A = 22 chars, B = 36 chars.

    // "XLSX.utils.book_append_sheet(wb, ws, nombre)" = añade la hoja al libro.
    // El nombre de la hoja aparece en la pestaña inferior de Excel.
    XLSX.utils.book_append_sheet(wb, ws, `Cita #${c.id_cita}`);

    // "XLSX.writeFile(wb, nombre.xlsx)" = genera y descarga el archivo Excel.
    XLSX.writeFile(wb, `cita_${c.id_cita}_${fechaArchivo()}.xlsx`);
}


// ============================================================
// UTILIDADES
// ============================================================

// Escape XSS — mismo patrón que historial.js.
// "flag g" en las regex = reemplaza TODAS las ocurrencias, no solo la primera.
function esc(s) {
    if (!s) return '—';
    return String(s)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;');
}

// Formatea fecha de YYYY-MM-DD a DD/MM/YYYY.
// "[y, m, d] = f.split('-')" = desestructuración del arreglo de partes.
function formatFecha(f) {
    if (!f) return '—';
    const [y, m, d] = f.split('-');
    return `${d}/${m}/${y}`;
}

// Convierte hora de 24h a 12h con AM/PM.
// "hora % 12 || 12" = si hora % 12 es 0 (las 12 o las 0), usa 12.
// Ejemplo: 0%12=0 → usa 12 (medianoche); 12%12=0 → usa 12 (mediodía).
function formatHora(h) {
    if (!h) return '—';
    const [hh, mm] = h.split(':');
    const hora = parseInt(hh);
    const ampm = hora >= 12 ? 'PM' : 'AM';
    const h12  = hora % 12 || 12;
    return `${h12}:${mm} ${ampm}`;
}

// Genera una cadena de fecha para el nombre del archivo descargado.
// "new Date().toISOString().slice(0, 10)" = "2024-03-15" (fecha de hoy).
// Se usa en: "cita_42_2024-03-15.pdf" y "cita_42_2024-03-15.xlsx".
function fechaArchivo() {
    return new Date().toISOString().slice(0, 10);
}