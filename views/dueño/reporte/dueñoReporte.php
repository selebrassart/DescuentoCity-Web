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
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <title>Reportes Gerenciales</title>
    <link rel="icon" type="image/png" href="/Descuento-City/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

    <?php include("../../../includes/dueño/dueñoHeader.php");?>

    <div class="container my-4">
        <h1 class="text-center mb-4">REPORTES</h1>
        
        <!-- Estadísticas generales -->
        <h3><i class="bi bi-info-circle"></i> Información general promociones</h3>
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card text-center bg-primary text-white h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-plus-circle display-4 mb-2"></i>
                        <h5 class="card-title"><?= $totalPromos ?></h5>
                        <p class="card-text mb-0">Creadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center bg-success text-white h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-check-circle display-4 mb-2"></i>
                        <h5 class="card-title"><?= $totalPromosV ?></h5>
                        <p class="card-text mb-0">Vigentes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center bg-warning text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-graph-up display-4 mb-2"></i>
                        <h5 class="card-title"><?= $totalSolcitudes ?></h5>
                        <p class="card-text mb-0">Usadas</p>
                    </div>
                </div>
            </div>
        </div>


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

            <h3 class="text-center">Uso de promociones</h3>

            <div>
                <div class='table-responsive'>
                    <table class='tabla table table-striped'>
                        <thead>
                            <tr>
                                <th>Promocion</th>
                                <th>Vigencia</th>
                                <th>Dias disponble</th>            
                                <th>Usos</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php while($reporte = mysqli_fetch_assoc($resultadoReporte)):                             
                                ?>
                                <tr>
                                    <td>
                                        #<?=htmlspecialchars($reporte['codPromo']) ?><br>
                                        <?= htmlspecialchars($reporte['textoPromo']) ?><br>
                                        <?php
                                        $categoria = $reporte['categoriaCliente'];
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
                                        <span class="badge <?= $badge_class ?> rounded-pill">
                                            <i class="<?= $icon ?>"></i> <?= $categoria ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="badge bg-success-subtle text-success-emphasis rounded-pill mb-1">
                                                <i class="bi bi-calendar-check"></i> Desde: <?= date('d/m/Y', strtotime($reporte["fechaDesdePromo"])) ?>
                                            </div>
                                            <div class="badge bg-danger-subtle text-danger-emphasis rounded-pill">
                                                <i class="bi bi-calendar-x"></i> Hasta: <?= date('d/m/Y', strtotime($reporte["fechaHastaPromo"])) ?>
                                            </div>
                                        </div>
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
                                        <span class="badge bg-success fs-6 rounded-pill">
                                            <i class="bi bi-graph-up"></i> <?= $reporte["totalUsos"] ?> usos
                                        </span>
                                    </td>
                                    <td>
                                        <a href="reporteDetallesDueño.php?codLocal=<?= $reporte["codLocal"] ?>&codPromo=<?= $reporte['codPromo'] ?>" 
                                        class="btn btn-outline-secondary btn-sm rounded-pill px-3" 
                                        title="Inspeccionar reporte promoción">
                                            <i class="bi bi-search"></i> Ver detalles
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
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
        
        <a href="#" class="btn btn-warning btn-sm mt-3" onclick="window.print()" title="Imprimir página">
            <i class="bi bi-printer-fill"></i> Imprimir
        </a>
    </div>
</body>
</html>