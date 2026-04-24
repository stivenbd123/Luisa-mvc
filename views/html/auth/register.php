<?php

session_start();
?>

<!DOCTYPE html>


<html lang="es">


<head>
   
    <meta charset="UTF-8">

    <title>Registro | Sistema de Gestión de Citas Médicas</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="stylesheet" href="../../../public/css/styleAuth.css">
</head>

<body>
   

   
    <div class="register-container">

        
        <h1>Sistema de Gestión de Citas Médicas</h1>


        <?php
      
        if(isset($_SESSION['error_register'])): ?>

            
            <div class="alert-error">
                <?php
               
                echo $_SESSION['error_register'];

               
                unset($_SESSION['error_register']);
                ?>
            </div>

        <?php endif; ?>


        

        <?php
      
        if(isset($_SESSION['success_register'])): ?>

            
            <div class="alert-success">
                <?php
               
                echo $_SESSION['success_register'];

                
                unset($_SESSION['success_register']);
                ?>
            </div>

        <?php endif; ?>


        
        <form action="../../../controllers/usuarioController.php" method="POST">


    
            <div class="row">

               
                <div class="form-group">
                    
                    <label for="primer_nombre">Primer Nombre</label>

               
                    <input type="text" name="primer_nombre" required minlength="2" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]+" title="Debe contener solo letras">
                </div>

              
                <div class="form-group">
                    <label for="segundo_nombre">Segundo Nombre</label>

            
                    <input type="text" name="segundo_nombre" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]*" title="Debe contener solo letras">
                </div>

            </div>
  



            <div class="row">

             
                <div class="form-group">
                    <label for="primer_apellido">Primer Apellido</label>
                    <input type="text" name="primer_apellido" required minlength="2" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]+" title="Debe contener solo letras">
                </div>

            
                <div class="form-group">
                    <label for="segundo_apellido">Segundo Apellido</label>
                
                    <input type="text" name="segundo_apellido" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]*" title="Debe contener solo letras">
                </div>

            </div>
         


           
            <div class="form-group">
                <label for="numero_de_cedula">Número de Cédula</label>

                <input type="text" name="numero_de_cedula" required minlength="6" maxlength="12" pattern="[0-9]+" title="Debe contener solo numeros">
            </div>

            
            <div class="form-group">
                <label for="correo_electronico">Correo Electrónico</label>

                <input type="email" name="correo_electronico" required>
            </div>

            
            <div class="form-group">
                <label for="direccion">Dirección</label>

                <input type="text" name="direccion">
            </div>

            <div class="form-group">
                <label for="numero_de_celular">Número de Celular</label>

                <input type="text" name="numero_de_celular" pattern="[0-9+ ]{7,15}">
            </div>

           
            <div class="form-group">
                <label for="contrasena">Contraseña</label>

                <input type="password" name="contrasena" required minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}" title="Debe tener mínimo 8 caracteres, al menos una letra y un número">
            </div>


     
            <button type="submit" name="register" class="register-btn">
                Registrarse
            </button>

        </form>
        

        <div class="footer-text">
            ¿Ya tienes cuenta?
            <a href="login.php">Inicia sesión aquí</a>
        </div>

    </div>
    

</body>
</html>

<?php

?>