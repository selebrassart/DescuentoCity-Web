<?php
include("conexionBD.php");

//var para rutas
$breadcrumb_titulo_activo = 'Locales';

//capturo filtros del form POST
$rubroSeleccionado = $_POST['rubro'] ?? '';
$nombreSeleccionado = $_POST['local'] ?? '';


// cONSULTAS AUXILIARES (para llenar los selects)
$sql_rubros = "SELECT DISTINCT rubroLocal FROM locales WHERE estadoLocal = 'activo'";
$resultado_rubros = mysqli_query($conexion, $sql_rubros);

$sql_nombres = "SELECT codLocal, nombreLocal FROM locales WHERE estadoLocal = 'activo'";
$resultado_nombres = mysqli_query($conexion, $sql_nombres);

// CONSULTA PRINCIPAL CON FILTROS 

$sql_locales = "
    SELECT 
        l.codLocal, l.nombreLocal, l.ubicacionLocal, l.rubroLocal, i.rutaArchivo AS logo
    FROM 
        locales l
    LEFT JOIN 
        imagenes i ON i.idIdentidad = l.codLocal AND i.tipoImg = 'logo' AND i.tipoIdentidad = 'local'
    WHERE 
        l.estadoLocal = 'activo'
";


if (!empty($rubroSeleccionado)) {
    $sql_locales .= " AND l.rubroLocal = '{$rubroSeleccionado}'";
}
if (!empty($nombreSeleccionado)) {
    $sql_locales .= " AND l.codLocal = '{$nombreSeleccionado}'";
}

$sql_locales .= " ORDER BY l.nombreLocal ASC";

$resultado_locales = mysqli_query($conexion, $sql_locales);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locales - Descuento City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>

    <?php include("includes/navbar.php"); ?>

    <!-- Portada -->
     <section class="portada position-relative">
        <img src="/assets/img/locales-portada.png" alt="Portada Locales"class="portada-img img-fluid">
        <div class="portada-overlay text-center">
            <h1 class="portada-titulo">LOCALES</h1>
            <p class="portada-subtitulo">Descubrí todas las marcas y rubros de  <strong>Descuento City</strong>.</p>
        </div>
    </section>


    <!-- Breadcrumb debajo de la portada -->
    <div class="container mt-3">
        <?php include 'includes/breadcrumb.php'; ?>
    </div>

    <!-- Buscador de locales -->
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <form class="d-flex" role="search" onsubmit="return false;">
                    <input class="form-control me-2" type="search" id="buscar-locales" onkeyup="filtrarLocales(this.value);" placeholder="Buscar locales..." aria-label="Search"/>
                    <button class="btn btn-outline-primary" type="button" onclick="filtrarLocales(document.getElementById('buscar-locales').value);">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="container my-4">


        <!-- Mensaje de búsqueda -->
        <div id="mensaje-busqueda-locales" class="alert alert-info text-center" style="display: none;">
            <i class="bi bi-search"></i> Mostrando resultados para: <span id="termino-busqueda-locales"></span>
        </div>
        
        <!-- Mensaje de no encontrado -->
        <div id="mensaje-no-encontrado-locales" class="alert alert-warning text-center" style="display: none;">
            <i class="bi bi-exclamation-triangle"></i> No se encontraron locales que coincidan con la búsqueda.
        </div>
        <!--  filtros -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <form class="row" method="POST">
                    <div class="col-md-6 mb-2">
                        <select name="local" class="form-select">
                            <option value="" hidden selected>Buscar por local</option>
                            <option value="">Todos los locales</option>
                            <?php 
                            while ($local = mysqli_fetch_assoc($resultado_nombres)) { 
                                $selected = ($nombreSeleccionado == $local['codLocal']) ? 'selected' : '';
                            ?>
                                <option value="<?= $local['codLocal'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($local['nombreLocal']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <select name="rubro" class="form-select">
                            <option value="" hidden selected>Buscar por rubro</option>
                            <option value="">Todos los rubros</option>
                            <?php 
                            while ($rubro = mysqli_fetch_assoc($resultado_rubros)) { 
                                $selected = ($rubroSeleccionado == $rubro['rubroLocal']) ? 'selected' : '';
                            ?>
                                <option value="<?= $rubro['rubroLocal'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($rubro['rubroLocal']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
            
                    <div class="col-12 text-center mt-3">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="limpiarTodosFiltrosLocales()">
                            <i class="bi bi-arrow-clockwise"></i> Borrar Filtros
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RESULTADOS -->
        <div class="row">
            <?php if ($resultado_locales && mysqli_num_rows($resultado_locales) > 0) { ?>
                <?php while ($local = mysqli_fetch_assoc($resultado_locales)) { ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 bg-light ">
                            <div class="card-header bg-white p-4" style="min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <img src="/<?= $local['logo'] ?: 'assets/img/default-logo.png'; ?>" 
                                    class="img-fluid" alt="<?= $local['nombreLocal']; ?>"
                                    style="max-height: 100px; max-width: 100%; object-fit: contain;">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= $local['nombreLocal']; ?></h5>
                                <p class="card-text text-muted"><i class="bi bi-tag me-1"></i><?= $local['rubroLocal']; ?></p>
                                <p class="card-text"><small class="text-secondary"><i class="bi bi-geo-alt me-1"></i><?= $local['ubicacionLocal']; ?></small></p>
                                <p class="card-text"><small class="text-secondary"><i class="bi bi-upc-scan me-1"></i><?= $local['codLocal']; ?></small></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-info-circle-fill"></i> 
                        <strong>No existen locales en este momento.</strong><br>
                        <small>Vuelve pronto aprovechar sus promociones.</small>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php include("includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        function filtrarLocales(termino) {
            const cards = document.querySelectorAll('.row .col-lg-3, .row .col-md-4, .row .col-sm-6');
            const mensajeBusqueda = document.getElementById('mensaje-busqueda-locales');
            const mensajeNoEncontrado = document.getElementById('mensaje-no-encontrado-locales');
            const terminoBusqueda = document.getElementById('termino-busqueda-locales');
            let encontrados = 0;
            
            // Si el término está vacío, mostrar todos los locales
            if (termino.trim() === '') {
                cards.forEach(card => {
                    card.style.display = 'block';
                });
                mensajeBusqueda.style.display = 'none';
                mensajeNoEncontrado.style.display = 'none';
                return;
            }
            
            // Filtrar locales
            cards.forEach(card => {
                const titulo = card.querySelector('.card-title');
                const rubro = card.querySelector('.card-text');
                const ubicacion = card.querySelector('small');
                
                let textoCompleto = '';
                
                if (titulo) textoCompleto += ' ' + titulo.textContent.toLowerCase();
                if (rubro) textoCompleto += ' ' + rubro.textContent.toLowerCase();
                if (ubicacion) textoCompleto += ' ' + ubicacion.textContent.toLowerCase();
                
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
        
        function limpiarTodosFiltrosLocales() {
            // Limpiar el buscador de texto
            document.getElementById('buscar-locales').value = '';
            filtrarLocales('');
            
            // Redirigir a la página sin parámetros POST para limpiar los selects
            window.location.href = window.location.pathname;
        }
        
        // Auto-limpiar cuando se borre el input
        document.getElementById('buscar-locales').addEventListener('input', function(e) {
            if (e.target.value === '') {
                filtrarLocales('');
            }
        });
    </script>
</body>
</html>
