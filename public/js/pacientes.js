
function openModal(accion, data = null) {

    const modal = document.getElementById('pacienteModal');
    const form  = document.getElementById('pacienteForm');
    modal.style.display = 'flex';


    if (accion === 'editar' && data) {

        document.getElementById('modalTitle').innerText = 'Editar Paciente';
        document.getElementById('formAccion').value = 'editar';
        document.getElementById('id_paciente').value = data.id_paciente;
        document.getElementById('primer_nombre').value    = data.primer_nombre;
        document.getElementById('segundo_nombre').value   = data.segundo_nombre;
        document.getElementById('primer_apellido').value  = data.primer_apellido;
        document.getElementById('segundo_apellido').value = data.segundo_apellido;
        document.getElementById('numero_de_cedula').value = data.numero_de_cedula;
        document.getElementById('correo_electronico').value = data.correo_electronico;
        document.getElementById('numero_de_celular').value = data.numero_de_celular;
        document.getElementById('direccion').value = data.direccion || '';

    } else {

        form.reset();

        document.getElementById('modalTitle').innerText = 'Nuevo Paciente';
        document.getElementById('formAccion').value = 'crear';

    }
}



function closeModal() {

    document.getElementById('pacienteModal').style.display = 'none';
}


window.onclick = function(event) {

    if (event.target == document.getElementById('pacienteModal')) {
        closeModal();
    }
}