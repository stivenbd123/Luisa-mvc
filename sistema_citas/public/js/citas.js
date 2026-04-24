// ============================================================
// GENERADOR DE SLOTS DE HORARIO
// Crea el arreglo de todos los turnos posibles del día.
// Jornada mañana: 07:00 – 11:40 (15 turnos de 20 min)
// Jornada tarde:  14:00 – 17:40 (12 turnos de 20 min)
// Total: 27 turnos por médico por día.
// ============================================================

function generarSlots() {

    // "const slots = []" = arreglo vacío donde se acumularán las horas.
    const slots = [];

    // Se definen las dos jornadas como objetos con inicio y fin en MINUTOS.
    // Trabajar en minutos desde medianoche simplifica la aritmética de horas.
    // 7*60 = 420 (07:00), 11*60+40 = 700 (11:40)
    // 14*60 = 840 (14:00), 17*60+40 = 1060 (17:40)
    const jornadas = [
        { inicio: 7  * 60,      fin: 11 * 60 + 40 },  // Mañana: 07:00–11:40
        { inicio: 14 * 60,      fin: 17 * 60 + 40 }   // Tarde:  14:00–17:40
    ];

    // Se itera sobre cada jornada.
    // ".forEach(j => { ... })" = método de arreglo que ejecuta la función
    // una vez por cada elemento. "j" es el objeto de jornada actual.
    jornadas.forEach(j => {

        // "for (let m = j.inicio; m <= j.fin; m += 20)" = bucle desde el
        // inicio hasta el fin de la jornada, avanzando 20 minutos cada vez.
        // "let m" = variable local del bucle (minutos acumulados desde medianoche).
        for (let m = j.inicio; m <= j.fin; m += 20) {

            // "Math.floor(m / 60)" = división entera para obtener las horas.
            // Ejemplo: 490 / 60 = 8.16 → Math.floor = 8 (son las 8 horas).
            const hh = String(Math.floor(m / 60)).padStart(2, '0');
            //                                    ^^^^^^^^^^^^^^^^
            // ".padStart(2, '0')" = si el número tiene un solo dígito (ej: "8"),
            // agrega un "0" al inicio para obtener "08".
            // Garantiza el formato HH:MM consistente.

            // "m % 60" = módulo: obtiene los minutos restantes dentro de la hora.
            // Ejemplo: 490 % 60 = 10 (son 8 horas y 10 minutos → "08:10").
            const mm = String(m % 60).padStart(2, '0');

            // Se agrega el slot formateado al arreglo.
            // Template literal: `${hh}:${mm}` → "08:10", "09:00", etc.
            slots.push(`${hh}:${mm}`);
        }
    });

    return slots;
}

// Se pre-genera el arreglo COMPLETO de slots una sola vez al cargar el script.
// "const" = constante — no cambia durante la sesión.
// Se reutiliza en actualizarSlots() sin recalcular en cada llamada.
const TODOS_LOS_SLOTS = generarSlots();


// ============================================================
// VALIDACIÓN DE DÍA HÁBIL
// Retorna true solo para lunes a viernes (días 1–5 de la semana).
// Parámetro: fechaStr en formato 'YYYY-MM-DD' (del input type="date").
// ============================================================

function esDiaHabil(fechaStr) {

    // Si no hay fecha, retorna false inmediatamente.
    if (!fechaStr) return false;

    // "fechaStr.split('-').map(Number)" = divide 'YYYY-MM-DD' por '-'
    // y convierte cada parte a número.
    // Resultado: ['2024','03','15'] → [2024, 3, 15]
    const [y, mo, d] = fechaStr.split('-').map(Number);

    // "new Date(y, mo - 1, d)" = crea un objeto Date.
    // MES IMPORTANTE: JavaScript usa meses 0-indexados (enero=0, diciembre=11).
    // Por eso se usa "mo - 1": si el mes es 3 (marzo), Date necesita 2.
    // ".getDay()" = retorna el día de la semana: 0=domingo, 1=lunes, ..., 6=sábado.
    const dia = new Date(y, mo - 1, d).getDay();

    // "dia !== 0 && dia !== 6" = no es domingo (0) y no es sábado (6).
    // Retorna true solo para días 1,2,3,4,5 (lunes a viernes).
    return dia !== 0 && dia !== 6;
}

