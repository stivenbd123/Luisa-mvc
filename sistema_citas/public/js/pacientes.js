// ============================================================
// FUNCIÓN: openModal(accion, data)
// Abre el modal y lo configura según la acción:
//   'crear' → formulario limpio para un nuevo paciente.
//   'editar' → formulario pre-llenado con los datos del paciente.
// Parámetros:
//   accion (string) = 'crear' o 'editar'.
//   data (object|null) = objeto con los datos del paciente.
//                        null por defecto si no se pasa.
// ============================================================

// "data = null" = parámetro opcional con valor por defecto null.
// Al llamar openModal('crear') sin segundo argumento,
// JavaScript asigna null a data automáticamente.
function openModal(accion, data = null) {

    // Se obtiene referencia al elemento del modal y al formulario.
    const modal = document.getElementById('pacienteModal');
    const form  = document.getElementById('pacienteForm');

    // Se muestra el modal cambiando su display de 'none' a 'flex'.
    // 'flex' permite centrar .modal-content usando justify-content y align-items.
    modal.style.display = 'flex';


    // Se bifurca según la acción recibida.
    // "accion === 'editar' && data" = la condición tiene DOS partes:
    // 1. "accion === 'editar'" = la acción es editar (no crear).
    // 2. "&& data" = el objeto data existe y no es null/undefined/false.
    // Ambas deben ser verdaderas para entrar al modo editar.
    // Si llegara 'editar' pero sin data (error de llamada), entraría al else
    // y mostraría el formulario vacío en lugar de fallar.
    if (accion === 'editar' && data) {

        // --- MODO EDITAR: precargar formulario con datos del paciente ---

        // Se cambia el título del modal.
        document.getElementById('modalTitle').innerText = 'Editar Paciente';

        // Se cambia la acción oculta a 'editar' para que el controlador
        // ejecute actualizarPaciente() en lugar de crearPaciente().
        document.getElementById('formAccion').value = 'editar';

        // Se llena el campo oculto con el ID del paciente para el WHERE del UPDATE.
        document.getElementById('id_paciente').value = data.id_paciente;

        // Se precargan todos los campos con los valores actuales del paciente.
        // "data.campo" accede a cada propiedad del objeto JSON recibido desde PHP.
        document.getElementById('primer_nombre').value    = data.primer_nombre;
        document.getElementById('segundo_nombre').value   = data.segundo_nombre;
        document.getElementById('primer_apellido').value  = data.primer_apellido;
        document.getElementById('segundo_apellido').value = data.segundo_apellido;
        document.getElementById('numero_de_cedula').value = data.numero_de_cedula;
        document.getElementById('correo_electronico').value = data.correo_electronico;
        document.getElementById('numero_de_celular').value = data.numero_de_celular;

        // "data.direccion" puede ser null si el paciente no registró dirección.
        // JavaScript asigna null al .value del input, lo cual el navegador
        // convierte a la cadena "null" — un error visual.
        // CORRECCIÓN APLICADA: "data.direccion || ''" = si data.direccion es
        // null, undefined o cadena vacía, usa '' (cadena vacía) en su lugar.
        // "||" = operador OR: retorna el primer valor "truthy" o el último valor.
        document.getElementById('direccion').value = data.direccion || '';

    } else {

        // --- MODO CREAR: limpiar formulario para nuevo paciente ---

        // "form.reset()" = limpia todos los campos del formulario,
        // reseteándolos a sus valores por defecto (cadena vacía para inputs).
        form.reset();

        // Se restaura el título y la acción al modo de creación.
        document.getElementById('modalTitle').innerText = 'Nuevo Paciente';
        document.getElementById('formAccion').value = 'crear';

        // No se toca el campo id_paciente — form.reset() ya lo dejó vacío.
        // El controlador recibirá $_POST['id_paciente'] = '' y lo ignorará
        // porque la acción es 'crear' (INSERT no necesita ID).
    }
}


// ============================================================
// FUNCIÓN: closeModal()
// Oculta el modal asignando display:none.
// ============================================================

function closeModal() {
    // Oculta el modal. El DOM lo conserva para poder reabrirlo.
    document.getElementById('pacienteModal').style.display = 'none';
}


// ============================================================
// EVENTO: window.onclick
// Cierra el modal si el usuario hace clic en el overlay oscuro
// (fuera de la tarjeta .modal-content).
// ============================================================

// Se asigna un manejador de clic global a toda la ventana.
window.onclick = function(event) {

    // "event.target" = el elemento DOM exacto que recibió el clic.
    // Si el clic fue en el overlay (#pacienteModal) y NO dentro
    // del formulario (.modal-content o sus hijos), se cierra.
    //
    // Por qué funciona la distinción:
    // - Clic en el overlay → event.target ES #pacienteModal → se cierra.
    // - Clic en el formulario → event.target es un input/button/label
    //   → NO es #pacienteModal → no se cierra.
    if (event.target == document.getElementById('pacienteModal')) {
        closeModal();
    }
}