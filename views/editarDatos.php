<?php

session_start();

include("../conexionBD.php");

// Variable para breadcrumb
$breadcrumb_titulo_activo = 'Mi perfil';


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Editar datos - Descuento City</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <?php
    // Verificar si el usuario está logueado y qué tipo de usuario es
    $usuario_logueado = isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true;
    $tipo_usuario = isset($_SESSION['tipoUsuario']) ? $_SESSION['tipoUsuario'] : null;


    if ($usuario_logueado && $tipo_usuario) {
        switch ($tipo_usuario) {
            case 'dueño':
                include("../includes/dueño/dueñoHeader.php");
                break;
            case 'cliente':
                include("../includes/cliente/clienteHeader.php");
                break;
            default:
                include("../includes/navbar.php"); 
                break;
        }
    } else {
        include("../includes/navbar.php"); 
    }
    ?>
        <!--ruta de navegacion -->
        <div class="container mt-3 small">
                <?php include '../includes/breadcrumb.php'; ?> 
        </div>

    <?php

    //recupero datos de formulario de miPerfil
    $codUsuario = $_SESSION["codUsuario"];
    $nombreUsuario = $_SESSION["nombreUsuario"];
    $claveUsuario = $_SESSION["claveUsuario"];

    ?>
        


    <?php
    //Mensajes
    if(isset($_SESSION['mensaje_exito'])){
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
        echo "<i class='bi bi-check-circle'></i> " .$_SESSION['mensaje_exito'];
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
        unset($_SESSION['mensaje_exito']);
    }
    if(isset($_SESSION['mensaje_error'])){
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
        echo "<i class='bi bi-exclamation-circle-fill'></i> " .$_SESSION['mensaje_error'];
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
        unset($_SESSION['mensaje_error']);
    }
    if(isset($_SESSION['mensaje_warning'])){
        echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>";
        echo "<i class='bi bi-exclamation-triangle-fill'></i> " .$_SESSION['mensaje_warning'];
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
        unset($_SESSION['mensaje_warning']);
    }
    if(isset($_SESSION['mensaje_info'])){
        echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>";
        echo "<i class='bi bi-info-circle-fill'></i> " .$_SESSION['mensaje_info'];
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
        unset($_SESSION['mensaje_info']);
    }
    ?>



    <!-- formulario contacto -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Editar datos</h1>

                <form action="../controllers/editarDatosController.php" method="POST" class="p-4 border rounded shadow-sm bg-white">

                    <input type="hidden" name="codUsuario" value="<?=$codUsuario?>">
 
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" name="nombreUsuario" value="<?= $nombreUsuario ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="claveUsuario" class="form-label fw-bold">Contraseña</label>
                        <div class="input-group"> 
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" 
                                   name="claveUsuario" 
                                   id="clave" 
                                   class="form-control"
                                    value="<?= $claveUsuario ?>" >
                            
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="clave">
                                <i class="bi bi-eye-slash-fill"></i> 
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="claveUsuario2" class="form-label fw-bold">Confirmar contraseña</label>
                        <div class="input-group"> 
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" 
                                   name="claveUsuario2" 
                                   id="clave" 
                                   class="form-control"
                                   placeholder="Ingrese nuevamente la contraseña" >
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="miPerfil.php" class="btn btn-secondary me-md-2">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" name="confirm" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    
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