<?php

session_start();

// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] !== 'cliente') {
    header('Location: ../../views/auth/login.php');
    exit();
}

include("../../conexionBD.php");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <title>Mis Promociones Usadas - Cliente</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
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
    $codCliente = $_SESSION["codUsuario"];

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


    $consultaUsos = "SELECT 
                    up.idUso, 
                    up.codCliente , 
                    up.codPromo , 
                    up.fechaUsoPromo , 
                    up.estado,
                    p.textoPromo,
                    p.fechaDesdePromo,
                    p.fechaHastaPromo,
                    p.categoriaCliente,
                    l.nombreLocal,
                    sd.fecha_solicitud
                    FROM uso_promociones up
                    JOIN promociones p ON up.codPromo = p.codPromo 
                    LEFT JOIN solicitudes_descuentos sd ON up.codCliente = sd.codCliente AND up.codPromo = sd.codPromo
                    LEFT JOIN locales l ON p.codLocal = l.codLocal
                    WHERE up.codCliente = '$codCliente'
                    ORDER BY FIELD(up.estado, 'enviada', 'aceptada', 'rechazada'),
                    up.fechaUsoPromo DESC";


    $resultadoUsos = mysqli_query($conexion,$consultaUsos);


    $total_registros = mysqli_num_rows($resultadoUsos);

    //consulta Páginada
    $consultaUsosPaginada = "SELECT 
                    up.idUso, 
                    up.codCliente , 
                    up.codPromo , 
                    up.fechaUsoPromo , 
                    up.estado,
                    p.textoPromo,
                    p.fechaDesdePromo,
                    p.fechaHastaPromo,
                    p.categoriaCliente,
                    l.nombreLocal,
                    l.codLocal,
                    sd.fecha_solicitud
                    FROM uso_promociones up
                    JOIN promociones p ON up.codPromo = p.codPromo 
                    LEFT JOIN solicitudes_descuentos sd ON up.codCliente = sd.codCliente AND up.codPromo = sd.codPromo
                    LEFT JOIN locales l ON p.codLocal = l.codLocal
                    WHERE up.codCliente = '$codCliente'
                    AND up.estado IN ('enviada','aceptada','rechazada')
                    ORDER BY FIELD(up.estado, 'enviada', 'aceptada','rechazada'),
                    up.fechaUsoPromo DESC
                    LIMIT $inicio,$cant_por_pag";

    $listaUsos = mysqli_query($conexion,$consultaUsosPaginada);

    $total_paginas = ceil($total_registros / $cant_por_pag);

    echo "<div class='table-responsive'>";
    echo "<div class='d-flex justify-content-center align-items-center mb-2'>";
    echo "<div class='badge bg-primary text-white fs-6 px-2 py-1'>";
    echo "</div>";
    echo "</div>";

    echo "<table class='tabla table table-striped'>";
    echo "<caption>Mis uso promociones</caption>";
    echo "<thead><tr>
            <th>ID</th>
            <th>Local</th>
            <th>Promocion</th>            
            <th>Categoria</th>
            <th>Fecha Solicitud</th>
            <th>Fecha uso</th>
            <th>Estado</th>
            <th>Accion</th>
        </tr></thead><tbody>";
    
    if(mysqli_num_rows($listaUsos) <= 0){
        ?>
        <tr>
            <td colspan="8" style="text-align: center; padding: 20px; color: #666; font-style: italic;">
                No hay existen uso de promociones.
            </td>
        </tr>
        <?php
    }
    while($uso = mysqli_fetch_assoc($listaUsos)){
        ?>
        <tr>
            <td> #<?= $uso["idUso"] ?></td>

            <td>
                <?= "#". $uso['codPromo']?>
                <br>
                <span class="fw-bold"><?= htmlspecialchars($uso['nombreLocal']) ?></span>

            </td>

            <td>                        
                <?= "#". $uso['codPromo']?>
                <br>
                <span class="fw-bold"><?= htmlspecialchars($uso['textoPromo']) ?></span>
                <br>
                <small class="text-muted">
                    <i class="bi bi-calendar3"></i> 
                    Válida: <?= date('d/m/Y', strtotime($uso['fechaDesdePromo'])) ?> - 
                    <?= date('d/m/Y', strtotime($uso['fechaHastaPromo'])) ?><br>
                </small>
            </td>
            <td>
                <?php
                $categoria = $uso['categoriaCliente'];
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
                <i class="bi bi-calendar3"></i> 
                <?= date('d/m/Y', strtotime($uso['fecha_solicitud'])) ?> 
            </td>
            <td> 
                <i class="bi bi-calendar3"></i> 
                <?= date('d/m/Y', strtotime($uso['fechaUsoPromo'])) ?> 
            </td>
            <td>
                <?php
                $estado = $uso['estado'];
                $estado_class = '';
                $estado_icon = '';
                switch($estado) {
                    case 'enviada':
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
            <td>
                <form action="../../controllers/promocionesCtrl/eliminarUsoPromoController.php" method="POST" class="d-inline">
                    <input type="hidden" name="idUso" value="<?= $uso['idUso']?>">
                    <input type="hidden" name="codCliente" value="<?= $uso['codCliente']?>"> 
                    <button type="submit" name="eliminar" class="btn btn-secondary btn-sm rounded-circle" title="Eliminar solicitud">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </form>
            </td>

        </tr>
    <?php
    }
    echo "</tbody></table></div>" ;

    mysqli_free_result($listaUsos);

    echo "<div class='paginacion mt-3'>";
    for($i = 1;$i <= $total_paginas;$i++){
        if($pagina == $i){
            echo "<span class='current'>$i</span>";
        }
        else{
            echo "<a href='misUsoPromociones.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1' id='paginacion'>$i</a>";
        }
    }
    echo "</div><br>";

    mysqli_close($conexion);
    ?>
    </div> <!-- Cierre del contenedor principal -->

    <?php include("../../includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>