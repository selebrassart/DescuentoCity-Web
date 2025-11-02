<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrar Sesión - Descuento City</title>
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="/Descuento-City/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <?php 
    // Verificar si el usuario está logueado y mostrar el header correspondiente
    if(isset($_SESSION["tipoUsuario"])) {
        switch($_SESSION["tipoUsuario"]) {
            case "cliente":
                include("../../includes/cliente/clienteHeader.php");
                break;
            case "dueño": 
                include("../../includes/dueño/dueñoHeader.php");
                break;
            case "admin":
                include("../../includes/admin/adminHeader.php");
                break;
            default:
                include("../../includes/header.php");
                break;
        }
    } else {
        include("../../includes/header.php");
    }
    ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-box-arrow-right text-warning" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h2 class="card-title mb-4">Cerrar Sesión</h2>
                        
                        <p class="card-text text-muted mb-4">
                            ¿Estás seguro de que deseas cerrar tu sesión?
                        </p>

                        <form action="../../controllers/logoutController.php" method="POST" class="d-grid gap-2">
                            <button type="submit" name="confirm" class="btn btn-danger btn-lg">
                                <i class="bi bi-check-circle"></i> Sí, Cerrar Sesión
                            </button>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("../../includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>