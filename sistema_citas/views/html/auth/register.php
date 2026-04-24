<?php

// "session_start()" en línea con la etiqueta de apertura de PHP.
// Es funcionalmente idéntico a escribirlo en líneas separadas.
// Solo hay un espacio antes de "? >" para cerrar inmediatamente el bloque PHP
// sin generar espacios en blanco antes del <!DOCTYPE html>.
session_start();
?>

<!DOCTYPE html>
<!-- Declaración HTML5 — le indica al navegador la versión del lenguaje. -->

<html lang="es">
<!-- "lang='es'" = idioma español — usado por lectores de pantalla y SEO. -->

<head>
    <!-- Sección de metadatos — configura el navegador sin mostrar contenido. -->

    <!-- "charset='UTF-8'" = codificación que soporta tildes, ñ y caracteres especiales. -->
    <meta charset="UTF-8">

    <!-- Título en la pestaña del navegador: "Registro | Sistema..." -->
    <title>Registro | Sistema de Gestión de Citas Médicas</title>

    <!-- Configuración responsive: ancho = dispositivo, sin zoom inicial. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Vincula el archivo CSS de autenticación.
         "../../../" = sube 3 niveles desde views/html/auth/ hasta la raíz.
         Luego entra a public/css/styleAuth.css. -->
    <link rel="stylesheet" href="../../../public/css/styleAuth.css">
</head>

