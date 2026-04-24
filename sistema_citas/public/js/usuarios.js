// "function openModal(mode, data = null)" = declara la función con dos parámetros.
// "data = null" = valor por defecto: si no se pasa data al llamar la función,
// JavaScript lo asigna como null automáticamente.
// Ejemplo de llamadas:
//   openModal('create')          → mode='create', data=null
//   openModal('edit', {id:1,...})→ mode='edit', data={id_usuario:1,...}
function openModal(mode, data = null) {

    // "document.getElementById('userModal')" = busca en el HTML el elemento
    // cuyo atributo id sea exactamente 'userModal'.
    // Retorna el elemento DOM (objeto) o null si no existe.
    // Se guarda en variable para no buscarlo múltiples veces.
    const modal = document.getElementById('userModal');

    // Se obtiene referencia al formulario del modal.
    // "userForm" es el id del <form> en el HTML.
    const form = document.getElementById('userForm');

    // "modal.style.display = 'flex'" = muestra el modal cambiando
    // su propiedad CSS display de 'none' (oculto por defecto) a 'flex'.
    // 'flex' porque el CSS del modal usa display:flex para centrar
    // el .modal-content dentro del overlay.
    modal.style.display = 'flex';


    // Se bifurca la lógica según el modo recibido.
    // "mode === 'create'" = comparación estricta (valor y tipo).
    if (mode === 'create') {

        // --- MODO CREAR: limpiar y configurar el formulario vacío ---

        // "form.reset()" = método nativo del DOM que limpia todos los campos
        // del formulario, reseteándolos a sus valores por defecto.
        // Esto asegura que al abrir el modal para crear, no queden datos
        // del último usuario que se editó.
        form.reset();

        // Se cambia el título del modal a "Nuevo Usuario".
        // ".innerText" = propiedad que cambia el TEXTO del elemento
        // (solo texto plano, sin interpretar HTML).
        // Alternativa: .innerHTML permite insertar HTML, pero innerText es
        // más seguro cuando solo se necesita texto.
        document.getElementById('modalTitle').innerText = 'Nuevo Usuario';

        // Se cambia el valor del campo oculto "accion" a 'crear_usuario'.
        // El controlador PHP leerá $_POST['accion'] = 'crear_usuario'
        // y ejecutará el INSERT.
        // ".value" = propiedad para leer o cambiar el valor de un input.
        document.getElementById('form_accion').value = 'crear_usuario';

        // En modo crear, la contraseña es OBLIGATORIA.
        // ".required = true" = agrega el atributo HTML required al input de contraseña.
        // El navegador bloqueará el envío del formulario si el campo está vacío.
        document.getElementById('contrasena').required = true;

        // Se oculta el mensaje de ayuda "Dejar en blanco para no cambiar"
        // porque en modo crear la contraseña siempre es requerida.
        document.getElementById('pass_hint').style.display = 'none';


    } else {

        // --- MODO EDITAR: precargar el formulario con los datos del usuario ---

        // "data" aquí es el objeto JavaScript que PHP generó con json_encode().
        // Ejemplo: { id_usuario: 1, primer_nombre: "Juan", rol: "administrador", ... }
        // Se accede a cada propiedad con "data.nombre_propiedad".

        document.getElementById('modalTitle').innerText = 'Editar Usuario';

        // Se cambia la acción a 'editar_usuario' para que el controlador
        // ejecute el UPDATE en lugar del INSERT.
        document.getElementById('form_accion').value = 'editar_usuario';

        // Se llena el campo oculto de ID para que el controlador sepa
        // QUÉ usuario actualizar con el WHERE id_usuario = :id_usuario.
        document.getElementById('form_id_usuario').value = data.id_usuario;

        // Se precargan todos los campos del formulario con los datos del usuario.
        // Cada ".value = data.campo" llena el input con el valor actual del usuario,
        // para que el admin solo modifique lo que necesita cambiar.
        document.getElementById('primer_nombre').value    = data.primer_nombre;
        document.getElementById('segundo_nombre').value   = data.segundo_nombre;
        document.getElementById('primer_apellido').value  = data.primer_apellido;
        document.getElementById('segundo_apellido').value = data.segundo_apellido;
        document.getElementById('numero_de_cedula').value = data.numero_de_cedula;
        document.getElementById('correo_electronico').value = data.correo_electronico;
        document.getElementById('numero_de_celular').value = data.numero_de_celular;

        // Para el <select> de rol, ".value" selecciona la <option> cuyo value
        // coincida con data.rol (ej: 'administrador' o 'recepcionista').
        document.getElementById('rol').value = data.rol;

        // En modo editar, la contraseña es OPCIONAL.
        // ".required = false" = elimina el atributo required del input de contraseña.
        // El admin puede dejar el campo vacío para conservar la contraseña actual.
        document.getElementById('contrasena').required = false;

        // Se muestra el mensaje de ayuda "Dejar en blanco para no cambiar"
        // para que el admin entienda que la contraseña es opcional al editar.
        // "display: 'block'" = cambia el <small> de hidden a visible.
        document.getElementById('pass_hint').style.display = 'block';
    }
}


// ============================================================
// FUNCIÓN: closeModal()
// Cierra el modal ocultándolo con display:none.
// Llamada por el botón "Cancelar" y por window.onclick (clic fuera).
// ============================================================

function closeModal() {
    // "style.display = 'none'" = oculta el modal asignando display:none.
    // El modal sigue existiendo en el DOM — solo deja de ser visible.
    document.getElementById('userModal').style.display = 'none';
}


// ============================================================
// EVENTO: window.onclick
// Cierra el modal cuando el usuario hace clic FUERA del contenido
// del modal (en el overlay oscuro que rodea .modal-content).
// Patrón de "clic fuera para cerrar" — UX estándar de modales.
// ============================================================

// "window.onclick = function(event)" = asigna una función al evento
// 'click' del objeto window (toda la ventana del navegador).
// "event" = objeto que describe el evento: qué elemento fue clickeado,
// coordenadas del clic, etc.
window.onclick = function(event) {

    // "event.target" = el elemento exacto que fue clickeado.
    // Se compara con el elemento del modal (#userModal).
    // "document.getElementById('userModal')" = el div del overlay oscuro.
    //
    // Si el usuario hizo clic en el OVERLAY (#userModal):
    //   → event.target ES el modal → la condición es true → se cierra.
    //
    // Si hizo clic DENTRO del formulario (.modal-content o sus hijos):
    //   → event.target es el formulario/input/botón, NO el modal
    //   → la condición es false → el modal permanece abierto.
    //
    // Esta distinción funciona porque el clic en el interior NO hace
    // "bubbling" hasta el modal cuando el target es un elemento hijo.
    if (event.target == document.getElementById('userModal')) {
        closeModal();
    }
}