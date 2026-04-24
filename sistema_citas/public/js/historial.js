// ============================================================
// FUNCIÓN: buscar()
// Realiza una petición AJAX al controlador para buscar citas.
// Lee los valores de los inputs de nombre y cédula.
// Si ambos tienen valor, usa el nombre (prioridad al nombre).
// Actualiza la tabla con los resultados sin recargar la página.
// ============================================================

function buscar() {

    // Se leen y limpian los valores de los dos inputs de búsqueda.
    // ".trim()" elimina espacios al inicio y al final.
    const nombre = document.getElementById('inputNombre').value.trim();
    const cedula = document.getElementById('inputCedula').value.trim();

    // "nombre || cedula" = operador OR: usa el nombre si existe,
    // si no, usa la cédula. Si ambos están vacíos, q = '' (vacío).
    // Con q vacío, el controlador retorna TODAS las citas.
    const q = nombre || cedula;

    // Se muestra el spinner de carga mientras espera la respuesta.
    mostrarSpinner(true);

    // --- PETICIÓN AJAX CON fetch() ---

    // "fetch(url)" = función nativa de JavaScript que hace una petición HTTP.
    // Retorna una Promesa (Promise) — se resuelve cuando llega la respuesta.
    // "encodeURIComponent(q)" = codifica el texto para usarlo en la URL.
    // Ejemplo: "juan pérez" → "juan%20p%C3%A9rez" (espacios y tildes codificados).
    fetch(`${CTRL}?accion=buscar&q=${encodeURIComponent(q)}`)

        // ".then(r => r.json())" = cuando llega la respuesta HTTP,
        // ".json()" la parsea de texto JSON a objeto JavaScript.
        // Retorna otra Promesa que se resuelve con el objeto parseado.
        .then(r => r.json())

        // ".then(res => { ... })" = cuando el JSON está listo,
        // "res" es el objeto JavaScript con la estructura:
        // { ok: true, data: [...] } o { ok: false, msg: '...' }
        .then(res => {

            // Se oculta el spinner — la respuesta llegó.
            mostrarSpinner(false);

            // Si el controlador retornó ok:false, hay un error en el servidor.
            if (!res.ok) {
                alert('Error: ' + res.msg);
                return;
            }

            // Se actualiza la tabla con los datos recibidos.
            renderTabla(res.data);
        })

        // ".catch()" = captura errores de RED (no errores del servidor).
        // Ejemplos: sin conexión a internet, servidor caído, URL incorrecta.
        // Los errores HTTP (404, 500) NO llegan aquí — llegan al .then().
        .catch(() => {
            mostrarSpinner(false);
            alert('Error de conexión al buscar.');
        });
}


// ============================================================
// FUNCIÓN: limpiar()
// Vacía los inputs de búsqueda y recarga todas las citas.
// Llamar buscar() con q vacío retorna todo sin filtro.
// ============================================================

function limpiar() {
    document.getElementById('inputNombre').value = '';
    document.getElementById('inputCedula').value = '';

    // Con inputs vacíos, buscar() enviará q='' al controlador.
    // El controlador retornará todas las citas (sin filtro WHERE).
    buscar();
}


// ============================================================
// FUNCIÓN: renderTabla(citas)
// Reemplaza el contenido del <tbody> con las filas generadas
// a partir del arreglo de citas recibido del controlador.
// Parámetro: citas (Array) = arreglo de objetos de cita.
// ============================================================

