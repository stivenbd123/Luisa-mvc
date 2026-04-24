
function openModal(modo, data = null) {

    const modal = document.getElementById('consultorioModal');

    modal.style.display = 'flex';


    if (modo === 'editar' && data) {

        document.getElementById('modalTitle').innerText = 'Editar Consultorio';
        document.getElementById('accion').value = 'editar_consultorio';
        document.getElementById('id_consultorio').value = data.id_consultorio;
        document.getElementById('nombre').value    = data.nombre;
        document.getElementById('direccion').value = data.direccion;
        document.getElementById('telefono').value  = data.telefono || '';

    } else {

        document.getElementById('modalTitle').innerText = 'Nuevo Consultorio';
        document.getElementById('accion').value = 'crear_consultorio';
        document.getElementById('nombre').value    = '';
        document.getElementById('direccion').value = '';
        document.getElementById('telefono').value  = '';
        document.getElementById('id_consultorio').value = '';
    }
}


function closeModal() {
    document.getElementById('consultorioModal').style.display = 'none';
}



window.onclick = function(event) {

    if (event.target == document.getElementById('consultorioModal')) {
        closeModal();
    }
}