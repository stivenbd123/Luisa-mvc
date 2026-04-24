
function openEspecialidadModal(modo, data = null) {

    document.getElementById('especialidadModal').style.display = 'flex';

    if (modo === 'editar' && data) {

        document.getElementById('espTitle').innerText = 'Editar Especialidad';
        document.getElementById('espAccion').value = 'editar_especialidad';
        document.getElementById('id_especialidad').value = data.id_especialidad;
        document.getElementById('nombre_especialidad').value = data.nombre_especialidad;

    } else {

        document.getElementById('espTitle').innerText = 'Nueva Especialidad';
        document.getElementById('espAccion').value = 'crear_especialidad';
        document.getElementById('nombre_especialidad').value = '';
        document.getElementById('id_especialidad').value = '';
    }
}


function closeEspecialidadModal() {
    document.getElementById('especialidadModal').style.display = 'none';
}



function openMedicoModal(modo, data = null) {

    const modal = document.getElementById('medicoModal');

    modal.style.display = 'flex';

    if (modo === 'editar' && data) {

        document.getElementById('medTitle').innerText = 'Editar Médico';

        document.getElementById('medAccion').value = 'editar_medico';

        document.getElementById('id_medico').value = data.id_medico;

        document.getElementById('primer_nombre').value    = data.primer_nombre;

        document.getElementById('segundo_nombre').value   = data.segundo_nombre   || '';

        document.getElementById('primer_apellido').value  = data.primer_apellido;

        document.getElementById('segundo_apellido').value = data.segundo_apellido || '';

        document.getElementById('med_id_especialidad').value = data.id_especialidad;

        document.getElementById('med_correo').value  = data.correo_electronico;

        document.getElementById('med_celular').value = data.numero_de_celular || '';

    } else {

        document.getElementById('medTitle').innerText = 'Nuevo Médico';
        document.getElementById('medAccion').value = 'crear_medico';
        document.getElementById('id_medico').value            = '';
        document.getElementById('primer_nombre').value        = '';
        document.getElementById('segundo_nombre').value       = '';
        document.getElementById('primer_apellido').value      = '';
        document.getElementById('segundo_apellido').value     = '';
        document.getElementById('med_id_especialidad').value  = '';
        document.getElementById('med_correo').value           = '';
        document.getElementById('med_celular').value          = '';
    }
}


function closeMedicoModal() {
    document.getElementById('medicoModal').style.display = 'none';
}


window.onclick = function(event) {

    if (event.target == document.getElementById('especialidadModal')) {
        closeEspecialidadModal();
    }

    else if (event.target == document.getElementById('medicoModal')) {
        closeMedicoModal();
    }
}