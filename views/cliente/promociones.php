<?php

session_start();

// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] !== 'cliente') {
    header('Location: ../../views/auth/login.php');
    exit();
}

include("../../conexionBD.php");

require("../../funciones/funcionesCliente.php");
require("../../funciones/funcionesSQL.php");

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Variable para breadcrumb
$breadcrumb_titulo_activo = 'Promociones';

$hoy = date('Y-m-d');

$dias_en_español = [
    'Monday' => 'lunes',
    'Tuesday' => 'martes',
    'Wednesday' => 'miércoles',
    'Thursday' => 'jueves',
    'Friday' => 'viernes',
    'Saturday' => 'sábado',
    'Sunday' => 'domingo',
];

$dia_semana_server = date('l'); // Servidor devuelve dias en ingles.

$dia_semana = $dias_en_español[$dia_semana_server]; //Realizo conversion. Para comparar con los datos de la BD.


$codCliente = $_SESSION["codUsuario"];

// Obtener la categoría del cliente logueado
$consulta_cliente = "SELECT categoriaCliente FROM usuarios WHERE codUsuario = '$codCliente'";
$resultado_cliente = mysqli_query($conexion, $consulta_cliente);


$cliente = mysqli_fetch_assoc($resultado_cliente);

$categoria_cliente = $cliente['categoriaCliente'];

// Obtener filtros de formulario
$localSeleccionado = $_POST['local'] ?? '';
$categoriaSeleccionada = $_POST['categoria'] ?? '';

//Obtengo alcance de la categoria cliente.
$categorias_permitidas = verificarCategoria($categoria_cliente);

// Consultas auxiliares para llenar los selects de filtros
$sql_locales_filter = "SELECT DISTINCT l.codLocal, l.nombreLocal 
                      FROM promociones p
                      JOIN locales l ON p.codLocal = l.codLocal
                      WHERE p.estadoPromo = 'aprobada' AND l.estadoLocal = 'activo'
                      ORDER BY l.nombreLocal ASC";
$resultado_locales_filter = mysqli_query($conexion, $sql_locales_filter);

$sql_categorias_filter = "SELECT DISTINCT categoriaCliente 
                         FROM promociones 
                         WHERE estadoPromo = 'aprobada' AND categoriaCliente IS NOT NULL
                         ORDER BY categoriaCliente ASC";
$resultado_categorias_filter = mysqli_query($conexion, $sql_categorias_filter);

//Creo la condicion. Con implode puedo convertir el array en cadena.
$condicion_categorias = "'" . implode("','", $categorias_permitidas) . "'";


$consultaPromos = "SELECT p.*, l.nombreLocal, l.rubroLocal, i.rutaArchivo /*Selecciono todos los campos promociones*/ 
            FROM promociones p /*Obtengo datos de tabla promociones*/ 
            JOIN locales l ON p.codLocal = l.codLocal /* Comparap con l.codLocal para obtener datos*/
            LEFT JOIN imagenes i ON i.idIdentidad = p.codPromo AND i.tipoImg = 'portada' /*Uno con imagenes*/

            WHERE p.estadoPromo = 'aprobada' 
            AND l.estadoLocal = 'activo'
            AND '$hoy' BETWEEN p.fechaDesdePromo AND p.fechaHastaPromo
            AND (p.diasSemana LIKE '%$dia_semana%' OR p.diasSemana = '' OR p.diasSemana IS NULL)
            AND (p.categoriaCliente IN ($condicion_categorias) OR p.categoriaCliente IS NULL OR p.categoriaCliente = '')";

// Agregar filtros si están seleccionados
if (!empty($localSeleccionado)) {
    $consultaPromos .= " AND p.codLocal = '$localSeleccionado'";
}
if (!empty($categoriaSeleccionada)) {
    $consultaPromos .= " AND p.categoriaCliente = '$categoriaSeleccionada'";
}

$consultaPromos .= " ORDER BY p.fechaDesdePromo DESC";

$resultado_promos = mysqli_query($conexion, $consultaPromos);

// Verificar si hay error en la consulta
if (!$resultado_promos) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
<div class="main-content">
<?php include("../../includes/navbar.php"); ?>