// --- LISTENER DE VALIDACIÓN DE FECHA ---
// Se ejecuta cada vez que el usuario cambia la fecha en el input.
document.getElementById('inputFecha').addEventListener('change', function () {

    // "this.value" = el valor del input de fecha (la fecha que seleccionó el usuario).
    if (!esDiaHabil(this.value)) {

        // Si es fin de semana, alerta y limpia el campo.
        alert('Por favor seleccione un día hábil (lunes a viernes). Los fines de semana no hay atención médica.');

        // "this.value = ''" = borra el valor del input para forzar al usuario a elegir de nuevo.
        this.value = '';

        // Se limpian los slots porque ya no hay una fecha válida seleccionada.
        limpiarSlots();
    }
    // Si es día hábil, el evento 'onchange' del HTML ya llamó a actualizarSlots()
    // directamente, por lo que no es necesario llamarlo aquí.
});


// ============================================================
// ACTUALIZAR SLOTS DE HORA
// Se llama cuando cambia el médico O la fecha.
// Calcula qué slots están disponibles y los renderiza en el DOM.
// ============================================================

function actualizarSlots() {

    // Se leen los valores actuales del médico y la fecha seleccionados.
    const idMedico   = document.getElementById('selectMedico').value;
    const fecha      = document.getElementById('inputFecha').value;
    const contenedor = document.getElementById('slotsContainer');

    // Se resetea la hora seleccionada y se deshabilita el botón Guardar.
    // Si el usuario cambia de médico o fecha, debe elegir un nuevo slot.
    document.getElementById('horaSeleccionada').value = '';
    document.getElementById('btnGuardar').disabled = true;

    // Si falta médico, fecha, o la fecha no es hábil, se limpia el contenedor.
    if (!idMedico || !fecha || !esDiaHabil(fecha)) {
        contenedor.innerHTML = '<span class="slots-vacio">Seleccione médico y fecha para ver horarios disponibles</span>';
        return;
    }

    // Se construye la clave de búsqueda en el mismo formato que PHP:
    // 'id_medico_fecha' → ej: '5_2024-03-15'
    const clave    = `${idMedico}_${fecha}`;

    // "horasOcupadas[clave] || []" = si no hay entradas para ese médico y fecha,
    // usa un arreglo vacío (ningún slot está ocupado).
    const ocupados = horasOcupadas[clave] || [];

    // Se filtran los slots disponibles: los que NO están en el arreglo de ocupados.
    // ".filter(s => !ocupados.includes(s))" = retiene los slots que no están ocupados.
    const disponibles = TODOS_LOS_SLOTS.filter(s => !ocupados.includes(s));

    // Si todos los slots están ocupados, muestra mensaje de advertencia.
    if (disponibles.length === 0) {
        contenedor.innerHTML = '<span class="slots-vacio" style="color:#dc3545;">⚠ No hay horarios disponibles para este médico en la fecha seleccionada.</span>';
        return;
    }

    // Se limpia el contenedor para renderizar los nuevos botones de slot.
    contenedor.innerHTML = '';

    // Se itera sobre TODOS los slots (no solo disponibles) para mostrar
    // también los ocupados visualmente (tachados y deshabilitados).
    TODOS_LOS_SLOTS.forEach(slot => {

        // "document.createElement('button')" = crea un elemento <button> en memoria
        // sin añadirlo al DOM todavía.
        const btn = document.createElement('button');

        // "type = 'button'" = CRÍTICO — evita que al hacer clic en un slot
        // se envíe el formulario. Sin esto, cualquier clic enviaría el form.
        btn.type = 'button';

        // El texto visible del botón es la hora: "09:00", "14:20", etc.
        btn.textContent = slot;

        // Se asigna la clase base 'slot-btn'.
        // Si el slot está ocupado, se agrega la clase ' ocupado' para el estilo gris.
        btn.className = 'slot-btn' + (ocupados.includes(slot) ? ' ocupado' : '');

        if (!ocupados.includes(slot)) {

            // Slot DISPONIBLE: se agrega el listener de selección.
            // "addEventListener('click', function() { ... })" = al hacer clic,
            // se marca este slot como seleccionado y se guarda su hora.
            btn.addEventListener('click', function () {

                // Se remueve la clase 'selected' de todos los slots previamente seleccionados.
                // ".querySelectorAll('.slot-btn.selected')" = busca todos los elementos
                // con ambas clases 'slot-btn' y 'selected' dentro del documento.
                // ".forEach(b => b.classList.remove('selected'))" = itera y remueve la clase.
                document.querySelectorAll('.slot-btn.selected')
                        .forEach(b => b.classList.remove('selected'));

                // Se agrega 'selected' al botón que el usuario acaba de clicar.
                // "this" dentro del listener hace referencia al botón clickeado.
                this.classList.add('selected');

                // Se guarda la hora en el campo oculto del formulario.
                // El controlador PHP lo recibirá con $_POST['hora'].
                document.getElementById('horaSeleccionada').value = slot;

                // Se habilita el botón "Guardar" ahora que hay hora seleccionada.
                document.getElementById('btnGuardar').disabled = false;
            });

        } else {

            // Slot OCUPADO: se deshabilita el botón completamente.
            // "disabled = true" = el botón no puede ser clickeado.
            btn.disabled = true;

            // "title" = tooltip de texto que aparece al pasar el cursor.
            btn.title = 'Horario ocupado';
        }

        // "contenedor.appendChild(btn)" = añade el botón creado al DOM
        // dentro del div#slotsContainer, al final de sus hijos.
        contenedor.appendChild(btn);
    });
}


