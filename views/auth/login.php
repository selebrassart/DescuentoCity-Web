<?php


session_start();
include("../../conexionBD.php");

// Variable para breadcrumb
$breadcrumb_titulo_activo = 'Iniciar Sesión';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Iniciar Sesión - Descuento City</title>
    <link rel="icon" type="image/png" href="/Descuento-City/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<?php include("../../includes/navbar.php"); ?>
    <!--ruta de navegacion -->
    <div class="container mt-3 small">
            <?php include '../../includes/breadcrumb.php'; ?> 
        </div>

    <!-- Mensajes de alerta -->
    <div class="container mt-3">
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
        
        // Compatibilidad con mensaje simple
        if(isset($_SESSION["mensaje"])){
            echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>";
            echo "<i class='bi bi-info-circle-fill'></i> " . $_SESSION['mensaje'];
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
            echo "</div>";
            unset($_SESSION['mensaje']);
        }
        ?>
    </div>

    <!-- Formulario de login -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Iniciar Sesión</h1>

                <form action="../../controllers/loginController.php" method="POST" class="p-4 border rounded shadow-sm bg-white">
                    
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-bold">E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" name="email" placeholder="tu@email.com" aria-label="Email" aria-describedby="basic-addon1" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-bold">Contraseña</label>
                        <div class="input-group"> 
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
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
                    
                    <div class="mb-3 text-center"> 
                        <span class="small text-muted">¿Olvidó su contraseña? </span>
                        <a href="/Descuento-City/views/auth/restablecer-contraseña.php" class="text-primary">
                            Click aquí
                        </a>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" name="confirm" class="btn btn-primary btn-lg">Iniciar Sesión</button>
                    </div>
                    
                    <div class="text-center">
                        <span class="text-muted">¿No tiene cuenta? </span>
                        <a href="/Descuento-City/views/auth/registro.php" class="text-primary">Crear una</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("../../includes/footer.php"); ?>
    
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