<body>
    <!-- Cuerpo visible de la página.
         El body tiene display:flex en styleAuth.css — centra .register-container. -->

    <!-- Tarjeta contenedora del formulario de registro.
         .register-container en styleAuth.css = fondo blanco, sombra, esquinas redondeadas. -->
    <div class="register-container">

        <!-- Título del formulario — mismo nombre del sistema que en login.php. -->
        <h1>Sistema de Gestión de Citas Médicas</h1>


        <!-- ================================================
             BLOQUE 1: ALERTA DE ERROR
             Se muestra si el controlador detectó que el correo
             ya está registrado en la base de datos.
             ================================================ -->

        <?php
        // "isset($_SESSION['error_register'])" = verifica si el controlador
        // guardó un mensaje de error en la sesión.
        // Esta clave la escribe el controlador cuando el correo ya existe:
        // $_SESSION['error_register'] = "El correo ya está registrado.";
        if(isset($_SESSION['error_register'])): ?>

            <!-- Alerta estilizada en rojo — ver styleAuth.css (.alert-error). -->
            <div class="alert-error">
                <?php
                // Se imprime el mensaje de error guardado por el controlador.
                echo $_SESSION['error_register'];

                // "unset()" = elimina la clave de la sesión inmediatamente
                // después de mostrarla para que no reaparezca al recargar.
                unset($_SESSION['error_register']);
                ?>
            </div>

        <?php endif; ?>


        <!-- ================================================
             BLOQUE 2: ALERTA DE ÉXITO
             Se muestra si el usuario fue registrado correctamente.
             A diferencia de login.php, esta vista tiene DOS alertas
             porque el registro puede terminar en éxito O en error.
             ================================================ -->

        <?php
        // "isset($_SESSION['success_register'])" = verifica si el controlador
        // guardó un mensaje de éxito tras crear el usuario correctamente:
        // $_SESSION['success_register'] = "Usuario registrado correctamente.";
        if(isset($_SESSION['success_register'])): ?>

            <!-- Alerta estilizada en verde — ver styleAuth.css (.alert-success). -->
            <div class="alert-success">
                <?php
                // Se imprime el mensaje de éxito.
                echo $_SESSION['success_register'];

                // Se elimina inmediatamente para no repetirlo al recargar.
                unset($_SESSION['success_register']);
                ?>
            </div>

        <?php endif; ?>


        <!-- ================================================
             FORMULARIO DE REGISTRO
             Envía los datos por POST al controlador de usuarios.
             ================================================ -->

        <!-- "action='../../../controllers/usuarioController.php'" =
             ruta relativa al controlador que procesará el formulario.
             "../../../" sube 3 niveles desde auth/ hasta la raíz del proyecto.

             "method='POST'" = los datos van en el cuerpo de la petición HTTP.
             Obligatorio para contraseñas (no quedan expuestas en la URL). -->
        <form action="../../../controllers/usuarioController.php" method="POST">


            <!-- ================================================
                 FILA 1: NOMBRES (dos columnas lado a lado)
                 ================================================ -->

            <!-- "class='row'" = div con display:flex en styleAuth.css.
                 Los dos .form-group hijos se colocan horizontalmente. -->
            <div class="row">

                <!-- CAMPO: PRIMER NOMBRE (obligatorio) -->
                <div class="form-group">
                    <!-- "for='primer_nombre'" = asocia el label con el input
                         cuyo id sea 'primer_nombre'. Al hacer clic en el label,
                         el cursor se mueve al input correspondiente.
                         Mejora la accesibilidad y usabilidad del formulario. -->
                    <label for="primer_nombre">Primer Nombre</label>

                    <!-- "type='text'" = campo de texto libre de una línea.
                         "name='primer_nombre'" = nombre del campo — el controlador
                         lo leerá con $_POST['primer_nombre'].
                         "required" = el formulario no se envía si está vacío.
                         Validación del lado del cliente (frontend/navegador). -->
                    <input type="text" name="primer_nombre" required>
                </div>

                <!-- CAMPO: SEGUNDO NOMBRE (opcional) -->
                <div class="form-group">
                    <label for="segundo_nombre">Segundo Nombre</label>

                    <!-- Sin "required" = campo opcional.
                         Si el usuario no lo llena, $_POST['segundo_nombre']
                         llegará como '' (cadena vacía) al controlador.
                         El modelo lo guardará como '' o null según el arreglo $data. -->
                    <input type="text" name="segundo_nombre">
                </div>

            </div>
            <!-- Fin de la fila de nombres. -->


            <!-- ================================================
                 FILA 2: APELLIDOS (dos columnas lado a lado)
                 ================================================ -->

            <div class="row">

                <!-- CAMPO: PRIMER APELLIDO (obligatorio) -->
                <div class="form-group">
                    <label for="primer_apellido">Primer Apellido</label>
                    <input type="text" name="primer_apellido" required>
                </div>

                <!-- CAMPO: SEGUNDO APELLIDO (opcional) -->
                <div class="form-group">
                    <label for="segundo_apellido">Segundo Apellido</label>
                    <!-- Sin required = opcional. -->
                    <input type="text" name="segundo_apellido">
                </div>

            </div>
            <!-- Fin de la fila de apellidos. -->


            <!-- ================================================
                 CAMPOS INDIVIDUALES (una columna, ancho completo)
                 ================================================ -->

            <!-- CAMPO: NÚMERO DE CÉDULA (obligatorio) -->
            <div class="form-group">
                <label for="numero_de_cedula">Número de Cédula</label>

                <!-- "type='text'" = se usa text en lugar de number porque
                     las cédulas pueden tener formatos con guiones, letras
                     iniciales o ceros a la izquierda que type='number' perdería.
                     "required" = obligatorio. -->
                <input type="text" name="numero_de_cedula" required>
            </div>

            <!-- CAMPO: CORREO ELECTRÓNICO (obligatorio) -->
            <div class="form-group">
                <label for="correo_electronico">Correo Electrónico</label>

                <!-- "type='email'" = el navegador valida formato de email
                     antes de enviar el formulario.
                     El controlador también verifica si ya está registrado
                     con correoExiste() en el modelo Usuario. -->
                <input type="email" name="correo_electronico" required>
            </div>

            <!-- CAMPO: DIRECCIÓN (opcional) -->
            <div class="form-group">
                <label for="direccion">Dirección</label>

                <!-- Sin required = opcional.
                     El controlador lo maneja con: ':direccion' => $_POST['direccion'] ?? null
                     Si no se envía, llega como '' y se guarda como null en la BD. -->
                <input type="text" name="direccion">
            </div>

            <!-- CAMPO: NÚMERO DE CELULAR (opcional) -->
            <div class="form-group">
                <label for="numero_de_celular">Número de Celular</label>

                <!-- "type='text'" en lugar de type='tel' o type='number'
                     para aceptar formatos con espacios, guiones o código de país
                     (ej: "+57 300 123 4567"). -->
                <input type="text" name="numero_de_celular">
            </div>

            <!-- CAMPO: CONTRASEÑA (obligatorio) -->
            <div class="form-group">
                <label for="contrasena">Contraseña</label>

                <!-- "type='password'" = oculta los caracteres con puntos (●●●●).
                     "name='contrasena'" = sin ñ, igual que el placeholder del modelo.
                     El controlador la hashea con password_hash() antes de guardarla. -->
                <input type="password" name="contrasena" required>
            </div>


            <!-- BOTÓN DE ENVÍO -->
            <!-- "type='submit'" = envía el formulario al action al hacer clic.
                 "name='register'" = nombre clave del botón.
                 El controlador lo detecta con: if(isset($_POST['register']))
                 Sin este name, el controlador no podría distinguir
                 si vino del formulario de registro o de otro formulario.
                 "class='register-btn'" = estilizado en styleAuth.css como botón azul. -->
            <button type="submit" name="register" class="register-btn">
                Registrarse
            </button>

        </form>
        <!-- Fin del formulario de registro. -->


        <!-- ================================================
             TEXTO DE PIE: ENLACE AL LOGIN
             ================================================ -->

        <!-- "class='footer-text'" = texto gris pequeño centrado (styleAuth.css). -->
        <div class="footer-text">
            ¿Ya tienes cuenta?
            <!-- "href='login.php'" = enlace relativo a login.php.
                 Mismo directorio (views/html/auth/) — solo el nombre del archivo.
                 Inverso al enlace de login.php que apunta a register.php. -->
            <a href="login.php">Inicia sesión aquí</a>
        </div>

    </div>
    <!-- Fin de .register-container -->

</body>
</html>

<?php

?>