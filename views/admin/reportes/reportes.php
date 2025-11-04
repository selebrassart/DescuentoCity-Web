<?php
session_start();

include("../../../conexionBD.php");

include("../../../includes/admin/adminHeader.php");


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
        <h1 class="text-center mb-4">REPORTES</h1>
        
        <!-- Estadísticas generales -->
        <h4><i class="bi bi-info-circle"></i> Información general</h4>
        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <div class="card text-center bg-primary text-white h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-people-fill display-4 mb-2"></i>
                        <h5 class="card-title"><?= $totalClientes ?></h5>
                        <p class="card-text mb-0">Clientes Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success text-white h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-shop display-4 mb-2"></i>
                        <h5 class="card-title"><?= $totalLocales ?></h5>
                        <p class="card-text mb-0">Locales Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-warning text-dark h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-person-badge display-4 mb-2"></i>
                        <h5 class="card-title"><?= $totalDueños ?></h5>
                        <p class="card-text mb-0">Dueños Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info text-white h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="bi bi-percent display-4 mb-2"></i>
                        <h5 class="card-title"><?= $totalPromos ?></h5>
                        <p class="card-text mb-0">Promociones Vigentes</p>
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

            <h2 class="text-center mb-4">Reporte de Uso de Promociones</h2>

            <div>
                <div class="table-responsive">
                    <table class='tabla table table-striped'>
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Promocion</th>
                                <th>Cantidad usos</th>            
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php while($reporte = mysqli_fetch_assoc($resultadoReporte)):                             
                                ?>
                                <tr>
                                    <td>
                                        #<?=htmlspecialchars($reporte['codLocal']) ?><br>
                                        <?= htmlspecialchars($reporte['nombreLocal']) ?><br>
                                        <span class="badge bg-secondary rounded-pill">
                                            <i class="bi bi-tag"></i> <?= htmlspecialchars($reporte['rubroLocal']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        #<?= htmlspecialchars($reporte['codPromo']) ?><br>
                                        <?= htmlspecialchars($reporte['textoPromo']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success fs-6 rounded-pill">
                                            <i class="bi bi-graph-up"></i> <?= $reporte["totalUsos"] ?> usos
                                        </span>
                                    </td>
                                    <td>
                                        <a href="reporteDetalles.php?codLocal=<?= $reporte['codLocal'] ?>&codPromo=<?= $reporte['codPromo'] ?>" 
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
                    echo "<a href='reportes.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1'>$i</a>";
                }
            }
            ?>
        </div>
        
        <a href="#" class="btn btn-success mt-3" onclick="window.print()" title="Imprimir página">
            <i class="bi bi-printer-fill"></i> Imprimir
        </a>
    </div>
</body>
</html>

