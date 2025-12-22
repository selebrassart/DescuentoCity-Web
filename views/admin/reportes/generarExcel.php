<?php

header("Content-type: application/xls");
header("Content-Disposition: attachment; filename= ReporteGeneralExcel.xls");
header('Content-Type: text/html; charset=UTF-8');

session_start();

include("../../../conexionBD.php");







//Consulto para informacion general


//Total clientes
$consultaCliente = "SELECT COUNT(*) as totalClientes FROM usuarios WHERE tipoUsuario='cliente' AND estadoUsuario='activo'";
$resultadoCliente = mysqli_query($conexion,$consultaCliente);
$totalClientes = mysqli_fetch_assoc($resultadoCliente)['totalClientes'];

//total Locales
$consultaLocales = "SELECT COUNT(*) as totalLocales FROM locales WHERE estadoLocal ='activo'";
$resultadoLocales = mysqli_query($conexion,$consultaLocales);
$totalLocales = mysqli_fetch_assoc($resultadoLocales)["totalLocales"];

//total dueños

$consultaDueños = "SELECT COUNT(*) as totalDueños FROM usuarios WHERE  tipoUsuario='dueño' AND estadoUsuario='activo'";
$resultadoDueños = mysqli_query($conexion,$consultaDueños);
$totalDueños = mysqli_fetch_assoc($resultadoDueños)["totalDueños"];

//total Promos
$consultaPromos = "SELECT COUNT(*) as totalPromos FROM promociones WHERE fechaHastaPromo >= CURDATE()";
$resultadoPromos= mysqli_query($conexion, $consultaPromos);
$totalPromos = mysqli_fetch_assoc($resultadoPromos)['totalPromos'];

//Consulta para reporte de uso de descuentoss.


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <title>Reportes Gerenciales - Admin</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <div class="container my-4">
        <h1 class="text-center mb-4">REPORTES</h1><br>
        
        <!-- Estadísticas generales -->
        <table class="table table-bordered border-primary" style="font-size:12px;">        
            <h2>Información general</h2><br>
            <h3> Total Activos</h3>
            <thead>
                <tr>
                    <th>Clientes</th>
                    <th>Locales</th>
                    <th>Dueños</th>
                    <th>Promociones</th>
                </tr>
            </thead>
            <tr>
                <td>
                    <?= $totalClientes ?>
                </td>
                <td>
                     <?= $totalLocales ?>
                </td>
                <td>
                    <?= $totalDueños ?>
                </td>
                <td>
                    <?= $totalPromos ?>
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
                        l.codLocal,
                        l.nombreLocal,
                        l.rubroLocal,
                        p.codPromo,
                        p.textoPromo,
                        COUNT(up.idUso) AS totalUsos
                        FROM uso_promociones up
                        JOIN promociones p ON up.codPromo = p.codPromo /*Utilizo JOIN para unir-relacionar*/
                        JOIN locales l ON p.codLocal = l.codLocal
                        WHERE up.estado = 'aceptada'
                        GROUP BY l.codLocal, l.nombreLocal, l.rubroLocal, p.codPromo, p.textoPromo
                        ORDER BY totalUsos DESC, l.nombreLocal";/*Ordeno por cantidad de usos y nombreLocal alfa*/

        
        $resultado = mysqli_query($conexion,$consultaReporte);
        $total_registros = mysqli_num_rows($resultado);
        $total_paginas = ceil($total_registros / $cant_por_pag);

        //consulta Páginada
        $consultaReportePaginada = "SELECT
                            l.codLocal,
                            l.nombreLocal,
                            l.rubroLocal,
                            p.codPromo,
                            p.textoPromo,
                            COUNT(up.idUso) AS totalUsos
                            FROM uso_promociones up
                            JOIN promociones p ON up.codPromo = p.codPromo
                            JOIN locales l ON p.codLocal = l.codLocal
                            WHERE up.estado IN ('aceptada','eliminada')
                            GROUP BY l.codLocal, l.nombreLocal, l.rubroLocal, p.codPromo, p.textoPromo
                            ORDER BY totalUsos DESC, l.nombreLocal
                            LIMIT $inicio, $cant_por_pag";

        $resultadoReporte = mysqli_query($conexion,$consultaReportePaginada);


        if($resultadoReporte && mysqli_num_rows($resultadoReporte) > 0){
            ?>

            <h3 class="text-center mb-4">Uso de Promociones</h3><br>

                    <table class="table table-bordered border-primary" style="font-size:12px;" >
                        <thead>
                            <tr>
                                <th>CodigoLocal</th>
                                <th>Nombre</th>
                                <th>Rubro</th>
                                <th colspan="2">Promocion</th>
                                <th>Cantidad usos</th>            
                            </tr>
                        </thead>
                        <tbody>

                            <?php while($reporte = mysqli_fetch_assoc($resultadoReporte)):                             
                                ?>
                                <tr>
                                    <td>
                                        #<?=htmlspecialchars($reporte['codLocal']) ?><br>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($reporte['nombreLocal']) ?><br>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($reporte['rubroLocal']) ?>
                                    </td>
                                    <td colspan="2">
                                        #<?= htmlspecialchars($reporte['codPromo']) ?><br>
                                        <?= htmlspecialchars($reporte['textoPromo']) ?>
                                    </td>
                                    <td>
                                        <?= $reporte["totalUsos"] ?> usos
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
                    echo "<a href='reportes.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1'>$i</a>";
                }
            }
            ?>
        </div>


    </div>
</body>
</html>

