<?php
// "session_start()" = inicia la sesión para leer $_SESSION.
session_start();

// Se carga el modelo Usuario para obtener la lista de usuarios.
// "__DIR__" = ruta absoluta de la carpeta actual (views/html/Usuarios/).
// "/../../../models/Usuario.php" = sube 3 niveles hasta la raíz
// y entra a models/Usuario.php.
require_once __DIR__ . "/../../../models/Usuario.php";


// --- GUARDIA DE ACCESO DOBLE ---

// Se verifican DOS condiciones con "||" (OR lógico):
// Condición 1: "!isset($_SESSION['id_usuario'])" = el usuario NO está logueado.
// Condición 2: "$_SESSION['rol'] != 'administrador'" = está logueado pero NO es admin.
// Si CUALQUIERA de las dos es verdadera, se redirige a home.
// Esto protege la vista de:
//   a) Usuarios no autenticados (acceso directo por URL).
//   b) Usuarios autenticados pero sin permiso de admin (recepcionistas).
if(!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'administrador'){
    // Redirige a home.php — dos niveles arriba desde Usuarios/.
    header("Location: ../home.php");
    // Detiene la ejecución para que no se renderice HTML de la vista.
    exit();
}


// --- CARGA DE DATOS ---

// Se instancia el modelo Usuario para consultar la base de datos.
$usuarioModel = new Usuario();

// "obtenerUsuarios()" = ejecuta SELECT * FROM usuarios ORDER BY id DESC.
// Retorna un arreglo con todos los usuarios del sistema.
// Se guarda en $usuarios para iterarlo en la tabla HTML más abajo.
$usuarios = $usuarioModel->obtenerUsuarios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios | Sistema Médico</title>

    <!-- CSS específico para la vista de usuarios del panel admin. -->
    <link rel="stylesheet" href="../../../public/css/styleCRUDUsuarios.css">
</head>
<body>

<!-- ================================================
     HEADER: barra superior con título y botón de volver
     ================================================ -->
<header>
    <!-- "<span>" = contenedor inline para el título del módulo actual. -->
    <span>Panel de Administración - Usuarios</span>

    <!-- Enlace para volver al dashboard principal.
         "../home.php" = sube un nivel desde Usuarios/ a html/ donde vive home.php. -->
    <a href="../home.php" class="home-btn">⬅ Volver al Home</a>
</header>


