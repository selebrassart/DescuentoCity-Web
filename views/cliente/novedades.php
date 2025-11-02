<?php
session_start();

include("../../conexionBD.php");

//variable para rutas de navegacion (breadcrumb)
$breadcrumb_titulo_activo = 'Novedades';

date_default_timezone_set('America/Argentina/Buenos_Aires');

$hoy = date('Y-m-d');

$codCliente = $_SESSION["codUsuario"];
$consultoCategoria = "SELECT categoriaCliente FROM usuarios WHERE codUsuario='$codCliente'";
$resultado = mysqli_query($conexion, $consultoCategoria);
$cliente = mysqli_fetch_assoc($resultado);

// Obtener la categoría del cliente
$categoriaCliente = $cliente['categoriaCliente'];

$consultaNovedades = "SELECT 
                    n.codNovedad,
                    n.tituloNovedad,
                    n.textoNovedad,
                    n.fechaDesdeNovedad,
                    n.fechaHastaNovedad,
                    n.categoriaCliente,
                    i.rutaArchivo,
                    i.nombreImg,
                    i.fechaSubida
                    FROM novedades n
                    LEFT JOIN imagenes i ON i.idIdentidad = n.codNovedad 
                        AND i.tipoImg = 'portada' 
                        AND i.tipoIdentidad = 'novedad'
                    WHERE '$hoy' BETWEEN n.fechaDesdeNovedad AND n.fechaHastaNovedad
                        AND n.estado = 'activa'
                        AND n.categoriaCliente = '$categoriaCliente'
                    ORDER BY n.codNovedad DESC";

$resultado_novedades = mysqli_query($conexion, $consultaNovedades );

// Verificar si hay error en la consulta
if (!$resultado_novedades) {
    die("Error en la consulta: " . mysqli_error($conexion));
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novedades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link rel="icon" type="image/png" href="assets/img/logo-ventana/logo-fondo-b-circular.png"/>

</head>
<body>
    <?php include("../../includes/cliente/clienteHeader.php"); ?>
    <div class="position-relative">
        <div class="portada-novedades">
            <img src="/Descuento-City/assets/img/novedades-portada.png" class="img-fluid w-100" alt="Portada Novedades" style="height: 300px; object-fit: cover;">
        </div>
        <div class="breadcrumb-overlay position-absolute top-0 start-0 w-100 text-white p-3">
            <div class="container">
                <?php include '../../includes/breadcrumb.php'; ?>
            </div>
        </div>


<div class="container my-4">

    <!-- Filtros ??? -->
    

    <div class="row">
        <?php
        if($resultado_novedades && mysqli_num_rows($resultado_novedades) > 0){
            while($novedad = mysqli_fetch_assoc($resultado_novedades)){
                ?>

                <div class="card mb-5">
                    <?php if(!empty($novedad["rutaArchivo"])): ?>
                        <img src="/Descuento-City/<?= htmlspecialchars($novedad["rutaArchivo"]) ?>" class="card-img-top" alt="portada novedad" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                            <span class="text-muted"><i class="bi bi-image"></i> Sin portada</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= !empty($novedad['tituloNovedad']) ? htmlspecialchars($novedad['tituloNovedad']) : 'Novedad #' . $novedad['codNovedad'] ?></h5>
                        <p class="card-text"><?= htmlspecialchars($novedad['textoNovedad']) ?></p>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="bi bi-calendar-event"></i> Desde: <?= date('d/m/Y', strtotime($novedad['fechaDesdeNovedad'])) ?> - 
                                <i class="bi bi-calendar-x"></i> Hasta: <?= date('d/m/Y', strtotime($novedad['fechaHastaNovedad'])) ?>
                            </small>
                        </p>
                        <p class="card-text">
                            <small class="text-body-secondary">
                                <?php
                                $categoria = $novedad['categoriaCliente'];
                                $badge_class = '';
                                $icon = '';
                                switch($categoria) {
                                    case 'premium':
                                        $badge_class = 'bg-warning text-dark';
                                        $icon = 'bi bi-gem';
                                        break;
                                    case 'medium':
                                        $badge_class = 'bg-info';
                                        $icon = 'bi bi-star-fill';
                                        break;
                                    case 'inicial':
                                    default:
                                        $badge_class = 'bg-secondary';
                                        $icon = 'bi bi-circle-fill';
                                        break;
                                }
                                ?>
                                <div class="mb-2">
                                    <span class="badge <?= $badge_class ?>">
                                        <i class="<?= $icon ?>"></i> <?= $categoria ?>
                                    </span>
                                </div>
                            </small>
                        </p>
                    </div>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    <i class="bi bi-info-circle-fill"></i> 
                    <strong>No hay novedades disponibles</strong><br>
                    <small>Vuelve pronto para ver las últimas novedades</small>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>