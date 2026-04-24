<?php

// "session_start()" = inicia o reanuda la sesión PHP del usuario.
// Es obligatorio en esta vista para poder LEER la variable
// $_SESSION['error_login'] que el controlador pudo haber guardado.
// Si no se llama session_start(), $_SESSION estaría vacío y nunca
// mostraría el mensaje de error.
session_start();
?>

<!DOCTYPE html>
<!-- "<!DOCTYPE html>" = declaración del tipo de documento.
     Le indica al navegador que este archivo usa HTML5 (la versión actual).
     Debe ser la primera línea del HTML, antes de cualquier etiqueta. -->

<!-- "<html lang='es'>" = etiqueta raíz que envuelve todo el contenido HTML.
     "lang='es'" = atributo que declara el idioma del contenido como español.
     Usado por lectores de pantalla (accesibilidad) y motores de búsqueda. -->
<html lang="es">

<head>
    <!-- "<head>" = sección de metadatos — información SOBRE la página.
         Su contenido no se muestra en pantalla, pero configura el navegador. -->

    <!-- "charset='UTF-8'" = codificación de caracteres del archivo.
         UTF-8 permite mostrar correctamente tildes (á,é,í,ó,ú),
         la ñ, y caracteres especiales de cualquier idioma. -->
    <meta charset="UTF-8">

    <!-- "<title>" = texto que aparece en la pestaña del navegador
         y en los resultados de búsqueda (SEO).
         El pipe "|" es convención para separar el nombre de la página
         del nombre del sistema. -->
    <title>Login | Sistema de Gestión de Citas Médicas</title>

    <!-- "name='viewport'" = configura cómo se muestra la página en dispositivos móviles.
         "width=device-width" = el ancho de la página se ajusta al ancho del dispositivo.
         "initial-scale=1.0" = sin zoom inicial — la página se muestra a escala 1:1.
         Sin esta línea, los móviles mostrarían la página como si fuera escritorio (muy pequeño). -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- "<link rel='stylesheet'>" = vincula un archivo CSS externo a esta página.
         "href='../../../public/css/styleAuth.css'" = ruta relativa al archivo CSS.
         "../../../" = sube 3 niveles desde views/html/auth/ hasta la raíz del proyecto.
         Desglose: auth/ → html/ → views/ → raíz/
         Luego entra a public/css/styleAuth.css para cargar los estilos. -->
    <link rel="stylesheet" href="../../../public/css/styleAuth.css">
</head>

