<?php
include("conexionBD.php");

// Variable para rutas de navegación (breadcrumb)
$breadcrumb_titulo_activo = 'Novedades';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novedades - Descuento City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
<?php include("includes/navbar.php"); ?>

    <!-- Portada -->
    
    <section class="portada position-relative">
        <img src="/assets/img/novedades-portada.png" alt="Portada Novedades"class="portada-img img-fluid">
        <div class="portada-overlay text-center">
            <h1 class="portada-titulo">NOVEDADES</h1>
            <p class="portada-subtitulo"> Mantenete al día con las últimas noticias y actualizaciones de <strong> Descuento City</strong>.</p>
        </div>
    </section>
    
    <!-- Breadcrumb debajo de la portada -->
    <div class="container mt-3">
        <?php include 'includes/breadcrumb.php'; ?>
    </div>

    <!-- Contenido principal -->
    <div class="container my-5">
        <!-- Usuario no registrado - Mensaje de no disponible -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        <h3 class="card-title mt-3">No disponible</h3>
                        <p class="card-text text-muted mb-4">
                            Debes iniciar sesión para ver novedades.
                        </p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="views/auth/login.php" class="btn btn-primary me-md-2">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                            <a href="views/auth/registro.php" class="btn btn-outline-primary">
                                <i class="bi bi-person-plus"></i> Registrarse
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>