function renderTabla(citas) {

    const tbody = document.getElementById('tbodyHistorial');
    const info  = document.getElementById('resultInfo');

    // Si el arreglo está vacío, muestra mensaje de "sin resultados".
    if (!citas || citas.length === 0) {

        // "innerHTML" = reemplaza TODO el contenido HTML del elemento.
        // Template literal con backticks permite HTML multilínea en JS.
        tbody.innerHTML = `<tr><td colspan="10" class="no-results">No se encontraron citas.</td></tr>`;

        // Limpia el contador de resultados.
        info.textContent = '';
        return;
    }

    // Actualiza el contador de resultados.
    // "textContent" = inserta texto plano (sin interpretar HTML).
    // Más seguro que innerHTML para texto del usuario.
    info.textContent = `${citas.length} resultado(s) encontrado(s)`;

    // Mapa de estado → clase CSS para el badge de color.
    const estadoClase = {
        agendada:   'agendada',
        confirmada: 'confirmada',
        cancelada:  'cancelada',
        atendida:   'atendida'
    };

    // ".map(c => `...`)" = transforma cada objeto de cita en un string HTML.
    // Retorna un arreglo de strings HTML, uno por fila.
    // ".join('')" = une todos los strings del arreglo en uno solo (sin separador).
    // El resultado se asigna a tbody.innerHTML para actualizar la tabla.
    tbody.innerHTML = citas.map(c => {

        // Normaliza el estado a minúsculas para buscar en el mapa.
        const est   = c.estado.toLowerCase();

        // "?? 'agendada'" = clase por defecto si el estado no está en el mapa.
        const clase = estadoClase[est] ?? 'agendada';

        // "c.hora ? c.hora.substring(0, 5) : ''" = recorta hora si existe.
        // Evita error si hora es null o undefined.
        const hora  = c.hora ? c.hora.substring(0, 5) : '';

        // Se construye el HTML de la fila como template literal.
        // "esc()" = función de escape XSS definida abajo — sanitiza los datos.
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


// ============================================================
// FUNCIÓN: verDetalle(id)
// Hace una petición AJAX para obtener el detalle completo
// de una cita específica y muestra el resultado en el modal.
// Parámetro: id (number) = ID de la cita a consultar.
// ============================================================

function verDetalle(id) {

    // Petición AJAX al controlador con accion=detalle e id de la cita.
    fetch(`${CTRL}?accion=detalle&id=${id}`)

        .then(r => r.json())

        .then(res => {

            // Si el controlador retornó error (cita no encontrada, ID inválido).
            if (!res.ok) {
                alert('Error: ' + res.msg);
                return;
            }

            // "const c = res.data" = objeto con todos los campos de la cita.
            const c = res.data;

            // --- CONSTRUCCIÓN DEL NOMBRE COMPLETO DEL PACIENTE ---
            // Se usa un arreglo de los 4 campos de nombre y se filtran los vacíos.
            // ".filter(Boolean)" = elimina null, undefined y '' del arreglo.
            // ".join(' ')" = une los nombres que quedan con un espacio.
            // Resultado: "Juan Carlos Pérez López" o "Juan Pérez" si no hay segundos.
            const nombreCompleto = [c.primer_nombre, c.segundo_nombre,
                                    c.primer_apellido, c.segundo_apellido]
                                    .filter(Boolean).join(' ');

            const hora = c.hora ? c.hora.substring(0, 5) : '';

            // --- GENERACIÓN DEL HTML DEL DETALLE ---
            // Se construye un grid de tarjetas de información.
            // "detalle-item" = tarjeta individual con label y valor.
            // "detalle-item full" = tarjeta que ocupa las 2 columnas del grid.
            // "c.dir_consultorio ?? '—'" = si es null, muestra '—' (guión largo).
            // "??" = operador nullish coalescing: retorna el lado derecho
            //         solo cuando el lado izquierdo es null o undefined.
            //         Diferente de "||" que también reemplaza '' y 0.
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

            // Se muestra el modal con el detalle.
            document.getElementById('detalleModal').style.display = 'flex';
        })

        .catch(() => alert('Error de conexión al cargar el detalle.'));
}


// ============================================================
// FUNCIÓN: closeModal()
// Cierra el modal de detalle.
// ============================================================

function closeModal() {
    document.getElementById('detalleModal').style.display = 'none';
}

// Cerrar el modal al hacer clic en el overlay oscuro.
document.getElementById('detalleModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});


// ============================================================
// BÚSQUEDA AL PRESIONAR ENTER EN LOS INPUTS
// ".forEach(id => ...)" itera sobre el arreglo de IDs de inputs.
// Agrega un listener 'keydown' a cada uno.
// "e.key === 'Enter'" verifica que la tecla presionada sea Enter.
// ============================================================

['inputNombre', 'inputCedula'].forEach(id => {
    document.getElementById(id).addEventListener('keydown', e => {

        // "e.key" = nombre de la tecla presionada ('Enter', 'Escape', 'a', etc.).
        if (e.key === 'Enter') buscar();
    });
});


// ============================================================
// UTILIDADES
// ============================================================

// --- MOSTRAR/OCULTAR SPINNER ---
function mostrarSpinner(show) {
    // "show ? 'block' : 'none'" = ternario — si show es true, 'block'; si no, 'none'.
    document.getElementById('spinner').style.display = show ? 'block' : 'none';
}

// --- FUNCIÓN DE ESCAPE XSS (versión JavaScript) ---
// Equivalente al htmlspecialchars() de PHP, pero para JavaScript.
// Se necesita porque el HTML generado en renderTabla() e innerHTML
// proviene de datos de la BD — deben escaparse para prevenir XSS.
// Parámetro: str = cualquier valor (string, null, undefined, number).
// Retorna: string seguro para insertar en HTML.
function esc(str) {

    // Si str es null, undefined, '' o 0, retorna '—' (guión largo).
    // "!str" es true para todos esos valores.
    if (!str) return '—';

    // "String(str)" = convierte el valor a string (por si llega un número).
    return String(str)
        // Orden IMPORTANTE: "&" debe reemplazarse PRIMERO.
        // Si se reemplazara después de "<", el "&lt;" generado se volvería a escapar.
        .replace(/&/g,  '&amp;')   // & → &amp;
        .replace(/</g,  '&lt;')    // < → &lt;  (previene inyección de tags)
        .replace(/>/g,  '&gt;')    // > → &gt;
        .replace(/"/g,  '&quot;'); // " → &quot; (previene inyección en atributos)
    // Las expresiones regulares /&/g, /</g, etc. usan el flag "g" (global)
    // para reemplazar TODAS las ocurrencias, no solo la primera.
}