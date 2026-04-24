
function generarSlots() {

   
    const slots = [];

    const jornadas = [
        { inicio: 7  * 60,      fin: 11 * 60 + 40 },  // Mañana: 07:00–11:40
        { inicio: 14 * 60,      fin: 17 * 60 + 40 }   // Tarde:  14:00–17:40
    ];

   
    jornadas.forEach(j => {

        
        for (let m = j.inicio; m <= j.fin; m += 20) {

            const hh = String(Math.floor(m / 60)).padStart(2, '0');
            
            const mm = String(m % 60).padStart(2, '0');

            slots.push(`${hh}:${mm}`);
        }
    });

    return slots;
}

const TODOS_LOS_SLOTS = generarSlots();


function esDiaHabil(fechaStr) {

    if (!fechaStr) return false;

    const [y, mo, d] = fechaStr.split('-').map(Number);

    const dia = new Date(y, mo - 1, d).getDay();

    return dia !== 0 && dia !== 6;
}

document.getElementById('inputFecha').addEventListener('change', function () {

    
    if (!esDiaHabil(this.value)) {

        alert('Por favor seleccione un día hábil (lunes a viernes). Los fines de semana no hay atención médica.');

        this.value = '';

        limpiarSlots();
    }
    
});


function actualizarSlots() {

    const idMedico   = document.getElementById('selectMedico').value;
    const fecha      = document.getElementById('inputFecha').value;
    const contenedor = document.getElementById('slotsContainer');

    document.getElementById('horaSeleccionada').value = '';
    document.getElementById('btnGuardar').disabled = true;

    if (!idMedico || !fecha || !esDiaHabil(fecha)) {
        contenedor.innerHTML = '<span class="slots-vacio">Seleccione médico y fecha para ver horarios disponibles</span>';
        return;
    }


    const clave    = `${idMedico}_${fecha}`;

    const ocupados = horasOcupadas[clave] || [];

    const disponibles = TODOS_LOS_SLOTS.filter(s => !ocupados.includes(s));

    if (disponibles.length === 0) {
        contenedor.innerHTML = '<span class="slots-vacio" style="color:#dc3545;">⚠ No hay horarios disponibles para este médico en la fecha seleccionada.</span>';
        return;
    }

    contenedor.innerHTML = '';

    TODOS_LOS_SLOTS.forEach(slot => {

        const btn = document.createElement('button');

        btn.type = 'button';

        btn.textContent = slot;

        btn.className = 'slot-btn' + (ocupados.includes(slot) ? ' ocupado' : '');

        if (!ocupados.includes(slot)) {

            btn.addEventListener('click', function () {

                
                document.querySelectorAll('.slot-btn.selected')
                        .forEach(b => b.classList.remove('selected'));

                this.classList.add('selected');

                document.getElementById('horaSeleccionada').value = slot;

                document.getElementById('btnGuardar').disabled = false;
            });

        } else {

            btn.disabled = true;

            btn.title = 'Horario ocupado';
        }

       
        contenedor.appendChild(btn);
    });
}


function limpiarSlots() {
    document.getElementById('slotsContainer').innerHTML =
        '<span class="slots-vacio">Seleccione médico y fecha para ver horarios disponibles</span>';

    
    document.getElementById('horaSeleccionada').value = '';
    document.getElementById('btnGuardar').disabled = true;
}


function openModal() {

    document.getElementById('citaModal').style.display = 'flex';

    document.getElementById('filtroEspecialidad').value = '';

    document.getElementById('inputFecha').value = '';


    const sel = document.getElementById('selectMedico');
    sel.innerHTML = '<option value="">— Seleccione primero una especialidad —</option>';

    sel.disabled = true;

    limpiarSlots();
}


function closeModal() {
    document.getElementById('citaModal').style.display = 'none';
}

document.getElementById('citaModal').addEventListener('click', function (e) {

    if (e.target === this) closeModal();
});



function filtrarMedicos() {

    const idEsp = document.getElementById('filtroEspecialidad').value;
    const sel   = document.getElementById('selectMedico');

    limpiarSlots();

    if (!idEsp) {
        
        sel.innerHTML = '<option value="">— Seleccione primero una especialidad —</option>';
        sel.disabled = true;
        return;
    }

   
    const filtrados = todosMedicos.filter(m => m.especialidad == idEsp);

    if (filtrados.length === 0) {
        sel.innerHTML = '<option value="">No hay médicos para esta especialidad</option>';
        sel.disabled = true;
        return;
    }

    sel.innerHTML = '<option value="">Seleccione Médico</option>';

   
    filtrados.forEach(m => {
        const opt = document.createElement('option');

        opt.value = m.id;

        opt.textContent = m.nombre;

        sel.appendChild(opt);
    });

    sel.disabled = false;
}


document.getElementById('formCita').addEventListener('submit', function (e) {

    if (!document.getElementById('horaSeleccionada').value) {
        e.preventDefault();
        alert('Por favor seleccione un horario disponible.');
    }
});


function cambiarEstado(id, estado) {

    if (confirm('¿Cambiar el estado de la cita a "' + estado + '"?')) {

        window.location.href =
            '../../../controllers/citaController.php?accion=cambiar_estado&id=' + id +
            '&estado=' + encodeURIComponent(estado);
    }
    
}