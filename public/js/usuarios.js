
function openModal(mode, data = null) {

    const modal = document.getElementById('userModal');

    const form = document.getElementById('userForm');

    modal.style.display = 'flex';


    if (mode === 'create') {

        form.reset();

        document.getElementById('modalTitle').innerText = 'Nuevo Usuario';
        document.getElementById('form_accion').value = 'crear_usuario';
        document.getElementById('contrasena').required = true;
        document.getElementById('pass_hint').style.display = 'none';


    } else {

        document.getElementById('modalTitle').innerText = 'Editar Usuario';
        document.getElementById('form_accion').value = 'editar_usuario';
        document.getElementById('form_id_usuario').value = data.id_usuario;
        document.getElementById('primer_nombre').value    = data.primer_nombre;
        document.getElementById('segundo_nombre').value   = data.segundo_nombre;
        document.getElementById('primer_apellido').value  = data.primer_apellido;
        document.getElementById('segundo_apellido').value = data.segundo_apellido;
        document.getElementById('numero_de_cedula').value = data.numero_de_cedula;
        document.getElementById('correo_electronico').value = data.correo_electronico;
        document.getElementById('numero_de_celular').value = data.numero_de_celular;
        document.getElementById('rol').value = data.rol;
        document.getElementById('contrasena').required = false;
        document.getElementById('pass_hint').style.display = 'block';
    }
}


function closeModal() {
    
    document.getElementById('userModal').style.display = 'none';
}


window.onclick = function(event) {

    if (event.target == document.getElementById('userModal')) {
        closeModal();
    }
}