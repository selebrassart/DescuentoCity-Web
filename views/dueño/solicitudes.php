<?php

session_start();

// Verificar que el usuario esté logueado y sea dueño
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] !== 'dueño') {
    header('Location: ../../views/auth/login.php');
    exit();
}

include("../../conexionBD.php");?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <title>Gestionar Solicitudes - Dueño</title>
    <link rel="icon" type="image/png" href="/Descuento-City/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <?php include("../../includes/navbar.php");?>

    <!-- Mensajes de alerta -->
    <div class="container mt-3">
        <?php
        // Alertas organizadas por tipo
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

    <div class="container mt-4">

    <?php
    
    $codDueño = $_SESSION['codUsuario'];

    //Consulto local del dueño
    $sql_local = "SELECT codLocal, nombreLocal FROM locales WHERE codUsuario = '$codDueño' AND estadoLocal = 'activo' LIMIT 1";
    $resultado_local = mysqli_query($conexion, $sql_local);

    if ($resultado_local && mysqli_num_rows($resultado_local) == 1) {
        $local = mysqli_fetch_assoc($resultado_local);
        $codLocal = $local['codLocal'];
        $nombreLocal = $local['nombreLocal'];
    } else {
        $_SESSION['mensaje_error'] = "No tienes un local activo asignado.";
        header("location:../dueño/dueñoDashboard.php");
        exit();
    }

    // Paginación

    $cant_por_pag =5;
    $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
    if(!$pagina){

    $inicio = 0;
    $pagina=1;

    }
    else{
        $inicio = ($pagina - 1) * $cant_por_pag;
    }


    //consulta solicitudesz
    $consultaSolicitudes = "SELECT 
                            sd.id_solicitud,
                            sd.codCliente,
                            sd.codPromo,
                            sd.fecha_solicitud,
                            sd.estado,
                            u.nombreUsuario,
                            p.textoPromo,
                            p.fechaDesdePromo,
                            p.fechaHastaPromo,
                            p.categoriaCliente
                        FROM solicitudes_descuentos sd
                        JOIN promociones p ON sd.codPromo = p.codPromo
                        JOIN usuarios u ON sd.codCliente = u.codUsuario
                        WHERE p.codLocal = '$codLocal'";


    $resultadoSolicitudes = mysqli_query($conexion,$consultaSolicitudes);
    $total_registros = mysqli_num_rows($resultadoSolicitudes);


    // Consulta paginada
    $consultaSolicitudesPag = "SELECT 
                            sd.id_solicitud,
                            sd.codCliente,
                            sd.codPromo,
                            sd.fecha_solicitud,
                            sd.estado,
                            u.nombreUsuario,
                            p.textoPromo,
                            p.fechaDesdePromo,
                            p.fechaHastaPromo,
                            p.categoriaCliente
                        FROM solicitudes_descuentos sd
                        JOIN promociones p ON sd.codPromo = p.codPromo
                        JOIN usuarios u ON sd.codCliente = u.codUsuario
                        WHERE p.codLocal = '$codLocal' AND
                        sd.estado IN ('aceptada','pendiente','rechazada')
                        ORDER BY FIELD(estado, 'pendiente','aceptada','rechazada'),
                        sd.fecha_solicitud DESC
                        LIMIT $inicio, $cant_por_pag";

    $resultadoPag = mysqli_query($conexion, $consultaSolicitudesPag);

    $total_paginas = ceil($total_registros / $cant_por_pag);




    echo "<div class='table-responsive'>";

    echo "<div class='d-flex justify-content-center align-items-center mb-2'>";
    echo "<div class='badge bg-primary text-white fs-6 px-2 py-1'>";
    echo "<i class='bi bi-shop me-1'></i>" . htmlspecialchars($nombreLocal);
    echo "</div>";
    echo "</div>";

    echo "<table class='tabla table table-striped'>";
    echo "<caption>Solicitudes de clientes</caption>";
    echo "<thead><tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Promocion</th>            
            <th>Categoria</th>
            <th>Fecha Solicitud</th>
            <th>Estado</th>
            <th>Accion</th>
        </tr></thead><tbody>";

        if(mysqli_num_rows($resultadoPag) <= 0){
        ?>
        <tr>
            <td colspan="9" style="text-align: center; padding: 20px; color: #666; font-style: italic;">
                No exiten solicitudes pendientes. 
            </td>
        </tr>
        <?php
        }
        while($solicitud = mysqli_fetch_assoc($resultadoPag)){
            ?>
            <tr>
                <td>#<?= $solicitud["id_solicitud"] ?></td>
                <td>#<?= $solicitud["codCliente"] ?></td>
                <td>                        
                    <?= "#". $solicitud['codPromo']?>
                    <br>
                    <span class="fw-bold"><?= htmlspecialchars($solicitud['textoPromo']) ?></span>
                    <br>
                    <small class="text-muted">
                        <i class="bi bi-calendar3"></i> 
                        Válida: <?= date('d/m/Y', strtotime($solicitud['fechaDesdePromo'])) ?> - 
                        <?= date('d/m/Y', strtotime($solicitud['fechaHastaPromo'])) ?><br>
                    </small>
                </td>
                <td>
                    <?php
                    $categoria = $solicitud['categoriaCliente'];
                    $badge_class = '';
                    $icon = '';
                    switch($categoria) {
                        case 'Premium':
                            $badge_class = 'bg-warning text-dark';
                            $icon = 'bi bi-gem';
                            break;
                        case 'Medium':
                            $badge_class = 'bg-info';
                            $icon = 'bi bi-star-fill';
                            break;
                        case 'Inicial':
                        default:
                            $badge_class = 'bg-secondary';
                            $icon = 'bi bi-circle-fill';
                            break;
                    }
                    ?>
                    <span class="badge <?= $badge_class ?>">
                        <i class="<?= $icon ?>"></i> <?= $categoria ?>
                    </span>
                </td>
                <td>
                    <?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'])) ?>
                </td>
                <td>
                    <?php
                    $estado = $solicitud['estado'];
                    $estado_class = '';
                    $estado_icon = '';
                    switch($estado) {
                        case 'pendiente':
                            $estado_class = 'bg-warning text-dark';
                            $estado_icon = 'bi bi-clock';
                            break;
                        case 'aceptada':
                            $estado_class = 'bg-success';
                            $estado_icon = 'bi bi-check-circle-fill';
                            break;
                        case 'rechazada':
                            $estado_class = 'bg-danger';
                            $estado_icon = 'bi bi-x-circle-fill';
                            break;
                    }
                    ?>
                    <span class="badge <?= $estado_class ?>">
                        <i class="<?= $estado_icon ?>"></i> <?= ucfirst($estado) ?>
                    </span>
                </td>
                <td>
                    <form action="../../controllers/dueñoCtrl/gestionarSolicitudesController.php" method="POST">
                    <!-- Si estado solicitud == pendiente -->
                        <?php if($solicitud['estado'] == 'pendiente' ): ?>
                        <input type="hidden" name="id_solicitud" value="<?= $solicitud['id_solicitud']?>">
                        <button type="submit" name="aceptar" class="button-activar">aceptar</button>
                        <button type="submit" name="rechazar" class="button-eliminar">Rachazar</button>

                    <!-- Si estado solicitud == aceptada -->
                        <?php elseif($solicitud['estado'] == 'aceptada'): ?>

                        <input type="hidden" name="id_solicitud" value="<?= $solicitud['id_solicitud']?>">
                        <button type="submit" name="rechazar" class="button-eliminar">Rachazar</button>
                        <button type="submit" name="eliminar" class="button-tacho"  title="Eliminar solicitud">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        <!-- Si estado solicitud == rechazada -->
                        <?php elseif($solicitud['estado'] == 'rechazada'): ?>
                        <input type="hidden" name="id_solicitud" value="<?= $solicitud['id_solicitud']?>">
                        <button type="submit" name="aceptar" class="button-activar">aceptar</button>
                        <button type="submit" name="eliminar" class="button-tacho" title="Eliminar solicitud">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        <?php endif;?>
                    </form>
                </td>
            </tr>
    <?php
    }
    echo "</tbody></table>";
    echo "</div>";

    mysqli_free_result($resultadoPag);

    mysqli_close($conexion);

    echo "<div class='paginacion mt-3'>";
    for($i = 1;$i <= $total_paginas;$i++){
        if($pagina == $i){
            echo $pagina . "";
        }
        else{
            echo "<a href='solicitudes.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1' id='paginacion'>$i</a>";
        }
    }
    echo "</div><br>";
    ?>
    </div> <!-- Cierre del contenedor principal -->

    <?php include("../../includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>