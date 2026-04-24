// ============================================================
// FUNCIÓN: openModal(modo, data)
// Abre el modal y lo configura según el modo recibido.
// Parámetros:
//   modo (string) = 'crear' o 'editar'.
//   data (object|null) = datos del consultorio al editar. null por defecto.
// ============================================================

function openModal(modo, data = null) {

    // Se obtiene referencia al elemento modal del DOM.
    const modal = document.getElementById('consultorioModal');

    // Muestra el modal: cambia display de 'none' a 'flex'.
    // 'flex' activa el centrado definido en el CSS del modal.
    modal.style.display = 'flex';


    if (modo === 'editar' && data) {

        // --- MODO EDITAR: precargar el formulario con los datos del consultorio ---

        // Cambia el título del modal.
        document.getElementById('modalTitle').innerText = 'Editar Consultorio';

        // Cambia la acción oculta para que el controlador ejecute el UPDATE.
        // El controlador lee: $accion = $_POST['accion'] → 'editar_consultorio'.
        document.getElementById('accion').value = 'editar_consultorio';

        // Llena el ID oculto con el ID del consultorio a modificar.
        // El controlador lo usará en: WHERE id_consultorio = :id_consultorio
        document.getElementById('id_consultorio').value = data.id_consultorio;

        // Precarga los tres campos editables con los valores actuales.
        document.getElementById('nombre').value    = data.nombre;
        document.getElementById('direccion').value = data.direccion;

        // "data.telefono || ''" = si el teléfono es null (campo opcional),
        // usa '' para que el input aparezca vacío en lugar de mostrar "null".
        document.getElementById('telefono').value  = data.telefono || '';

    } else {

        // --- MODO CREAR: limpiar el formulario para un nuevo consultorio ---

        document.getElementById('modalTitle').innerText = 'Nuevo Consultorio';

        // Cambia la acción para que el controlador ejecute el INSERT.
        document.getElementById('accion').value = 'crear_consultorio';

        // Se limpian los tres campos manualmente.
        // Se usa limpieza manual en lugar de form.reset() para mayor control
        // — se garantiza que solo estos tres campos se vacíen, sin afectar
        // otros posibles elementos del formulario.
        document.getElementById('nombre').value    = '';
        document.getElementById('direccion').value = '';
        document.getElementById('telefono').value  = '';

        // Se limpia también el ID oculto por si quedó un valor de una edición previa.
        // Sin esto, al crear después de editar, el campo id_consultorio
        // tendría el ID del último consultorio editado — lo cual el controlador
        // ignoraría porque la acción es 'crear_consultorio', pero es buena práctica.
        document.getElementById('id_consultorio').value = '';
    }
}


// ============================================================
// FUNCIÓN: closeModal()
// Oculta el modal cambiando su display a 'none'.
// ============================================================

function closeModal() {
    document.getElementById('consultorioModal').style.display = 'none';
}


// ============================================================
// EVENTO: window.onclick
// Cierra el modal si el usuario hace clic en el overlay oscuro
// (fuera del área blanca del modal-content).
// ============================================================

window.onclick = function(event) {

    // "event.target" = el elemento DOM exacto que fue clickeado.
    // Si el clic fue en el overlay (#consultorioModal) y no dentro
    // del formulario, se cierra el modal.
    // Si fue dentro del formulario, event.target es un input/button/etc.
    // y no coincide con el modal → no se cierra.
    if (event.target == document.getElementById('consultorioModal')) {
        closeModal();
    }
}