// ============================================================
// LIMPIAR SLOTS
// Restaura el contenedor a su estado vacío inicial.
// Llamado cuando cambia el médico, la fecha o se cierra el modal.
// ============================================================

function limpiarSlots() {
    document.getElementById('slotsContainer').innerHTML =
        '<span class="slots-vacio">Seleccione médico y fecha para ver horarios disponibles</span>';

    // Se limpian el campo oculto y el botón Guardar.
    document.getElementById('horaSeleccionada').value = '';
    document.getElementById('btnGuardar').disabled = true;
}


// ============================================================
// MODAL: ABRIR
// Abre el modal y resetea todos los campos a su estado inicial.
// ============================================================

function openModal() {

    // Muestra el modal.
    document.getElementById('citaModal').style.display = 'flex';

    // Resetea el filtro de especialidad a "Seleccione Especialidad".
    document.getElementById('filtroEspecialidad').value = '';

    // Limpia el campo de fecha.
    document.getElementById('inputFecha').value = '';

    // Resetea el select de médico a su estado inicial (deshabilitado).
    const sel = document.getElementById('selectMedico');
    sel.innerHTML = '<option value="">— Seleccione primero una especialidad —</option>';

    // "disabled = true" = deshabilita el select de médico hasta que se elija especialidad.
    sel.disabled = true;

    // Limpia el contenedor de slots.
    limpiarSlots();
}


// ============================================================
// MODAL: CERRAR
// ============================================================

function closeModal() {
    document.getElementById('citaModal').style.display = 'none';
}

// Cerrar el modal haciendo clic en el overlay oscuro (fuera del contenido).
// Usa addEventListener en lugar de window.onclick para mayor especificidad.
document.getElementById('citaModal').addEventListener('click', function (e) {

    // "e.target === this" = el clic fue directamente en el overlay (#citaModal)
    // y no en alguno de sus elementos hijos (el formulario, inputs, etc.).
    // "===" comparación estricta: verifica que sean el mismo objeto exacto.
    if (e.target === this) closeModal();
});


