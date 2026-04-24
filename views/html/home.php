<?php

session_start();


if(!isset($_SESSION['id_usuario'])){

    header("Location: auth/login.php");

    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Home | Sistema de Gestión de Citas Médicas</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <link rel="stylesheet" href="../../public/css/styleHome.css">
</head>

<body>


    <header>

        <div class="user-info">

            <?php
            
            echo $_SESSION['nombre'];
            ?>

           
            (<?php
            
            echo $_SESSION['rol'];
            ?>)

            
            <a href="../../controllers/logout.php" class="logout-btn">
                
            </a>

        </div>
        

    </header>
   
    
    <div class="container">


        <?php
        
        if($_SESSION['rol'] == 'administrador'): ?>

            
            <div class="card" onclick="location.href='Usuarios/CRUD_Usuarios.php'">

                
                <div class="icon">👤</div>

                <span>Gestión de usuarios del sistema</span>
            </div>

        <?php endif; ?>
      

        <div class="card" onclick="location.href='Pacientes/CRUD_pacientes.php'">
            <div class="icon">🧾</div>
            Pacientes
            <span>Registro y edición de pacientes</span>
        </div>


        <div class="card" onclick="location.href='Medicos/CRUD_Medicos_y_Especialidades.php'">
            <div class="icon">💉</div>
            Médicos y Especialidades
            <span>Asignación y gestión</span>
        </div>


        <div class="card" onclick="location.href='consultorios/consultorio.php'">
            <div class="icon">🏥</div>
            Consultorios
            <span>Gestión por especialidad</span>
        </div>



        <div class="card" onclick="location.href='citas/citas.php'">
            <div class="icon">📅</div>
            Citas Médicas
            <span>Agendar y modificar citas</span>
        </div>



        <div class="card" onclick="location.href='historial_citas/historial_citas.php'">
            <div class="icon">📖</div>
            Historial de Pacientes
            <span>Consultar historial de citas</span>
        </div>


      

        <div class="card" onclick="location.href='reportes/reportes.php'">
            <div class="icon">📊</div>
            Reportes
            <span>Generar PDF y Excel</span>
        </div>

    </div>
   

</body>
</html>

<?php

?>