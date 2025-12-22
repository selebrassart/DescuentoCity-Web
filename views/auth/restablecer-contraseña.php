<?php

session_start(); 

$breadcrumb_titulo_activo = 'Restablecer contraseña';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Restablecer Contraseña - Descuento City</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <?php include("../../includes/navbar.php");?>

    <!--ruta de navegacion -->
    <div class="container mt-3 small">
        <?php include '../../includes/breadcrumb.php'; ?> 
    </div>

        <div class="container mt-3">
        <?php
        // Sistema de alertas categorizado
        if(isset($_SESSION['mensaje_exito'])){
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
            echo "<i class='bi bi-check-circle'></i> ".$_SESSION['mensaje_exito'];
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
            unset($_SESSION['mensaje_exito']);
        }
        if(isset($_SESSION['mensaje_error'])){
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
            echo "<i class='bi bi-exclamation-circle-fill'></i> ".$_SESSION['mensaje_error'];
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
            unset($_SESSION['mensaje_error']);
        }
        if(isset($_SESSION['mensaje_warning'])){
            echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>";
            echo "<i class='bi bi-exclamation-triangle-fill'></i> ".$_SESSION['mensaje_warning'];
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
            unset($_SESSION['mensaje_warning']);
        }
        if(isset($_SESSION['mensaje_info'])){
            echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>";
            echo "<i class='bi bi-info-circle-fill'></i> ".$_SESSION['mensaje_info'];
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
            unset($_SESSION['mensaje_info']);
        }
        
        // Compatibilidad con mensaje simple
        if(isset($_SESSION["mensaje"])){
            echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>";
            echo "<i class='bi bi-info-circle-fill'></i> " . $_SESSION['mensaje'];
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
            unset($_SESSION['mensaje']);
        }
        ?>
    </div>


    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Restablecer contraseña</h1>

                <form action="/controllers/contraseñaController.php" method="POST" class="p-4 border rounded shadow-sm bg-white">
                    
                    <div class="mb-3">
                        <label for="clave" class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" name="email" id="email" placeholder="tu@email.com" required>
                        </div>
                    </div>
                    
                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-primary btn-sm" name="enviar">Enviar</button>
                    </div>

                    <div class="d-grid mb-2">
                        <a href="login.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i>Volver</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <?php include("../../includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
