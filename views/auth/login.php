<?php


session_start();
include("../../conexionBD.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Descuento City</title>
    <link rel="icon" type="image/png" href="assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include("../../includes/header.php");?>
    <!--ruta de navegacion -->
    <div class="container mt-3 small">
            <?php include '../../includes/breadcrumb.php'; ?> 
        </div>

    <div class="main-center">
        <div class="form__container">
            <h2>Inicio Sesion</h2>
            <form action="../../controllers/loginController.php" method="POST">
                <div  class="datos-container">
                    <label>Email</label><br>
                    <input type="email" name="email" class="input-form" placeholder="Email" required ><br> 
                    <div class="mb-3">
            <label for="clave" class="form-label">Contraseña</label>
            <div class="input-group"> 
                <input type="password" 
                       name="clave" 
                       id="clave" 
                       class="form-control" 
                       placeholder="Contraseña" 
                       required>
                
                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="clave">
                    <i class="bi bi-eye-slash-fill"></i> 
                </button>
            </div>
        </div>
                    
                     <div class="mb-3"> 
                        <span class="small text-muted">¿Olvidó su contraseña? </span>
                         <a href="/Descuento-City/views/auth/restablecer-contraseña.php" class="enlace-restablecer">
                                      Click aquí
                         </a>
                     </div>
                </div>
                <input type="submit" name="confirm" value="Iniciar Sesion" class="button-form">
                 <p>¿No tiene cuenta?<a href="/Descuento-City/views/auth/registro.php" class="text-primary">  Crear una
                 </a> </p>
                  
                </form>

            <?php
            // Sistema de alertas categorizado
            if(isset($_SESSION['mensaje_exito'])){
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
                echo "<i class='bi bi-check-circle'></i> ".$_SESSION['mensaje_exito'];
                echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
                echo "</div>";
                unset($_SESSION['mensaje_exito']);
            }
            if(isset($_SESSION['mensaje_error'])){
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
                echo "<i class='bi bi-exclamation-circle-fill'></i> ".$_SESSION['mensaje_error'];
                echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
                echo "</div>";
                unset($_SESSION['mensaje_error']);
            }
            if(isset($_SESSION['mensaje_warning'])){
                echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>";
                echo "<i class='bi bi-exclamation-triangle-fill'></i> ".$_SESSION['mensaje_warning'];
                echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
                echo "</div>";
                unset($_SESSION['mensaje_warning']);
            }
            if(isset($_SESSION['mensaje_info'])){
                echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>";
                echo "<i class='bi bi-info-circle-fill'></i> ".$_SESSION['mensaje_info'];
                echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
                echo "</div>";
                unset($_SESSION['mensaje_info']);
            }
            
            // Compatibilidad con mensaje simple (por si algún controlador aún no está actualizado)
            if(isset($_SESSION["mensaje"])){
                echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>";
                echo "<i class='bi bi-info-circle-fill'></i> " . $_SESSION['mensaje'];
                echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
                echo "</div>";
                unset($_SESSION['mensaje']);
            }
            ?>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-password');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.dataset.target; 
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                // Alternar el atributo 'type' y el ícono
                if (passwordInput.getAttribute('type') === 'password') {
                    passwordInput.setAttribute('type', 'text');
                    icon.classList.remove('bi-eye-slash-fill');
                    icon.classList.add('bi-eye-fill');
                } else {
                    passwordInput.setAttribute('type', 'password');
                    icon.classList.remove('bi-eye-fill');
                    icon.classList.add('bi-eye-slash-fill');
                }
            });
        });
    });
</script>
</body>
</html>


