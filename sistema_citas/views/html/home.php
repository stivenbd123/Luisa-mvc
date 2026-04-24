<?php

// "session_start()" = inicia o reanuda la sesión PHP.
// Obligatorio para poder leer $_SESSION['id_usuario'],
// $_SESSION['nombre'] y $_SESSION['rol'] definidos en el login.
session_start();

// --- PROTECCIÓN DE ACCESO (GUARDIA DE AUTENTICACIÓN) ---

// "!isset($_SESSION['id_usuario'])" = verifica si el usuario NO está logueado.
// "!" = operador de negación lógica — invierte el resultado de isset().
// "isset()" = retorna true si la variable existe y no es null.
// "!isset()" = true cuando la variable NO existe (usuario no logueado).
// Si el usuario intenta acceder directamente a home.php sin haber hecho login,
// no tendrá $_SESSION['id_usuario'] y será redirigido al login.
if(!isset($_SESSION['id_usuario'])){

    // "header('Location: auth/login.php')" = redirige al formulario de login.
    // "auth/login.php" = ruta RELATIVA desde views/html/ donde vive home.php.
    // No necesita subir niveles ("../") porque auth/ es subcarpeta de html/.
    header("Location: auth/login.php");

    // "exit()" = detiene la ejecución de PHP inmediatamente.
    // Obligatorio tras header('Location:') para que no se siga
    // ejecutando código ni renderizando HTML después de la redirección.
    exit();
}
// Si llegamos aquí, el usuario SÍ está logueado — continúa cargando la página.
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <!-- Título en la pestaña: "Home | Sistema..." -->
    <title>Home | Sistema de Gestión de Citas Médicas</title>

    <!-- Configuración responsive para dispositivos móviles. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- "../../public/css/styleHome.css" = ruta al CSS del home.
         Sube 2 niveles desde views/html/ hasta la raíz del proyecto:
         html/ → views/ → raíz/
         Luego entra a public/css/styleHome.css. -->
    <link rel="stylesheet" href="../../public/css/styleHome.css">
</head>

