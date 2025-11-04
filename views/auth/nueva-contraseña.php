<?php
// Iniciar sesión para poder leer los mensajes del controlador
session_start(); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Nueva Contraseña - Descuento City</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
 <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Nueva contraseña</h1>
                <?php 
            if (isset($_SESSION['mensaje_success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['mensaje_success'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['mensaje_success']); 
            }
            if (isset($_SESSION['mensaje_error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['mensaje_error'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['mensaje_error']);
            }
            if (isset($_SESSION['mensaje_warning'])) {
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">' . $_SESSION['mensaje_warning'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['mensaje_warning']);
            }
            ?>

                <form action="../../controllers/contraseñaController.php" method="POST" class="p-4 border rounded shadow-sm bg-white">
                    
                     <div class="mb-3">
                        <label for="token_code" class="form-label">Codigo de verificación</label>
                        <input type="text" class="form-control" name="token_code" id="token_code" placeholder="Ingrese el código recibido por mail" required>
                    </div>
                   <div class="mb-3">
                     <label for="new_clave" class="form-label">Nueva contraseña</label>
                    <input type="password" class="form-control" name="new_clave" id="new_clave" placeholder="Ingrese nueva contraseña" required>
                   </div>

                   <div class="mb-3">
                     <label for="confirm_clave" class="form-label">Confirmar contraseña</label>
                     <input type="password" class="form-control" name="confirm_clave" id="confirm_clave"  placeholder="Confirme su contraseña" required>
                    </div>
                    
                   
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" name="restablecer">Cambiar contraseña</button>
                    </div>
                   
                </form>
            </div>
        </div>
    </div>

    <?php include("../../includes/footer.php"); ?>
</body>