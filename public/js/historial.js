
function buscar() {
    
    const nombre = document.getElementById('inputNombre').value.trim();
    const cedula = document.getElementById('inputCedula').value.trim();

    const q = nombre || cedula;

   
    mostrarSpinner(true);

   
    fetch(`${CTRL}?accion=buscar&q=${encodeURIComponent(q)}`)

       
        .then(r => r.json())

       
        .then(res => {

            mostrarSpinner(false);

            if (!res.ok) {
                alert('Error: ' + res.msg);
                return;
            }

           
            renderTabla(res.data);
        })

        
        .catch(() => {
            mostrarSpinner(false);
            alert('Error de conexión al buscar.');
        });
}

function limpiar() {
    document.getElementById('inputNombre').value = '';
    document.getElementById('inputCedula').value = '';

    buscar();
}


function renderTabla(citas) {

    const tbody = document.getElementById('tbodyHistorial');
    const info  = document.getElementById('resultInfo');

    
    if (!citas || citas.length === 0) {

        tbody.innerHTML = `<tr><td colspan="10" class="no-results">No se encontraron citas.</td></tr>`;

        info.textContent = '';
        return;
    }

   
    info.textContent = `${citas.length} resultado(s) encontrado(s)`;

    
    const estadoClase = {
        agendada:   'agendada',
        confirmada: 'confirmada',
        cancelada:  'cancelada',
        atendida:   'atendida'
    };

   
    tbody.innerHTML = citas.map(c => {

        const est   = c.estado.toLowerCase();

        const clase = estadoClase[est] ?? 'agendada';

        const hora  = c.hora ? c.hora.substring(0, 5) : '';

        
        return `
        <tr>
            <td><strong>${c.id_cita}</strong></td>
            <td>${esc(c.paciente)}</td>
            <td>${esc(c.numero_de_cedula)}</td>
            <td>${esc(c.nombre_especialidad)}</td>
            <td>${esc(c.medico)}</td>
            <td>${esc(c.consultorio)}</td>
            <td>${c.fecha}</td>
            <td>${hora}</td>
            <td><span class="status ${clase}">${c.estado}</span></td>
            <td>
                <button class="btn btn-primary btn-sm"
                        onclick="verDetalle(${c.id_cita})">Ver</button>
            </td>
        </tr>`;

    }).join('');
}


function verDetalle(id) {

    fetch(`${CTRL}?accion=detalle&id=${id}`)

        .then(r => r.json())

        .then(res => {

            
            if (!res.ok) {
                alert('Error: ' + res.msg);
                return;
            }

            
            const c = res.data;

            
            const nombreCompleto = [c.primer_nombre, c.segundo_nombre,
                                    c.primer_apellido, c.segundo_apellido]
                                    .filter(Boolean).join(' ');

            const hora = c.hora ? c.hora.substring(0, 5) : '';

            
            document.getElementById('detalleContenido').innerHTML = `
                <div class="detalle-item full">
                    <label>Paciente</label>
                    <span>${esc(nombreCompleto)}</span>
                </div>
                <div class="detalle-item">
                    <label>Cédula</label>
                    <span>${esc(c.numero_de_cedula)}</span>
                </div>
                <div class="detalle-item">
                    <label>Celular paciente</label>
                    <span>${esc(c.numero_de_celular)}</span>
                </div>
                <div class="detalle-item full">
                    <label>Correo paciente</label>
                    <span>${esc(c.correo_paciente)}</span>
                </div>
                <div class="detalle-item">
                    <label>Especialidad</label>
                    <span>${esc(c.nombre_especialidad)}</span>
                </div>
                <div class="detalle-item">
                    <label>Médico</label>
                    <span>${esc(c.medico)}</span>
                </div>
                <div class="detalle-item full">
                    <label>Correo médico</label>
                    <span>${esc(c.correo_medico)}</span>
                </div>
                <div class="detalle-item">
                    <label>Consultorio</label>
                    <span>${esc(c.consultorio)}</span>
                </div>
                <div class="detalle-item">
                    <label>Dirección consultorio</label>
                    <span>${esc(c.dir_consultorio ?? '—')}</span>
                </div>
                <div class="detalle-item">
                    <label>Fecha</label>
                    <span>${c.fecha}</span>
                </div>
                <div class="detalle-item">
                    <label>Hora</label>
                    <span>${hora}</span>
                </div>
                <div class="detalle-item">
                    <label>Estado</label>
                    <span>${esc(c.estado)}</span>
                </div>
                <div class="detalle-item">
                    <label>Registrada el</label>
                    <span>${c.created_at ?? '—'}</span>
                </div>
            `;

           
            document.getElementById('detalleModal').style.display = 'flex';
        })

        .catch(() => alert('Error de conexión al cargar el detalle.'));
}


function closeModal() {
    document.getElementById('detalleModal').style.display = 'none';
}

document.getElementById('detalleModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});


['inputNombre', 'inputCedula'].forEach(id => {
    document.getElementById(id).addEventListener('keydown', e => {

        // "e.key" = nombre de la tecla presionada ('Enter', 'Escape', 'a', etc.).
        if (e.key === 'Enter') buscar();
    });
});


function mostrarSpinner(show) {
    
    document.getElementById('spinner').style.display = show ? 'block' : 'none';
}

function esc(str) {

    if (!str) return '—';

    return String(str)
        
        .replace(/&/g,  '&amp;')   
        .replace(/</g,  '&lt;')    
        .replace(/>/g,  '&gt;')    
        .replace(/"/g,  '&quot;'); 
    
}