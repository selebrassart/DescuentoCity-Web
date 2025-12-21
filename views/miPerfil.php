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
    <title>Mi Perfil - Descuento City</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>

    <?php
    // Verificar si el usuario está logueado y qué tipo de usuario es
    $usuario_logueado = isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true;
    $tipo_usuario = isset($_SESSION['tipoUsuario']) ? $_SESSION['tipoUsuario'] : null;
    $codUsuario = $_SESSION["codUsuario"];

    $consultaDatos = "SELECT * FROM usuarios WHERE codUsuario = '$codUsuario'";
    $resultadoDatos = mysqli_query($conexion,$consultaDatos);
    $datos = mysqli_fetch_assoc($resultadoDatos);

    //hash de contraseña
    $hash = password_hash($datos["claveUsuario"], PASSWORD_DEFAULT);

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
        include("includes/navbar.php"); 
    }
    ?>
        <!--ruta de navegacion -->
        <div class="container mt-3 small">
                <?php include '../includes/breadcrumb.php'; ?> 
        </div>
        

    <!-- datos usario -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Mi Perfil</h1>

                <form action="editarDatos.php" method="POST" class="p-4 border rounded shadow-sm bg-white">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <p><?= $datos["nombreUsuario"]?></p>
                        <input type="hidden"  name="nombreUsuario" value="<?= $datos["nombreUsuario"]?>">
                        <input type="hidden" name="codUsuario" value="<?= $codUsuario?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="mensaje" class="form-label fw-bold">Contraseña</label>                        
                        <p><?= $_SESSION["claveUsuario"]?></p>
                        <input type="hidden" name="claveUsuario" value="<?= $_SESSION["claveUsuario"]?>">
                    </div>

                    <div class="mb-3">
                        <label for="mensaje" class="form-label fw-bold">Categoria</label>                        
                        <p><?= $datos["categoriaCliente"]?></p>
                    </div>

                    <div class="mb-3">
                        <label for="mensaje" class="form-label fw-bold">Tipo Usuario</label>                        
                        <p><?= $datos["tipoUsuario"]?></p>
                    </div>

                    <div class="mb-3">
                        <label for="mensaje" class="form-label fw-bold">Fecha Registro</label>                        
                        <p><?= $datos["fechaRegistro"]?></p>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-bg" name="enviar">Editar datos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>