// ============================================================
// FILTRAR MÉDICOS POR ESPECIALIDAD
// Repobla dinámicamente el select de médico según la especialidad elegida.
// Usa el arreglo "todosMedicos" definido en el <script> inline de citas.php.
// ============================================================

function filtrarMedicos() {

    // Se lee el ID de la especialidad seleccionada.
    const idEsp = document.getElementById('filtroEspecialidad').value;
    const sel   = document.getElementById('selectMedico');

    // Se limpian los slots porque cambiar especialidad implica
    // cambiar médico, lo cual invalida el horario anterior.
    limpiarSlots();

    if (!idEsp) {
        // Sin especialidad seleccionada: se restaura el estado inicial.
        sel.innerHTML = '<option value="">— Seleccione primero una especialidad —</option>';
        sel.disabled = true;
        return;
    }

    // ".filter(m => m.especialidad == idEsp)" = filtra los médicos cuya
    // propiedad 'especialidad' coincida con el ID elegido.
    // "==" (doble igual) en lugar de "===" porque idEsp es string (del input)
    // y m.especialidad es número (del PHP), y == hace conversión de tipo.
    const filtrados = todosMedicos.filter(m => m.especialidad == idEsp);

    if (filtrados.length === 0) {
        sel.innerHTML = '<option value="">No hay médicos para esta especialidad</option>';
        sel.disabled = true;
        return;
    }

    // Se añade la opción inicial vacía antes de los médicos.
    sel.innerHTML = '<option value="">Seleccione Médico</option>';

    // Se crea una <option> por cada médico filtrado.
    filtrados.forEach(m => {
        const opt = document.createElement('option');

        // "opt.value" = lo que se envía al servidor ($_POST['id_medico']).
        opt.value = m.id;

        // "opt.textContent" = texto visible en el desplegable.
        opt.textContent = m.nombre;

        // "sel.appendChild(opt)" = añade la opción al select.
        sel.appendChild(opt);
    });

    // Se habilita el select de médico ahora que tiene opciones.
    sel.disabled = false;
}


// ============================================================
// VALIDACIÓN DEL FORMULARIO ANTES DE ENVIAR
// Previene el envío si no se seleccionó ningún slot de hora.
// El botón "Guardar" ya está deshabilitado, pero esta es una
// capa extra de seguridad si el disabled se saltara por alguna razón.
// ============================================================

document.getElementById('formCita').addEventListener('submit', function (e) {

    // "e.preventDefault()" = cancela el envío del formulario.
    // Solo se llama si no hay hora seleccionada.
    if (!document.getElementById('horaSeleccionada').value) {
        e.preventDefault();
        alert('Por favor seleccione un horario disponible.');
    }
});


// ============================================================
// CAMBIAR ESTADO DE UNA CITA DESDE LA TABLA
// Llamada desde el onchange del <select> de estado en cada fila.
// Parámetros:
//   id (number)     = ID de la cita a modificar.
//   estado (string) = nuevo estado seleccionado ('Confirmada', etc.).
// ============================================================

function cambiarEstado(id, estado) {

    // Se solicita confirmación antes de cambiar el estado.
    // "confirm()" retorna true (Aceptar) o false (Cancelar).
    if (confirm('¿Cambiar el estado de la cita a "' + estado + '"?')) {

        // Si confirma, se redirige al controlador con los parámetros en la URL.
        // "encodeURIComponent(estado)" = codifica el texto del estado para la URL.
        // Evita problemas con caracteres especiales o espacios en el estado.
        // Ejemplo: "Atendida" → "Atendida" (sin cambios, pero protege valores como "En revisión").
        window.location.href =
            '../../../controllers/citaController.php?accion=cambiar_estado&id=' + id +
            '&estado=' + encodeURIComponent(estado);
    }
    // Si cancela, el select visual ya cambió en la página pero la BD no se actualizó.
    // MEJORA: restaurar el select al valor original si el usuario cancela.
}