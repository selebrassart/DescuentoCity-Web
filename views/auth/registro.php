
<?php
session_start();
// base de datos
include("../../conexionBD.php");

// Variable para breadcrumb
$breadcrumb_titulo_activo = 'Registro';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Registro - Descuento City</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <?php include("../../includes/navbar.php");?>
    
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

    <!-- Formulario de registro -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Registrarse</h1>

                <form action="../../controllers/registroController.php" method="POST" class="p-4 border rounded shadow-sm bg-white">
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="example@gmail.com" aria-label="Email" required>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" name="clave" placeholder="Contraseña" aria-label="Contraseña" required>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" name="claveConfirm" placeholder="Confirmar contraseña" aria-label="Confirmar Contraseña" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de cuenta</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rol" value="cliente" id="cliente" required>
                            <label class="form-check-label" for="cliente">
                                <i class="bi bi-person"></i> Cliente
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rol" value="dueño" id="dueño" required>
                            <label class="form-check-label" for="dueño">
                                <i class="bi bi-shop"></i> Dueño de local
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" name="confirm" class="btn btn-primary btn-lg">Registrarse</button>
                    </div>
                    
                    <div class="text-center">
                        <span class="text-muted">¿Ya estás registrado? </span>
                        <a href="/views/auth/login.php" class="text-primary">Iniciar Sesión</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("../../includes/footer.php"); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



