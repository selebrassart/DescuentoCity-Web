<?php

header("Content-type: application/xls");
header("Content-Disposition: attachment; filename= ReporteGeneralExcel.xls");


?>

<?php

session_start();

include("../../../conexionBD.php");


//Consulto para informacion general

$codDueño = $_SESSION["codUsuario"];

//Total Promos creadas por un dueño
$consultaPromos= "SELECT COUNT(*) as totalPromosCre
                    FROM promociones p 
                    JOIN locales l ON p.codLocal = l.codLocal
                    WHERE l.codUsuario = '$codDueño' AND p.estadoPromo = 'aprobada'";

$resultadoPromos = mysqli_query($conexion,$consultaPromos);
$totalPromos = mysqli_fetch_assoc($resultadoPromos)['totalPromosCre'];

//Total Promos vigentes al dia de hoy 
$consultaLocales = "SELECT COUNT(*) as totalPromosVig 
                        FROM promociones p
                        JOIN locales l on p.codLocal = l.codLocal
                        WHERE l.codUsuario = '$codDueño' 
                        AND p.fechaHastaPromo >= CURDATE()";
$resultadoPromosV =  mysqli_query($conexion,$consultaLocales);
$totalPromosV = mysqli_fetch_assoc($resultadoPromosV)["totalPromosVig"];

//total de promociones usadas  
$consultaSolicitudes = "SELECT COUNT(*) as totalSol
                        FROM solicitudes_descuentos sd
                        JOIN promociones p ON sd.codPromo = p.codPromo 
                        LEFT JOIN locales l ON p.codLocal = l.codLocal
                        WHERE l.codUsuario = '$codDueño' AND sd.estado IN ('aceptada','eliminada')";

$resultadoSolicitudes = mysqli_query($conexion,$consultaSolicitudes);
$totalSolcitudes = mysqli_fetch_assoc($resultadoSolicitudes)["totalSol"];


//Consulta para reporte de uso de descuentos.




?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <title>Reportes Gerenciales</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/imprimir.css" media="print">
</head>
<body>

    <div class="container my-4">
        <h3 class="text-center mb-4">REPORTES</h3>
        
        <!-- Estadísticas generales -->
        <table class="table table-bordered border-primary">        
            <h4>Información general promociones</h4><br>
            <thead>
                <tr style="font-size:12px;">
                    <th>Creadas</th>
                    <th>Vigentes</th>
                    <th>Usadas</th>
                </tr>
            </thead>
            <tr>
                <td>
                    <h5> <?= $totalPromos ?></h5>
                </td>
                <td>
                    <h5> <?= $totalPromosV ?></h5>
                </td>
                <td>
                    <h5> <?= $totalSolcitudes ?></h5>
                </td>
            </tr>
        </table><br>

        <?php
        //paginacion

        $cant_por_pag =5;
        $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;

        if(!$pagina){
            $inicio = 0;
            $pagina=1;
        }
        else{
            $inicio = ($pagina - 1) * $cant_por_pag;
        }



        //Consulta Reportes
        $consultaReporte = "SELECT
                        p.codPromo,
                        p.textoPromo,
                        p.fechaDesdePromo,
                        p.fechaHastaPromo,
                        p.categoriaCliente,
                        p.diasSemana,
                        l.codLocal,
                        COUNT(up.idUso) AS totalUsos
                        FROM uso_promociones up
                        JOIN promociones p ON up.codPromo = p.codPromo /*Utilizo JOIN para unir-relacionar*/
                        JOIN locales l ON p.codLocal = l.codLocal
                        WHERE l.codUsuario =  '$codDueño' AND up.estado = 'aceptada'
                        GROUP BY p.codPromo,p.textoPromo,p.fechaDesdePromo,p.fechaHastaPromo,p.categoriaCliente, l.codLocal  ORDER BY totalUsos DESC";/*Ordeno por cantidad de usos y nombreLocal alfa*/

        
        $resultado = mysqli_query($conexion,$consultaReporte);
        $total_registros = mysqli_num_rows($resultado);
        $total_paginas = ceil($total_registros / $cant_por_pag);

        //consulta Páginada
        $consultaReportePaginada = "SELECT
                        p.codPromo,
                        p.textoPromo,
                        p.fechaDesdePromo,
                        p.fechaHastaPromo,
                        p.categoriaCliente,
                        p.diasSemana,
                        l.codLocal,
                        COUNT(up.idUso) AS totalUsos
                        FROM uso_promociones up
                        JOIN promociones p ON up.codPromo = p.codPromo /*Utilizo JOIN para unir-relacionar*/
                        JOIN locales l ON p.codLocal = l.codLocal
                        WHERE l.codUsuario =  '$codDueño' AND up.estado = 'aceptada'
                        GROUP BY p.codPromo,p.textoPromo,p.fechaDesdePromo,p.fechaHastaPromo,p.categoriaCliente,l.codLocal
                        ORDER BY totalUsos DESC
                        LIMIT $inicio, $cant_por_pag";

        $resultadoReporte = mysqli_query($conexion,$consultaReportePaginada);


        if($resultadoReporte && mysqli_num_rows($resultadoReporte) > 0){
        ?>

        <table class="table table-bordered border-primary">
            <thead>
                <tr style="font-size: 10px;">
                    <th>Promocion</th>
                    <th>Texto</th>
                    <th>Categoria</th>
                    <th>Vigencia</th>
                    <th>Dias disponble</th>            
                    <th>Usos</th>
                </tr>
            </thead>
            <tbody>
                <?php while($reporte = mysqli_fetch_assoc($resultadoReporte)):                             
                    ?>
                    <tr style="font-size: 10px;">
                        <td>
                            #<?=htmlspecialchars($reporte['codPromo']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($reporte['textoPromo']) ?><br>
                        </td>
                        <td>
                            <?= $reporte['categoriaCliente'];?>
                        </td>
                        <td>
                            Desde: <?= date('d/m/Y', strtotime($reporte["fechaDesdePromo"])) ?><br>
                            Hasta: <?= date('d/m/Y', strtotime($reporte["fechaHastaPromo"])) ?>
                        </td>
                        <td> 
                            <small class="text-muted">
                                <?php
                                    //Si esta disponible toda la semana.
                                    if(strlen($reporte["diasSemana"]) != 54){
                                        echo $reporte["diasSemana"];
                                    }
                                    else{
                                        echo "Todos los días";
                                    }
                                ?>
                            </small>
                        </td>
                        <td>
                            <?= $reporte["totalUsos"] ?> usos
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php
        }
        else{
            ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> No hay datos de promociones aceptadas para mostrar.
            </div>
            <?php
        }

        mysqli_free_result($resultadoReporte);

        mysqli_close($conexion);?>

        <div class='paginacion mt-3'>
            <?php
            for($i = 1; $i <= $total_paginas; $i++){
                if($pagina == $i){
                    echo "<span class='current'>$i</span> ";
                } else {
                    echo "<a href='dueñoReporte.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1'>$i</a>";
                }
            }
            ?>
        </div>
        
    </div>
</body>
</html>