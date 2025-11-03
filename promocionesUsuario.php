<?php

session_start();

include("conexionBD.php");

//Var rutas de navegacion
$breadcrumb_titulo_activo = 'Promociones';

date_default_timezone_set('America/Argentina/Buenos_Aires');

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

// Obtener filtros de formulario
$localSeleccionado = $_POST['local'] ?? '';
$categoriaSeleccionada = $_POST['categoria'] ?? '';

// Consultas auxiliares para llenar los selects
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

// Consulta principal con filtros
$sql_promos = "SELECT p.*, l.nombreLocal, l.rubroLocal, i.rutaArchivo 
            FROM promociones p
            JOIN locales l ON p.codLocal = l.codLocal
            LEFT JOIN imagenes i ON i.idIdentidad = p.codPromo AND i.tipoImg = 'portada'
            WHERE p.estadoPromo = 'aprobada'
            AND l.estadoLocal = 'activo'
            AND '$hoy' BETWEEN p.fechaDesdePromo AND p.fechaHastaPromo
            AND (p.diasSemana LIKE '%$dia_semana%' OR p.diasSemana = '' OR p.diasSemana IS NULL)";

// Agregar filtros si están seleccionados
if (!empty($localSeleccionado)) {
    $sql_promos .= " AND p.codLocal = '$localSeleccionado'";
}
if (!empty($categoriaSeleccionada)) {
    $sql_promos .= " AND p.categoriaCliente = '$categoriaSeleccionada'";
}

$sql_promos .= " ORDER BY p.fechaDesdePromo DESC";

$resultado_promos = mysqli_query($conexion, $sql_promos);

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
    <title>Promociones - Invitado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link rel="icon" type="image/png" href="assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
<?php include("includes/navbar.php"); ?>

<!-- Portada -->
<div class="portada-promociones">
    <img src="/Descuento-City/assets/img/promociones-portada.png" class="img-fluid w-100" alt="Portada Promociones" style="height: 300px; object-fit: cover;">
</div>

<!-- Breadcrumb debajo de la portada -->
<div class="container mt-3">
    <?php include 'includes/breadcrumb.php'; ?>
</div>

    <!-- Buscador de promociones -->
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <form class="d-flex" role="search" onsubmit="return false;">
                    <input class="form-control me-2" type="search" id="buscar-promos" onkeyup="filtrarPromociones(this.value);" placeholder="Buscar promociones..." aria-label="Search"/>
                    <button class="btn btn-outline-primary" type="button" onclick="filtrarPromociones(document.getElementById('buscar-promos').value);">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

<div class="container my-4">

    <!-- Mensaje de búsqueda -->

    <div id="mensaje-busqueda-promos" class="alert alert-info text-center" style="display: none;">
        <i class="bi bi-search"></i> Mostrando resultados para: <span id="termino-busqueda-promos"></span>
    </div>
    
    <!-- Mensaje de no encontrado -->
    <div id="mensaje-no-encontrado-promos" class="alert alert-warning text-center" style="display: none;">
        <i class="bi bi-exclamation-triangle"></i> No se encontraron promociones que coincidan con la búsqueda.
    </div>    <?php

    // Sistema de alertas categorizado
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
    ?>

    <!-- Filtros -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <form class="row" method="POST">
                <div class="col-md-6 mb-2">
                    <select name="local" class="form-select">
                        <option value="" hidden selected>Buscar por local</option>
                        <option value="">Todos los locales</option>
                        <?php 
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
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="limpiarTodosFiltrosPromos()">
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
                        <img src="<?= htmlspecialchars($promo["rutaArchivo"]) ?>" class="card-img-top img-fluid" alt="portada promocion" style="height: 250px; object-fit: cover; width: 100%;"> 
                        <?php else: ?>
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 250px; width: 100%;">
                                <span class="text-muted"><i class="bi bi-image"></i> Sin portada</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <i class="bi bi-shop"></i> <?= htmlspecialchars($promo['nombreLocal']) ?>
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                <i class="fas fa-tag"></i> <?= htmlspecialchars($promo['rubroLocal']) ?>
                            </h6>
                            <p class="card-text"><?= htmlspecialchars($promo['textoPromo']) ?></p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i> Hasta :<?=$promo['fechaHastaPromo'] ?> 
                                </small>
                            </p>
                            <div class="mt-auto">
                                <form action="controllers/promocionesCtrl/usoPromocionController.php" method="POST">
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
                    <strong>No hay promociones disponibles en este momento.</strong><br>
                    <small>Vuelve pronto para ver las últimas ofertas y descuentos.</small>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    function filtrarPromociones(termino) {
        const cards = document.querySelectorAll('.row .col-md-4');
        const mensajeBusqueda = document.getElementById('mensaje-busqueda-promos');
        const mensajeNoEncontrado = document.getElementById('mensaje-no-encontrado-promos');
        const terminoBusqueda = document.getElementById('termino-busqueda-promos');
        let encontrados = 0;
        
        // Si el término está vacío, mostrar todas las promociones
        if (termino.trim() === '') {
            cards.forEach(card => {
                card.style.display = 'block';
            });
            mensajeBusqueda.style.display = 'none';
            mensajeNoEncontrado.style.display = 'none';
            return;
        }
        
        // Filtrar promociones
        cards.forEach(card => {
            const titulo = card.querySelector('.card-title').textContent.toLowerCase();
            const subtitulo = card.querySelector('.card-subtitle').textContent.toLowerCase();
            const descripcion = card.querySelector('.card-text').textContent.toLowerCase();
            const fecha = card.querySelector('small').textContent.toLowerCase();
            
            const textoCompleto = titulo + ' ' + subtitulo + ' ' + descripcion + ' ' + fecha;
            
            if (textoCompleto.includes(termino.toLowerCase())) {
                card.style.display = 'block';
                encontrados++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Mostrar mensajes apropiados
        if (encontrados > 0) {
            terminoBusqueda.textContent = termino;
            mensajeBusqueda.style.display = 'block';
            mensajeNoEncontrado.style.display = 'none';
        } else {
            mensajeBusqueda.style.display = 'none';
            mensajeNoEncontrado.style.display = 'block';
        }
    }
    
    function limpiarTodosFiltrosPromos() {
        // Limpiar el buscador de texto
        document.getElementById('buscar-promos').value = '';
        filtrarPromociones('');
        
        // Redirigir a la página sin parámetros POST para limpiar los selects
        window.location.href = window.location.pathname;
    }
    
    // Auto-limpiar cuando se borre el input
    document.getElementById('buscar-promos').addEventListener('input', function(e) {
        if (e.target.value === '') {
            filtrarPromociones('');
        }
    });
</script>
</body>
</html>
