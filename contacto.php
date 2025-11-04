<?php
session_start();
include("conexionBD.php");

// Variable para breadcrumb
$breadcrumb_titulo_activo = 'Contacto';

// Debug - Puedes descomentar esta línea para ver el contenido de la sesión
// echo "<pre>SESSION: "; var_dump($_SESSION); echo "</pre>";

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Contacto - Descuento City</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>

<?php
// Verificar si el usuario está logueado y qué tipo de usuario es
$usuario_logueado = isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true;
$tipo_usuario = isset($_SESSION['tipoUsuario']) ? $_SESSION['tipoUsuario'] : null;

if ($usuario_logueado && $tipo_usuario) {
    switch ($tipo_usuario) {
        case 'admin':
            include("includes/admin/adminHeader.php");
            break;
        case 'dueño':
            include("includes/dueño/dueñoHeader.php");
            break;
        case 'cliente':
            include("includes/cliente/clienteHeader.php");
            break;
        default:
            include("includes/navbar.php"); 
            break;
    }
} else {
    include("includes/navbar.php"); 
}
?>
    <!--ruta de navegacion -->
    <div class="container mt-3 small">
            <?php include 'includes/breadcrumb.php'; ?> 
    </div>
        


    <?php
    //Mensajes
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
    ?>



    <!-- formulario contacto -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Contacto</h1>

                <form action="controllers/contactoController.php" method="POST" class="p-4 border rounded shadow-sm bg-white">
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">👤</span>
                        <input type="text" class="form-control" name="nombre" placeholder="Nombre completo" aria-label="Nombre" aria-describedby="basic-addon1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="tu@email.com" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mensaje" class="form-label fw-bold">Asunto</label>                        
                        <input type="text" class="form-control" name="asunto" placeholder="Asunto del mensaje" aria-label="asunto" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mensaje" class="form-label fw-bold">Mensaje</label>
                        <textarea class="form-control" name="mensaje" id="mensaje" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" name="enviar">Enviar Mensaje</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include("includes/footer.php"); ?>

    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>