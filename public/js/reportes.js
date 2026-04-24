
document.querySelectorAll('.filter-grid input').forEach(inp => {
    inp.addEventListener('keydown', e => {
        if (e.key === 'Enter') buscar();
    });
});


let citaActual = null;


function buscar() {
    const paciente    = document.getElementById('inputPaciente').value.trim();
    const cedula      = document.getElementById('inputCedula').value.trim();
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin    = document.getElementById('fechaFin').value;

    const tbody = document.getElementById('tbodyBusqueda');
    tbody.innerHTML = <tr><td colspan="10"><div class="state-box"><div class="icon">⏳</div>Buscando...</div></td></tr>;
    document.getElementById('seccionReporte').style.display = 'none';

    const params = new URLSearchParams({ accion: 'buscar' });
    if (paciente)    params.append('paciente',     paciente);
    if (cedula)      params.append('cedula',       cedula);
    if (fechaInicio) params.append('fecha_inicio', fechaInicio);
    if (fechaFin)    params.append('fecha_fin',    fechaFin);

    fetch(`${CTRL}?${params}`)
        .then(r => r.json())
        .then(res => {
            if (!res.ok) { alert('Error al buscar.'); return; }
            renderTabla(res.data);
        })
        .catch(() => {
            tbody.innerHTML = <tr><td colspan="10"><div class="state-box">❌ Error de conexión.</div></td></tr>;
        });
}



function limpiar() {
    document.getElementById('inputPaciente').value = '';
    document.getElementById('inputCedula').value   = '';
    document.getElementById('fechaInicio').value   = '';
    document.getElementById('fechaFin').value      = '';

    document.getElementById('tbodyBusqueda').innerHTML =
        <tr><td colspan="10"><div class="state-box"><div class="icon">🔎</div>Use los filtros para buscar la cita y luego presione <strong>Ver Reporte</strong>.</div></td></tr>;
    document.getElementById('seccionReporte').style.display = 'none';
}



const claseEstado = {
    agendada:   'agendada',
    confirmada: 'confirmada',
    cancelada:  'cancelada',
    atendida:   'atendida'
};

