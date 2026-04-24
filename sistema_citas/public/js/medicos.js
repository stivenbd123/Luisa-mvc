// ============================================================
// ARCHIVO: public/js/medicos.js
// PROPÓSITO: Lógica JavaScript de los DOS modales en la vista
//            de médicos y especialidades.
// CONSUMIDO POR: views/html/Medicos/CRUD_Medicos_Y_Especialidades.php
// FUNCIONES:
//   openEspecialidadModal(modo, data) — modal de especialidades.
//   closeEspecialidadModal()          — cierra modal especialidad.
//   openMedicoModal(modo, data)       — modal de médicos.
//   closeMedicoModal()                — cierra modal médico.
// DIFERENCIA con pacientes.js y usuarios.js: este archivo maneja
//   DOS modales independientes (uno por entidad) en lugar de uno solo.
//   Cada modal tiene sus propias funciones open/close.
// ============================================================


// ============================================================
// FUNCIÓN: openEspecialidadModal(modo, data)
// Abre el modal de especialidades y lo configura según el modo.
// Parámetros:
//   modo (string) = 'crear' o 'editar'.
//   data (object|null) = datos de la especialidad al editar.
// ============================================================

function openEspecialidadModal(modo, data = null) {

    // Muestra el modal de especialidad cambiando display de 'none' a 'flex'.
    // 'flex' permite que justify-content y align-items centren el contenido.
    document.getElementById('especialidadModal').style.display = 'flex';

    if (modo === 'editar' && data) {

        // --- MODO EDITAR ESPECIALIDAD ---

        // Cambia el título del modal.
        document.getElementById('espTitle').innerText = 'Editar Especialidad';

        // Cambia la acción oculta para que el controlador ejecute el UPDATE.
        document.getElementById('espAccion').value = 'editar_especialidad';

        // Llena el campo oculto con el ID para el WHERE del UPDATE.
        document.getElementById('id_especialidad').value = data.id_especialidad;

        // Precarga el campo de nombre con el valor actual de la especialidad.
        document.getElementById('nombre_especialidad').value = data.nombre_especialidad;

    } else {

        // --- MODO CREAR ESPECIALIDAD ---

        document.getElementById('espTitle').innerText = 'Nueva Especialidad';
        document.getElementById('espAccion').value = 'crear_especialidad';

        // Limpia el campo de nombre manualmente (solo hay un campo — no se usa form.reset()).
        // Limpiar manualmente es más preciso cuando el modal tiene pocos campos.
        document.getElementById('nombre_especialidad').value = '';

        // El campo id_especialidad queda vacío — el controlador no lo necesita para crear.
        document.getElementById('id_especialidad').value = '';
    }
}


// ============================================================
// FUNCIÓN: closeEspecialidadModal()
// Oculta el modal de especialidades.
// ============================================================

function closeEspecialidadModal() {
    document.getElementById('especialidadModal').style.display = 'none';
}


// ============================================================
// FUNCIÓN: openMedicoModal(modo, data)
// Abre el modal de médicos y lo configura según el modo.
// Parámetros:
//   modo (string) = 'crear' o 'editar'.
//   data (object|null) = datos del médico al editar.
// ============================================================

function openMedicoModal(modo, data = null) {

    // Se obtiene el elemento del modal de médico.
    const modal = document.getElementById('medicoModal');

    // Muestra el modal.
    modal.style.display = 'flex';

    if (modo === 'editar' && data) {

        // --- MODO EDITAR MÉDICO ---

        document.getElementById('medTitle').innerText = 'Editar Médico';

        // Cambia la acción para que el controlador ejecute el UPDATE de médico.
        document.getElementById('medAccion').value = 'editar_medico';

        // Llena el campo oculto con el ID del médico para el WHERE.
        document.getElementById('id_medico').value = data.id_medico;

        // Precarga los campos de nombre y apellido del médico.
        document.getElementById('primer_nombre').value    = data.primer_nombre;

        // "data.segundo_nombre || ''" = si segundo_nombre es null o vacío,
        // usa '' para no mostrar el texto "null" en el input.
        document.getElementById('segundo_nombre').value   = data.segundo_nombre   || '';

        document.getElementById('primer_apellido').value  = data.primer_apellido;
        document.getElementById('segundo_apellido').value = data.segundo_apellido || '';

        // Para el <select> de especialidad, asignar a .value selecciona
        // automáticamente la <option> cuyo value coincida con data.id_especialidad.
        // Ejemplo: si data.id_especialidad = 3, el select mostrará "Cardiología".
        document.getElementById('med_id_especialidad').value = data.id_especialidad;

        document.getElementById('med_correo').value  = data.correo_electronico;
        document.getElementById('med_celular').value = data.numero_de_celular || '';

    } else {

        // --- MODO CREAR MÉDICO ---

        document.getElementById('medTitle').innerText = 'Nuevo Médico';
        document.getElementById('medAccion').value = 'crear_medico';

        // Se limpian todos los campos del formulario del médico manualmente.
        // No se usa form.reset() porque el formulario del modal no tiene un id
        // en el original — se limpian campo por campo para mayor control.
        document.getElementById('id_medico').value            = '';
        document.getElementById('primer_nombre').value        = '';
        document.getElementById('segundo_nombre').value       = '';
        document.getElementById('primer_apellido').value      = '';
        document.getElementById('segundo_apellido').value     = '';

        // Resetea el select a la opción inicial "Seleccione Especialidad".
        // Asignar '' al value del select selecciona la <option value="">.
        document.getElementById('med_id_especialidad').value  = '';

        document.getElementById('med_correo').value           = '';
        document.getElementById('med_celular').value          = '';
    }
}


// ============================================================
// FUNCIÓN: closeMedicoModal()
// Oculta el modal de médicos.
// ============================================================

function closeMedicoModal() {
    document.getElementById('medicoModal').style.display = 'none';
}


// ============================================================
// EVENTO: window.onclick
// Cierra el modal correspondiente si el usuario hace clic
// en el overlay oscuro fuera del contenido del modal.
// DIFERENCIA con pacientes.js y usuarios.js: aquí hay DOS modales,
// por lo que se verifica cuál de los dos fue el objetivo del clic.
// ============================================================

window.onclick = function(event) {

    // Si el clic fue en el overlay del modal de especialidad → cerrarlo.
    if (event.target == document.getElementById('especialidadModal')) {
        closeEspecialidadModal();
    }

    // Si el clic fue en el overlay del modal de médico → cerrarlo.
    // "else if" garantiza que solo se evalúa esta condición si la primera fue falsa.
    // Aunque técnicamente no pueden ser iguales al mismo tiempo,
    // "else if" es más eficiente que dos "if" independientes.
    else if (event.target == document.getElementById('medicoModal')) {
        closeMedicoModal();
    }
}