<body>
    <!-- "<body>" = contiene todo el contenido VISIBLE de la página.
         Los estilos de body en styleAuth.css lo configuran como
         contenedor flex centrado con fondo azul. -->

    <!-- "class='login-container'" = div que envuelve todo el formulario de login.
         .login-container en styleAuth.css lo estiliza como tarjeta blanca
         centrada con sombra y ancho fijo de 400px. -->
    <div class="login-container">

        <!-- Título principal del formulario de login.
             Muestra el nombre del sistema como encabezado. -->
        <h1>Sistema de Gestión de Citas Médicas</h1>


        <!-- ================================================
             BLOQUE PHP: MOSTRAR ERROR DE LOGIN (si existe)
             ================================================ -->

        <?php
        // "if(isset($_SESSION['error_login']))" = verifica si existe la clave
        // 'error_login' en la sesión.
        // "isset()" = retorna true si la variable existe Y no es null.
        // Esta clave la crea el controlador cuando las credenciales son incorrectas:
        // $_SESSION['error_login'] = "Correo o contraseña incorrectos.";
        // Si no existe (primer acceso o login exitoso), este bloque se omite completo.
        if(isset($_SESSION['error_login'])): ?>

            <!-- "class='alert-error'" = div estilizado en styleAuth.css
                 con fondo rosado, texto rojo y animación fadeIn. -->
            <div class="alert-error">
                <?php
                // "echo $_SESSION['error_login']" = imprime el texto del mensaje de error
                // que el controlador guardó en la sesión.
                // Ejemplo de lo que imprime: "Correo o contraseña incorrectos."
                echo $_SESSION['error_login'];

                // "unset($_SESSION['error_login'])" = ELIMINA la clave de la sesión
                // inmediatamente después de mostrarla.
                // Sin unset(), el mensaje de error permanecería en sesión y
                // aparecería en CADA recarga de la página hasta que el usuario
                // cierre sesión — lo cual sería confuso y engañoso.
                // Al hacer unset() aquí, el mensaje se muestra UNA SOLA VEZ.
                unset($_SESSION['error_login']);
                ?>
            </div>

        <?php
        // "endif" = cierra el bloque condicional "if" abierto con "if(...):".
        // La sintaxis "if(): ... endif;" es la forma alternativa de PHP para
        // bloques if que contienen HTML — más legible que if() { } cuando
        // se mezcla PHP con HTML.
        endif; ?>


        <!-- ================================================
             FORMULARIO DE LOGIN
             ================================================ -->

        <!-- "action='../../../controllers/usuarioController.php'" =
             URL a la que se enviarán los datos cuando el usuario haga clic en el botón.
             "../../../" sube 3 niveles hasta la raíz y entra a controllers/.
             El controlador usuarioController.php procesará las credenciales.

             "method='POST'" = los datos del formulario se envían en el cuerpo
             de la petición HTTP (no en la URL).
             POST es el método correcto para contraseñas — no las expone en la URL.
             Con GET, la contraseña aparecería visible en la barra de direcciones. -->
        <form action="../../../controllers/usuarioController.php" method="POST">

            <!-- CAMPO: CORREO ELECTRÓNICO -->
            <!-- "class='form-group'" = div contenedor del par label+input.
                 Estilizado en styleAuth.css con margen vertical entre campos. -->
            <div class="form-group">

                <!-- "<label>" = etiqueta visible que describe el campo.
                     Mejora la usabilidad — al hacer clic en el label,
                     el foco se mueve al input correspondiente.
                     Para eso debería tener "for='correo'" y el input "id='correo'",
                     aunque aquí funciona por ser el único input del form-group. -->
                <label>Correo Electrónico</label>

                <!-- "type='email'" = campo de texto especializado para correos.
                     El navegador valida automáticamente que tenga formato de email
                     (que contenga "@" y un dominio) antes de enviar el formulario.
                     "name='correo_electronico'" = nombre del campo en el formulario.
                     El controlador lo leerá con: $_POST['correo_electronico'].
                     "required" = atributo HTML5 que impide enviar el formulario
                     si este campo está vacío. Validación del lado del cliente (frontend). -->
                <input type="email" name="correo_electronico" required>
            </div>

            <!-- CAMPO: CONTRASEÑA -->
            <div class="form-group">
                <label>Contraseña</label>

                <!-- "type='password'" = campo especial de contraseña.
                     El texto escrito se oculta con puntos o asteriscos (●●●●).
                     Esto protege la contraseña de miradas indiscretas.
                     "name='contrasena'" = nombre del campo (sin ñ, igual que el modelo).
                     El controlador lo leerá con: $_POST['contrasena'].
                     "required" = el formulario no se envía si está vacío. -->
                <input type="password" name="contrasena" required>
            </div>

            <!-- BOTÓN DE ENVÍO DEL FORMULARIO -->
            <!-- "type='submit'" = al hacer clic, envía el formulario al action.
                 "name='login'" = nombre del botón — clave para el controlador.
                 El controlador detecta el login con: if(isset($_POST['login']))
                 Si el botón no tuviera "name='login'", el controlador no sabría
                 que fue el formulario de login (y no el de registro) el que se envió.
                 "class='login-btn'" = estilizado en styleAuth.css como botón azul. -->
            <button type="submit" name="login" class="login-btn">
                Iniciar Sesión
            </button>

        </form>
        <!-- Fin del formulario de login. -->


        <!-- ================================================
             TEXTO DE PIE: ENLACE AL REGISTRO
             ================================================ -->

        <!-- "class='footer-text'" = párrafo estilizado en styleAuth.css
             con texto gris pequeño centrado. -->
        <div class="footer-text">
            ¿No tienes cuenta?
            <!-- "href='register.php'" = enlace RELATIVO a register.php.
                 Como register.php está en la MISMA carpeta que login.php
                 (ambos en views/html/auth/), solo se necesita el nombre del archivo.
                 No es necesario escribir la ruta completa "../../../views/html/auth/register.php". -->
            <a href="register.php">Regístrate aquí</a>
        </div>

    </div>
    <!-- Fin de .login-container -->

</body>
</html>

<?php

?>