function renderTabla(citas) {
    const tbody = document.getElementById('tbodyBusqueda');

    if (!citas || !citas.length) {
        tbody.innerHTML = <tr><td colspan="10"><div class="state-box"><div class="icon">🔎</div>No se encontraron citas con esos criterios.</div></td></tr>;
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



function verReporte(id) {
    document.querySelectorAll('#tbodyBusqueda tr')
            .forEach(tr => tr.classList.remove('seleccionada'));

    const fila = document.getElementById(`fila-${id}`);
    if (fila) fila.classList.add('seleccionada');

    fetch(`${CTRL}?accion=detalle&id=${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.ok) { alert('Error: ' + res.msg); return; }
            poblarReporte(res.data);
            const sec = document.getElementById('seccionReporte');
            sec.style.display = 'block';
            sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(() => alert('Error de conexión al cargar el reporte.'));
}



function poblarReporte(c) {
    citaActual = c;

    const nombreCompleto = [c.primer_nombre, c.segundo_nombre,
                            c.primer_apellido, c.segundo_apellido]
                           .filter(Boolean).join(' ');

    const hora12 = formatHora(c.hora);
    const fecha  = formatFecha(c.fecha);
    const est    = c.estado.toLowerCase();
    const iconEst = { agendada:'🕐', confirmada:'✅', cancelada:'❌', atendida:'🩺' };

    document.getElementById('repFechaGen').textContent        = 'Generado: ' + new Date().toLocaleString('es-CO');
    document.getElementById('repIdCita').textContent          = '#' + c.id_cita;
    document.getElementById('repNombrePaciente').textContent  = nombreCompleto;
    document.getElementById('repCedula').textContent          = c.numero_de_cedula  || '—';
    document.getElementById('repCelular').textContent         = c.numero_de_celular || '—';
    document.getElementById('repCorreoPaciente').textContent  = c.correo_paciente   || '—';
    document.getElementById('repEspecialidad').textContent    = c.nombre_especialidad;
    document.getElementById('repMedico').textContent          = 'Dr./Dra. ' + c.medico;
    document.getElementById('repCorreoMedico').textContent    = c.correo_medico     || '—';
    document.getElementById('repConsultorio').textContent     = c.consultorio;
    document.getElementById('repDireccion').textContent       = c.dir_consultorio   || '—';
    document.getElementById('repFecha').textContent           = fecha;
    document.getElementById('repHora').textContent            = hora12;
    document.getElementById('repCreatedAt').textContent       = c.created_at        || '—';

    const badge       = document.getElementById('repEstadoBadge');
    badge.textContent = (iconEst[est] ?? '📋') + ' ' + c.estado;
    badge.className   = 'estado-grande ' + (est in iconEst ? est : 'agendada');
}



function volverBusqueda() {
    document.getElementById('seccionReporte').style.display = 'none';
    document.getElementById('seccionTabla').scrollIntoView({ behavior: 'smooth' });
}


function exportarPDF() {
    if (!citaActual) return;
    const c = citaActual;

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

    const nombreCompleto = [c.primer_nombre, c.segundo_nombre,
                            c.primer_apellido, c.segundo_apellido]
                           .filter(Boolean).join(' ');
    const hora12 = formatHora(c.hora);
    const fecha  = formatFecha(c.fecha);

    doc.setFillColor(220, 53, 69);
    doc.rect(0, 0, 210, 30, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(15);
    doc.setFont('helvetica', 'bold');
    doc.text('REPORTE DE CITA MÉDICA', 105, 13, { align: 'center' });
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text('Sistema de Gestión de Citas Médicas', 105, 20, { align: 'center' });
    doc.text('Generado: ' + new Date().toLocaleString('es-CO'), 105, 26, { align: 'center' });

    doc.setTextColor(220, 53, 69);
    doc.setFontSize(11);
    doc.setFont('helvetica', 'bold');
    doc.text(`N° de Cita: #${c.id_cita}`, 14, 40);
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(80);
    doc.text(`Estado: ${c.estado}`, 14, 48);

    let y = 58;

    const seccion = (titulo) => {
        doc.setFillColor(248, 215, 218);
        doc.rect(14, y, 182, 7, 'F');
        doc.setTextColor(180, 30, 45);
        doc.setFontSize(9);
        doc.setFont('helvetica', 'bold');
        doc.text(titulo.toUpperCase(), 17, y + 5);
        y += 12;
    };

    const campo = (label, valor) => {
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text(label + ':', 17, y);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(50);
        doc.text(String(valor || '—'), 60, y);
        y += 7;
    };

    seccion('Datos del Paciente');
    campo('Nombre completo', nombreCompleto);
    campo('Cédula',          c.numero_de_cedula);
    campo('Celular',         c.numero_de_celular);
    campo('Correo',          c.correo_paciente);

    y += 4;
    seccion('Datos de la Atención');
    campo('Especialidad',    c.nombre_especialidad);
    campo('Médico tratante', 'Dr./Dra. ' + c.medico);
    campo('Correo médico',   c.correo_medico);
    campo('Consultorio',     c.consultorio);
    campo('Dirección',       c.dir_consultorio);

    y += 4;
    seccion('Fecha y Hora');
    campo('Fecha',             fecha);
    campo('Hora',              hora12);
    campo('Fecha de registro', c.created_at);

    doc.setFillColor(248, 249, 250);
    doc.rect(0, 272, 210, 25, 'F');
    doc.setDrawColor(233, 236, 239);
    doc.line(0, 272, 210, 272);
    doc.setFontSize(7);
    doc.setTextColor(180);
    doc.text('Documento generado automáticamente. No requiere firma.', 105, 282, { align: 'center' });

    doc.save(`cita_${c.id_cita}_${fechaArchivo()}.pdf`);
}


function exportarExcel() {
    if (!citaActual) return;
    const c = citaActual;

    const nombreCompleto = [c.primer_nombre, c.segundo_nombre,
                            c.primer_apellido, c.segundo_apellido]
                           .filter(Boolean).join(' ');

    const filas = [
        ['REPORTE DE CITA MÉDICA'],
        ['Sistema de Gestión de Citas Médicas'],
        ['Generado:', new Date().toLocaleString('es-CO')],
        [],
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

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(filas);
    ws['!cols'] = [{ wch: 22 }, { wch: 36 }];
    XLSX.utils.book_append_sheet(wb, ws, `Cita #${c.id_cita}`);
    XLSX.writeFile(wb, `cita_${c.id_cita}_${fechaArchivo()}.xlsx`);
}



function esc(s) {
    if (!s) return '—';
    return String(s)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;');
}

function formatFecha(f) {
    if (!f) return '—';
    const [y, m, d] = f.split('-');
    return `${d}/${m}/${y}`;
}

function formatHora(h) {
    if (!h) return '—';
    const [hh, mm] = h.split(':');
    const hora = parseInt(hh);
    const ampm = hora >= 12 ? 'PM' : 'AM';
    const h12  = hora % 12 || 12;
    return `${h12}:${mm} ${ampm}`;
}

function fechaArchivo() {
    return new Date().toISOString().slice(0, 10);
}