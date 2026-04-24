<?php

session_start();
?>

<!DOCTYPE html>



<html lang="es">

<head>
    
    <meta charset="UTF-8">

    <title>Login | Sistema de Gestión de Citas Médicas</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../../public/css/styleAuth.css">
</head>

<body>
   
    <div class="login-container">

      
        <h1>Sistema de Gestión de Citas Médicas</h1>



        <?php
   
        if(isset($_SESSION['error_login'])): ?>

          
            <div class="alert-error">
                <?php
              
                echo $_SESSION['error_login'];

               
                unset($_SESSION['error_login']);
                ?>
            </div>

        <?php
        
        endif; ?>


        
        <form action="../../../controllers/usuarioController.php" method="POST">

            
            <div class="form-group">

     
                <label>Correo Electrónico</label>

                <input type="email" name="correo_electronico" required>
            </div>

           
            <div class="form-group">
                <label>Contraseña</label>

                
                <input type="password" name="contrasena" required>
            </div>

           
            <button type="submit" name="login" class="login-btn">
                Iniciar Sesión
            </button>

        </form>
     


       
        <div class="footer-text">
            ¿No tienes cuenta?
            
            <a href="register.php">Regístrate aquí</a>
        </div>

    </div>
   

</body>
</html>

<?php

?>