<body>

    <!-- ================================================
         ENCABEZADO DE LA PÁGINA
         Barra superior con nombre del sistema, usuario activo y botón de logout.
         ================================================ -->

    <!-- "<header>" = etiqueta semántica HTML5 para encabezados de sección o página.
         Más descriptiva que <div class="header"> para navegadores y lectores de pantalla. -->
    <header>

        <!-- Texto del nombre del sistema mostrado en la barra superior. -->
        Sistema de Gestión de Citas Médicas

        <!-- Área de información del usuario autenticado (nombre, rol y logout). -->
        <div class="user-info">

            <?php
            // Se imprime el nombre del usuario logueado desde la sesión.
            // "$_SESSION['nombre']" = primer nombre guardado en el login:
            // $_SESSION['nombre'] = $usuario['primer_nombre'];
            // Ejemplo de salida: "Juan"
            echo $_SESSION['nombre'];
            ?>

            <!-- Paréntesis y rol se muestran entre el nombre y el botón logout.
                 Ejemplo de salida completa: "Juan (administrador)" -->
            (<?php
            // "$_SESSION['rol']" = rol del usuario ('administrador' o 'recepcionista').
            // Guardado en el login: $_SESSION['rol'] = $usuario['rol'];
            // Este valor se usa también abajo para mostrar u ocultar módulos.
            echo $_SESSION['rol'];
            ?>)

            <!-- Enlace para cerrar sesión.
                 "href='../../controllers/logout.php'" = ruta al script de logout.
                 Sube 2 niveles desde views/html/ hasta la raíz, luego entra a controllers/.
                 logout.php destruirá la sesión y redirigirá al login.
                 "class='logout-btn'" = estilizado en styleHome.css como botón de cierre. -->
            <a href="../../controllers/logout.php" class="logout-btn">
                Cerrar Sesión
            </a>

        </div>
        <!-- Fin de .user-info -->

    </header>
    <!-- Fin del header. -->


    <!-- ================================================
         GRILLA DE TARJETAS DE NAVEGACIÓN
         Cada tarjeta lleva al módulo correspondiente del sistema.
         ================================================ -->

    <!-- "class='container'" = div que organiza las tarjetas en una grilla.
         styleHome.css lo configura como contenedor grid o flex. -->
    <div class="container">


        <!-- ================================================
             TARJETA: USUARIOS (solo para administradores)
             ================================================ -->

        <?php
        // Control de visibilidad por ROL:
        // Solo los usuarios con rol 'administrador' pueden ver y acceder
        // al módulo de gestión de usuarios.
        // Un 'recepcionista' no verá esta tarjeta en absoluto —
        // no es que la vea desactivada, directamente no existe en el HTML.
        // "$_SESSION['rol'] == 'administrador'" = compara el rol en sesión
        // con la cadena 'administrador'.
        if($_SESSION['rol'] == 'administrador'): ?>

            <!-- "onclick='location.href=...'" = al hacer clic en la tarjeta,
                 JavaScript redirige al módulo usando location.href.
                 Es una forma alternativa a usar un enlace <a href="...">,
                 que permite que toda el área de la tarjeta sea clickeable
                 en lugar de solo el texto del enlace.
                 "location.href" = propiedad JavaScript que contiene la URL actual
                 y asignarle un valor equivale a navegar a esa URL. -->
            <div class="card" onclick="location.href='Usuarios/CRUD_Usuarios.php'">

                <!-- "class='icon'" = contenedor del emoji/ícono decorativo de la tarjeta.
                     Los emojis se usan como íconos para evitar dependencias externas
                     (como FontAwesome) y simplificar el código. -->
                <div class="icon">👤</div>

                <!-- Título de la tarjeta. -->
                Usuarios

                <!-- "<span>" = elemento en línea para el subtítulo descriptivo.
                     Al ser inline, necesita CSS para mostrarse como bloque
                     debajo del título (display:block en styleHome.css). -->
                <span>Gestión de usuarios del sistema</span>
            </div>

        <?php endif; ?>
        <!-- Si el rol NO es 'administrador', este bloque no genera HTML. -->


        <!-- ================================================
             TARJETA: PACIENTES (visible para todos los roles)
             ================================================ -->

        <!-- Las tarjetas siguientes no tienen condicional de rol —
             son accesibles tanto para 'administrador' como 'recepcionista'. -->
        <div class="card" onclick="location.href='Pacientes/CRUD_pacientes.php'">
            <div class="icon">🧾</div>
            Pacientes
            <span>Registro y edición de pacientes</span>
        </div>


        <!-- ================================================
             TARJETA: MÉDICOS Y ESPECIALIDADES
             ================================================ -->

        <div class="card" onclick="location.href='Medicos/CRUD_Medicos_y_Especialidades.php'">
            <div class="icon">💉</div>
            Médicos y Especialidades
            <span>Asignación y gestión</span>
        </div>


        <!-- ================================================
             TARJETA: CONSULTORIOS
             ================================================ -->

        <div class="card" onclick="location.href='consultorios/consultorio.php'">
            <div class="icon">🏥</div>
            Consultorios
            <span>Gestión por especialidad</span>
        </div>


        <!-- ================================================
             TARJETA: CITAS MÉDICAS
             ================================================ -->

        <div class="card" onclick="location.href='citas/citas.php'">
            <div class="icon">📅</div>
            Citas Médicas
            <span>Agendar y modificar citas</span>
        </div>


        <!-- ================================================
             TARJETA: HISTORIAL DE PACIENTES
             ================================================ -->

        <div class="card" onclick="location.href='historial_citas/historial_citas.php'">
            <div class="icon">📖</div>
            Historial de Pacientes
            <span>Consultar historial de citas</span>
        </div>


        <!-- ================================================
             TARJETA: REPORTES
             ================================================ -->

        <div class="card" onclick="location.href='reportes/reportes.php'">
            <div class="icon">📊</div>
            Reportes
            <span>Generar PDF y Excel</span>
        </div>

    </div>
    <!-- Fin de .container -->

</body>
</html>

<?php

?>