<div class="container">

    <!-- ================================================
         ALERTAS DE SESIÓN (éxito o error tras una acción)
         El controlador guarda el mensaje en $_SESSION y redirige aquí.
         La vista lo muestra UNA vez y lo borra con unset().
         ================================================ -->

    <?php if(isset($_SESSION['success_crud'])): ?>
        <!-- "alert success-alert" = dos clases CSS: la base "alert" y el modificador "success-alert".
             Esta combinación permite estilos compartidos (padding, border-radius) en "alert"
             y colores específicos (verde) en "success-alert". -->
        <div class="alert success-alert">
            <?php
            // "< ?= ... ? >" = forma corta de "<?php echo ... ? >".
            // Imprime el valor directamente. Idéntico en resultado, más compacto en vistas.
            echo $_SESSION['success_crud'];

            // Se elimina inmediatamente para no repetir el mensaje al recargar.
            unset($_SESSION['success_crud']);
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_crud'])): ?>
        <div class="alert error-alert">
            <?php
            echo $_SESSION['error_crud'];
            unset($_SESSION['error_crud']);
            ?>
        </div>
    <?php endif; ?>


    <!-- ================================================
         BARRA SUPERIOR: título del listado y botón de nuevo usuario
         ================================================ -->

    <div class="top-bar">
        <h2>Listado de Usuarios</h2>

        <!-- "onclick='openModal(\"create\")'" = llama a la función JavaScript
             openModal() pasando el modo 'create'.
             La función abre el modal en modo creación (formulario vacío).
             openModal() está definida en public/js/usuarios.js. -->
        <button class="btn btn-primary" onclick="openModal('create')">+ Nuevo Usuario</button>
    </div>


    <!-- ================================================
         TABLA DE USUARIOS
         "table-responsive" = div contenedor que agrega scroll horizontal
         en pantallas pequeñas para que la tabla no se rompa.
         ================================================ -->
    <div class="table-responsive">
        <table>
            <thead>
                <!-- "<tr>" = table row: fila de la tabla.
                     "<th>" = table header: celda de encabezado (negrita y centrada por defecto). -->
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php
                // "foreach($usuarios as $u)" = itera sobre el arreglo $usuarios.
                // En cada iteración, $u contiene los datos de un usuario (arreglo asociativo).
                // Por cada usuario se genera una fila <tr> en la tabla.
                foreach($usuarios as $u): ?>

                <tr>
                    <!-- "<strong>" = texto en negrita — destaca el ID visualmente. -->
                    <td><strong><?= $u['id_usuario']; ?></strong></td>

                    <!-- Se concatenan primer_nombre y primer_apellido con un espacio.
                         "." = operador de concatenación de PHP. -->
                    <td><?= $u['primer_nombre']." ".$u['primer_apellido']; ?></td>

                    <td><?= $u['numero_de_cedula']; ?></td>
                    <td><?= $u['correo_electronico']; ?></td>

                    <td>
                        <!-- "class='badge'" = etiqueta visual para mostrar el rol.
                             "ucfirst()" = función PHP que pone en mayúscula la primera letra.
                             Convierte 'administrador' → 'Administrador',
                             'recepcionista' → 'Recepcionista'. -->
                        <span class="badge"><?= ucfirst($u['rol']); ?></span>
                    </td>

                    <td>
                        <!-- BOTÓN EDITAR:
                             "onclick='openModal(\"edit\", ...data...)'" = llama a openModal
                             en modo 'edit' pasando todos los datos del usuario como objeto JS.

                             "json_encode($u)" = convierte el arreglo PHP $u a una cadena JSON.
                             Resultado ejemplo: {"id_usuario":1,"primer_nombre":"Juan",...}
                             JavaScript recibirá ese JSON como objeto y accederá a data.primer_nombre, etc.

                             "htmlspecialchars(..., ENT_QUOTES, 'UTF-8')" = escapa caracteres especiales
                             en el JSON para que sean seguros dentro de un atributo HTML onclick:
                             ENT_QUOTES = escapa tanto comillas simples (') como dobles (").
                             'UTF-8' = codificación de caracteres a usar.
                             Sin esto, un usuario llamado O'Brien rompería el atributo onclick. -->
                        <button class="btn btn-warning"
                                onclick="openModal('edit', <?= htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8') ?>)">
                            Editar
                        </button>

                        <!-- ENLACE ELIMINAR:
                             "href='...?eliminar=ID'" = envía el ID del usuario al controlador por GET.
                             El controlador detecta: if(isset($_GET['eliminar'])) { ... }

                             "onclick='return confirm(...)'" = muestra una ventana de confirmación
                             nativa del navegador ANTES de navegar al enlace.
                             "confirm()" = función JS que muestra un diálogo con "Aceptar"/"Cancelar".
                             Si el usuario hace clic en "Aceptar": confirm() retorna true → return true
                             → el enlace navega y se ejecuta la eliminación.
                             Si hace clic en "Cancelar": confirm() retorna false → return false
                             → el onclick cancela la navegación del enlace. -->
                        <a href="../../../controllers/usuarioController.php?eliminar=<?= $u['id_usuario']; ?>"
                           class="btn btn-danger"
                           onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
                            Eliminar
                        </a>
                    </td>
                </tr>

                <?php endforeach; ?>
                <!-- "endforeach" = cierra el bloque foreach abierto con "foreach(...):".
                     Sintaxis alternativa PHP equivalente a usar llaves: foreach() { } -->
            </tbody>
        </table>
    </div>
    <!-- Fin de .table-responsive -->

</div>
<!-- Fin de .container -->


<!-- ================================================
     MODAL: formulario reutilizable para CREAR y EDITAR usuarios.
     Un solo modal sirve para ambas operaciones:
     - En modo 'create': formulario vacío, acción = crear_usuario.
     - En modo 'edit':   formulario con datos precargados, acción = editar_usuario.
     La función openModal() en usuarios.js configura el modo correcto.
     ================================================ -->

<!-- "id='userModal'" = identificador único para que JavaScript pueda encontrar
     este elemento con document.getElementById('userModal').
     "class='modal'" = estilizado en styleCRUDUsuarios.css como overlay oscuro
     que cubre toda la pantalla cuando está visible. -->
<div class="modal" id="userModal">

    <!-- "class='modal-content'" = contenedor blanco centrado dentro del overlay.
         El modal está oculto por defecto (display:none en CSS).
         JavaScript lo muestra con modal.style.display = 'flex'. -->
    <div class="modal-content">

        <!-- "id='modalTitle'" = JavaScript cambia este texto según el modo:
             'Nuevo Usuario' para crear, 'Editar Usuario' para editar. -->
        <h3 id="modalTitle">Nuevo Usuario</h3>
        <hr>

        <!-- El formulario envía a usuarioController.php por POST.
             "id='userForm'" = JavaScript llama form.reset() para limpiarlo en modo create. -->
        <form id="userForm" action="../../../controllers/usuarioController.php" method="POST">

            <!-- CAMPO OCULTO: ID DEL USUARIO
                 "type='hidden'" = campo que no se muestra al usuario pero se envía con el formulario.
                 En modo crear: queda vacío (no se usa).
                 En modo editar: JavaScript lo llena con data.id_usuario para el UPDATE. -->
            <input type="hidden" name="id_usuario" id="form_id_usuario">

            <!-- CAMPO OCULTO: ACCIÓN
                 JavaScript cambia su valor según el modo:
                 'crear_usuario' → el controlador ejecuta INSERT.
                 'editar_usuario' → el controlador ejecuta UPDATE. -->
            <input type="hidden" name="accion" id="form_accion" value="crear_usuario">


            <!-- FILA 1: NOMBRES -->
            <div class="row">
                <div class="form-group">
                    <label>Primer Nombre</label>
                    <!-- "id='primer_nombre'" = JavaScript precarga el valor en modo edit:
                         document.getElementById('primer_nombre').value = data.primer_nombre; -->
                    <input type="text" name="primer_nombre" id="primer_nombre" required>
                </div>
                <div class="form-group">
                    <label>Segundo Nombre</label>
                    <!-- Sin "required" = campo opcional. -->
                    <input type="text" name="segundo_nombre" id="segundo_nombre">
                </div>
            </div>

            <!-- FILA 2: APELLIDOS -->
            <div class="row">
                <div class="form-group">
                    <label>Primer Apellido</label>
                    <input type="text" name="primer_apellido" id="primer_apellido" required>
                </div>
                <div class="form-group">
                    <label>Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" id="segundo_apellido">
                </div>
            </div>

            <!-- FILA 3: CÉDULA Y CELULAR -->
            <div class="row">
                <div class="form-group">
                    <label>Cédula</label>
                    <input type="text" name="numero_de_cedula" id="numero_de_cedula" required>
                </div>
                <div class="form-group">
                    <label>Celular</label>
                    <input type="text" name="numero_de_celular" id="numero_de_celular" required>
                </div>
            </div>

            <!-- CORREO -->
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="correo_electronico" id="correo_electronico" required>
            </div>

            <!-- ROL: selector desplegable -->
            <div class="form-group">
                <label>Rol del Sistema</label>

                <!-- "<select>" = lista desplegable de opciones.
                     "name='rol'" = el controlador lo lee con $_POST['rol'].
                     "required" = debe seleccionarse una opción válida (no la opción vacía). -->
                <select name="rol" id="rol" required>
                    <!-- "value=''" = opción inicial sin valor real.
                         Si el usuario intenta enviar con esta opción, "required" lo bloquea. -->
                    <option value="">Seleccione...</option>
                    <option value="administrador">Administrador</option>
                    <option value="recepcionista">Recepcionista</option>
                </select>
            </div>

            <!-- CONTRASEÑA: campo con comportamiento especial -->
            <div class="form-group">
                <label>Contraseña</label>

                <!-- En modo create: required=true → obligatorio llenar.
                     En modo edit: required=false → opcional (dejar vacío = no cambiar).
                     JavaScript alterna el atributo required según el modo. -->
                <input type="password" name="contrasena" id="contrasena">

                <!-- "<small>" = texto de ayuda pequeño debajo del input.
                     "id='pass_hint'" = JavaScript lo muestra (display:block)
                     solo en modo editar para indicar que la contraseña es opcional.
                     "style='display:none'" = oculto por defecto (modo crear). -->
                <small id="pass_hint" style="display:none; color:#666;">
                    (Dejar en blanco para no cambiar)
                </small>
            </div>

            <!-- BOTONES DEL MODAL -->
            <div class="modal-actions">
                <!-- "type='button'" = botón que NO envía el formulario.
                     "onclick='closeModal()'" = llama a la función de cierre del modal
                     definida en usuarios.js. -->
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    Cancelar
                </button>

                <!-- "type='submit'" = envía el formulario al action (usuarioController.php). -->
                <button type="submit" class="btn btn-primary">
                    Guardar Usuario
                </button>
            </div>

        </form>
        <!-- Fin del formulario del modal. -->

    </div>
    <!-- Fin de .modal-content -->

</div>
<!-- Fin del modal #userModal -->


<!-- ================================================
     CARGA DEL SCRIPT EXTERNO
     La lógica JavaScript está separada en public/js/usuarios.js
     para mantener la vista limpia (separación de responsabilidades).
     Se carga al FINAL del <body> para garantizar que todos los
     elementos HTML ya existen en el DOM cuando el script los busca
     con getElementById(). Si se cargara en <head>, el DOM aún
     no estaría construido y getElementById() retornaría null.
     ================================================ -->
<script src="../../../public/js/usuarios.js"></script>

</body>
</html>