<!-- Portada -->
    <section class="portada position-relative">
        <img src="/assets/img/promociones-portada.png" alt="Portada Promociones"class="portada-img img-fluid">
        <div class="portada-overlay text-center">
            <h1 class="portada-titulo">PROMOCIONES</h1>
            <p class="portada-subtitulo"> Mantenete al día con las últimas noticias y actualizaciones de <strong>Descuento City</strong>.</p>
        </div>
    </section>
<!-- Breadcrumb debajo de la portada -->
    <div class="container mt-3">
        <?php include '../../includes/breadcrumb.php'; ?>
    </div>

<div class="container my-4">

    <?php
    //Mensajes
    if(isset($_SESSION['mensaje_exito'])){
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
        echo "<i class='bi bi-check-circle'></i> ".$_SESSION['mensaje_exito'];
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        echo "</div>";
        unset($_SESSION['mensaje_exito']);
    }
    if(isset($_SESSION['mensaje_error'])){
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
        echo "<i class='bi bi-exclamation-circle-fill'></i> ".$_SESSION['mensaje_error'];
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        echo "</div>";
        unset($_SESSION['mensaje_error']);
    }
    if(isset($_SESSION['mensaje_warning'])){
        echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>";
        echo "<i class='bi bi-exclamation-triangle-fill'></i> ".$_SESSION['mensaje_warning'];
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        echo "</div>";
        unset($_SESSION['mensaje_warning']);
    }
    if(isset($_SESSION['mensaje_info'])){
        echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>";
        echo "<i class='bi bi-info-circle-fill'></i> ".$_SESSION['mensaje_info'];
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        echo "</div>";
        unset($_SESSION['mensaje_info']);
    }
    ?>

    <?php
    $resultado = subirCategoria($codCliente, $conexion);

    if($resultado['actualizado']) { 
        // Obtener estilos para categorías
        $estilo_anterior = devolverCategoriaEstilo($resultado['categoria_anterior']);
        $estilo_nueva = devolverCategoriaEstilo($resultado['categoria_nueva']);
        ?>
        <div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>¡Felicitaciones!</strong> Has subido de 
            <span class='badge <?= $estilo_anterior['badge_class'] ?>'>
                <i class="<?= $estilo_anterior['icon'] ?>"></i> <?= ucfirst($resultado['categoria_anterior']) ?>
            </span> a 
            <span class='badge <?= $estilo_nueva['badge_class'] ?>'>
                <i class="<?= $estilo_nueva['icon'] ?>"></i> <?= ucfirst($resultado['categoria_nueva']) ?>
            </span>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    <?php } ?>

    <!-- Información de categoría del cliente -->

        <div class="alert alert-info border border-primary" role="alert">        
            <?php
            // Solo mostrar total de usos si no hubo actualización de categoría
            if(!$resultado['actualizado']) { ?>
                <div class='mb-2'>
                    <small class='text-muted'>
                        <i class='bi bi-graph-up'></i> Total de usos: 
                        <strong><?= $resultado['total_usos'] ?></strong>
                    </small>
                </div>
            <?php } ?>
            <div class="d-flex align-items-center">
                <?php 
                $estilo_cliente = devolverCategoriaEstilo($categoria_cliente);
                ?>
                <i class="<?= $estilo_cliente['icon'] ?> <?= $estilo_cliente['color_text'] ?> me-2"></i>
                <span>
                    <strong>Tu categoría:</strong> 
                    <span class="<?= $estilo_cliente['color_text'] ?>"><?= ucfirst($categoria_cliente) ?></span>
                    - Puedes acceder a promociones de nivel: 
                    <strong><?= implode(', ', array_map('ucfirst',$categorias_permitidas)) ?></strong>
                </span>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <form class="row" method="POST">
                <div class="col-md-6 mb-2">
                    <select name="local" class="form-select">
                        <option value="" hidden selected>Buscar por local</option>
                        <option value="">Todos los locales</option>
                        <?php 
                        mysqli_data_seek($resultado_locales_filter, 0); // Reset the result pointer
                        while ($local_filter = mysqli_fetch_assoc($resultado_locales_filter)) { 
                            $selected = ($localSeleccionado == $local_filter['codLocal']) ? 'selected' : '';
                        ?>
                            <option value="<?= $local_filter['codLocal'] ?>" <?= $selected ?>>
                                <?= htmlspecialchars($local_filter['nombreLocal']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-6 mb-2">
                    <select name="categoria" class="form-select">
                        <option value="" hidden selected>Buscar por categoría</option>
                        <option value="">Todas las categorías</option>
                        <?php 
                        mysqli_data_seek($resultado_categorias_filter, 0); // Reset the result pointer
                        while ($categoria_filter = mysqli_fetch_assoc($resultado_categorias_filter)) { 
                            $selected = ($categoriaSeleccionada == $categoria_filter['categoriaCliente']) ? 'selected' : '';
                        ?>
                            <option value="<?= $categoria_filter['categoriaCliente'] ?>" <?= $selected ?>>
                                <?= ucfirst($categoria_filter['categoriaCliente']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="col-12 text-center mt-3">
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="limpiarFiltrosPromos()">
                        <i class="bi bi-arrow-clockwise"></i> Borrar Filtros
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php
        if($resultado_promos && mysqli_num_rows($resultado_promos) > 0){
            while($promo = mysqli_fetch_assoc($resultado_promos)){
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if(!empty($promo["rutaArchivo"])):?>
                        <img src="/<?= htmlspecialchars($promo["rutaArchivo"]) ?>" class="card-img-top img-fluid" alt="portada promocion" style="height: 250px; object-fit: cover; width: 100%;"> 
                        <?php else: ?>
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 250px; width: 100%;">
                                <span class="text-muted"><i class="bi bi-image"></i> Sin portada</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0 flex-grow-1 me-2">
                                    <i class="bi bi-shop"></i> <?= htmlspecialchars($promo['nombreLocal']) ?>
                                </h5>
                                <?php 

                                // Mostrar categoria
                                $categoria_promo = !empty($promo['categoriaCliente']) ? $promo['categoriaCliente'] : 'inicial';
                                $color = '';
                                $icono = '';
                                switch($categoria_promo) {
                                    case 'Premium':
                                        $color = 'bg-warning text-dark';
                                        $icono = 'bi bi-gem';
                                        break;
                                    case 'Medium':
                                        $color= 'bg-info';
                                        $icono = 'bi bi-star-fill';
                                        break;
                                    case 'Inicial':
                                    default:
                                        $color = 'bg-secondary';
                                        $icono = 'bi bi-circle-fill';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $color ?> small">
                                    <i class="<?= $icono ?>"></i> <?= ucfirst($categoria_promo) ?>
                                </span>
                            </div>
                            <h6 class="card-subtitle mb-2 text-muted">
                                <i class="fas fa-tag"></i> <?= htmlspecialchars($promo['rubroLocal']) ?>
                            </h6>
                            <p class="card-text"><?= htmlspecialchars($promo['textoPromo']) ?></p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i> Hasta: <?=$promo['fechaHastaPromo'] ?> 
                                </small>
                            </p>
                            <div class="mt-auto">
                                <form action="../../controllers/promocionesCtrl/usoPromocionController.php" method="POST">
                                    <input type="hidden" name="codPromo" value="<?= $promo['codPromo']?>">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-outline-success" name="usar">
                                            <i class="bi bi-percent"></i> Usar promoción
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    <i class="bi bi-info-circle-fill"></i> 
                    <strong>No hay promociones disponibles para tu categoría (<?= ucfirst($categoria_cliente) ?>) en este momento.</strong><br>
                    <small>Las promociones disponibles para ti son de nivel: <?= implode(', ', array_map('ucfirst', $categorias_permitidas)) ?>.</small><br>
                    <small>Vuelve pronto para ver las últimas ofertas y descuentos.</small>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
</div>

<?php include("../../includes/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    function limpiarFiltrosPromos() {
        // Redirigir a la página sin parámetros POST para limpiar los selects
        window.location.href = window.location.pathname;
    }
</script>
</body>
</html>