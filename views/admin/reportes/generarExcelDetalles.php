        
        
<?php

header("Content-type: application/xls");
header("Content-Disposition: attachment; filename= ReporteDetallesExcel.xls");     

include("../../../conexionBD.php");

$codPromo = $_GET['codPromo'] ?? '';
$codLocal = $_GET['codLocal'] ?? '';

if (empty($codPromo) || empty($codLocal)) {
    echo '<div class="alert alert-danger">Parámetros requeridos no encontrados</div>';
    exit;
}

$consultaDetalle = "SELECT 
                p.*,
                l.nombreLocal,
                l.rubroLocal,
                l.ubicacionLocal
                FROM promociones p
                JOIN locales l ON p.codLocal = l.codLocal
                WHERE p.codPromo = '$codPromo' AND l.codLocal = '$codLocal'";

$resultadoDetalle = mysqli_query($conexion, $consultaDetalle);
$detalle = mysqli_fetch_assoc($resultadoDetalle);

if (!$detalle) {
    echo '<div class="alert alert-danger">Promoción no encontrada</div>';
    exit;
}

//Consulto total de solicitudes aceptados
$consultaAceptadas = "SELECT COUNT(*) as totalAceptadas FROM uso_promociones WHERE estado = 'aceptada' AND codPromo='$codPromo'";
$resultadoAceptadas = mysqli_query($conexion,$consultaAceptadas);
$aceptadas = mysqli_fetch_assoc($resultadoAceptadas)["totalAceptadas"];
//Consulto total de solicitudes rechazados
$consultaRechazadas = "SELECT COUNT(*) as totalRechazadas FROM uso_promociones WHERE estado = 'rechazada' AND codPromo='$codPromo'";
$resultadoRechazadas = mysqli_query($conexion,$consultaRechazadas);
$rechazadas = mysqli_fetch_assoc($resultadoRechazadas)["totalRechazadas"];


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



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <title>Detalles Reporte - Admin</title>
</head>
<body>
        <h2>Detalle de Promoción</h2>

        <!--  -->
        <h4>Información de la Promoción</h4>
        <table class="table table-bordered border-primary" style="font-size:12px;">
            <tr><td>Código:</td><td>#<?= $detalle['codPromo'] ?></td></tr>
            <tr><td>Texto:</td><td><?= htmlspecialchars($detalle['textoPromo']) ?></td></tr>
            <tr><td>Categoría:</td><td><?= $detalle['categoriaCliente'] ?></td></tr>
            <tr><td>Vigencia:</td><td>
                <?= date('d/m/Y', strtotime($detalle['fechaDesdePromo'])) ?> -
                <?= date('d/m/Y', strtotime($detalle['fechaHastaPromo'])) ?>
            </td></tr>
        </table>
        <br>
                
        <h4></i> Información del Local</h4>
        <table class="table table-bordered border-primary" style="font-size:12px;">
            <tr><td>Local:</td><td><?= htmlspecialchars($detalle['nombreLocal']) ?></td></tr>
            <tr><td>Rubro:</td><td><?= htmlspecialchars($detalle['rubroLocal']) ?></td></tr>
            <tr><td>Ubicación:</td><td><?= htmlspecialchars($detalle['ubicacionLocal']) ?></td></tr>
        </table><br>

        <h4> Estadísticas de Uso</h4>
        <p style="font-size:12px">
            Aceptados : <?= $aceptadas?><br>
            Rechazados : <?= $rechazadas ?>
        </p>


        <h6> Últimos Usuarios (<?= mysqli_num_rows($resultadoUsuarios) ?>)</h6>
            <table class="table table-bordered border-primary" style="font-size:12px">
                <thead>
                    <tr>
                        <th colspan="2">Usuario</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($resultadoUsuarios) > 0) {
                        while($usuario = mysqli_fetch_assoc($resultadoUsuarios)): 
                    ?>
                    <tr >
                        <td colspan="2"><?= htmlspecialchars($usuario['nombreUsuario']) ?></td>
                        <td><?= date('d/m/Y', strtotime($usuario['fechaUsoPromo'])) ?></td>
                        <td>
                            <?= ucfirst($usuario['estado']) ?>
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

</body>
</html>