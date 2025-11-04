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
    <div class="container my-4">
        <h1 class="text-center mb-4">Detalle de Promoción</h1>

        <?php
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

        <!--  -->
        <div class="row">
            <div class="col-md-6">
                <h6><i class="bi bi-info-circle"></i> Información de la Promoción</h6>
                <table class="table table-sm">
                    <tr><td><strong>Código:</strong></td><td>#<?= $detalle['codPromo'] ?></td></tr>
                    <tr><td><strong>Texto:</strong></td><td><?= htmlspecialchars($detalle['textoPromo']) ?></td></tr>
                    <tr><td><strong>Categoría:</strong></td><td><?= $detalle['categoriaCliente'] ?></td></tr>
                    <tr><td><strong>Vigencia:</strong></td><td>
                        <?= date('d/m/Y', strtotime($detalle['fechaDesdePromo'])) ?> - 
                        <?= date('d/m/Y', strtotime($detalle['fechaHastaPromo'])) ?>
                    </td></tr>
                </table>
                
                <h6><i class="bi bi-shop"></i> Información del Local</h6>
                <table class="table table-sm">
                    <tr><td><strong>Local:</strong></td><td><?= htmlspecialchars($detalle['nombreLocal']) ?></td></tr>
                    <tr><td><strong>Rubro:</strong></td><td><?= htmlspecialchars($detalle['rubroLocal']) ?></td></tr>
                    <tr><td><strong>Ubicación:</strong></td><td><?= htmlspecialchars($detalle['ubicacionLocal']) ?></td></tr>
                </table>
            </div>

            <div class="col-md-6">
                <h6><i class="bi bi-graph-up"></i> Estadísticas de Uso</h6>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body p-2">
                                <h5 class="mb-0"><?= $aceptadas?></h5>
                                <small>Aceptados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-danger text-white text-center">
                            <div class="card-body p-2">
                                <h5 class="mb-0"><?= $rechazadas ?></h5>
                                <small>Rechazados</small>
                            </div>
                        </div>
                    </div>
                </div>

                
                <h6><i class="bi bi-people"></i> Últimos Usuarios (<?= mysqli_num_rows($resultadoUsuarios) ?>)</h6>
                <div style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm">
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

                                    <span class="badge bg 
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
                </div>
            </div>
        </div>

        <!-- Botón para volver -->
        <div class="text-center mt-4">
            <a href="reportes.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a Reportes
            </a>
        </div>

    </div>

    <?php mysqli_close($conexion); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>