<?php

header("Content-type: application/xls");
header("Content-Disposition: attachment; filename= ReporteDetallesExcel.xls");        
include("../../../conexionBD.php");
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Detalles Reporte - Dueño</title>
</head>
<body>
    <div class="container my-4">
        <h3 class="text-center mb-4">Detalle de Promoción</h3><br>

        <?php

        $codPromo = $_GET['codPromo'] ?? '';
        $codLocal = $_GET['codLocal'] ?? '';
        
        if (empty($codPromo)) {
            echo '<div class="alert alert-danger">Parámetros requeridos no encontrados</div>';
            exit;
        }

        $consultaDetalle = "SELECT 
                        p.*

                        FROM promociones p
                        JOIN locales l ON p.codLocal = l.codLocal
                        WHERE p.codPromo = '$codPromo'";

        $resultadoDetalle = mysqli_query($conexion, $consultaDetalle);
        $detalle = mysqli_fetch_assoc($resultadoDetalle);

        if (!$detalle) {
            echo '<div class="alert alert-danger">Promoción no encontrada</div>';
            exit;
        }



        //Consulto total de solicitudes aceptas de promo especifica
        $consultaAceptadas = "SELECT COUNT(*) as totalAceptadas FROM solicitudes_descuentos WHERE estado = 'aceptada' AND codPromo='$codPromo'";
        $resultadoAceptadas = mysqli_query($conexion,$consultaAceptadas);
        $aceptadas = mysqli_fetch_assoc($resultadoAceptadas)["totalAceptadas"];

        //Consulto total de solicitudes rechazadas de promo especifica
        $consultaRechazadas = "SELECT COUNT(*) as totalRechazadas FROM solicitudes_descuentos WHERE estado = 'rechazada' AND codPromo='$codPromo'";
        $resultadoRechazadas = mysqli_query($conexion,$consultaRechazadas);
        $rechazadas = mysqli_fetch_assoc($resultadoRechazadas)["totalRechazadas"];

        //Consulto total de solicitudes para una promo 
        $consultaTotal = "SELECT COUNT(*) as totalSolicitudes FROM solicitudes_descuentos WHERE codPromo='$codPromo'";
        $resultadoTotal = mysqli_query($conexion,$consultaTotal);
        $totalUsos = mysqli_fetch_assoc($resultadoTotal)["totalSolicitudes"];
   
        $consultaUsuarios = "SELECT 
                        u.nombreUsuario,
                        up.fechaUsoPromo,
                        up.estado
                        FROM uso_promociones up
                        JOIN usuarios u ON up.codCliente = u.codUsuario
                        WHERE up.codPromo = '$codPromo'
                        ORDER BY up.fechaUsoPromo DESC
                        LIMIT 10";
            
        $resultadoUsuarios = mysqli_query($conexion, $consultaUsuarios);
        
        ?>

        <!--  -->

        <table class="table table-bordered border-primary" style="font-size:12px;">
            <tr><td>Código:</td><td>#<?= $detalle['codPromo'] ?></td></tr>
            <tr><td>Texto:</td><td><?= htmlspecialchars($detalle['textoPromo']) ?></td></tr>
            <tr><td>Categoría:</td><td><?= $detalle['categoriaCliente'] ?></td></tr>
            <tr><td>Vigencia:</td><td>
                <?= date('d/m/Y', strtotime($detalle['fechaDesdePromo'])) ?> - 
                <?= date('d/m/Y', strtotime($detalle['fechaHastaPromo'])) ?>
            </td></tr>
            <tr>
                <td>
                   <p>Dias disponible:</p>
                </td>
                <td>                                     
                    <?php
                    //Si esta disponible toda la semana.
                    if(strlen($detalle["diasSemana"]) != 54){
                        
                        echo $detalle["diasSemana"];
                    }
                    else{
                        echo "Todos los dias";
                    }
                    ?>
                </td>
            </tr>
        </table><br>

        <h3>Estadisitica de usos</h3><br>

        <p style="font-size:12px">
            Total solicitudes : <?= $totalUsos?><br>
            Total Aceptados : <?= $aceptadas?><br>
            Total rechazados : <?= $rechazadas ?><br>
        </p>

        <h4>Últimos Usuarios (<?= mysqli_num_rows($resultadoUsuarios) ?>)</h4>

        <table class="table table-bordered border-primary" style="font-size:12px">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($resultadoUsuarios) > 0) {
                    while($usuario = mysqli_fetch_assoc($resultadoUsuarios)): 
                ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nombreUsuario']) ?></td>
                    <td><?= date('d/m H:i', strtotime($usuario['fechaUsoPromo'])) ?></td>
                    <td>

                        <span class="bg 
                            <?= $usuario['estado'] == 'aceptada' ? 'success' : 'danger' ?>">
                            <?= ucfirst($usuario['estado']) ?>
                        </span>
                    </td>
                </tr>
                <?php 
                    endwhile;
                } else {
                    echo '<tr><td colspan="4" class="text-center text-muted">No hay registros de uso</td></tr>';
                }
                ?>
            </tbody>
        </table>


    <?php mysqli_close($conexion); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>