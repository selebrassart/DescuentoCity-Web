<?php

//HOME DECUENTO CITY

include("conexionBD.php");    

include("includes/navbar.php");


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

$sql_locales .= " ORDER BY l.nombreLocal ASC LIMIT 4";

$resultado_locales = mysqli_query($conexion, $sql_locales);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Descuento City</title>
    <link rel="icon" type="image/png" href="assets/img/logo-ventana/logo-fondo-b-circular.png"/>

</head>
<body>


    <!-- CARRUSEL -->

    <div id="carouselExampleIndicators" 
        class="carousel slide" 
        data-bs-ride="carousel" 
        aria-roledescription="Carrusel de imágenes" 
        aria-label="Carrusel principal de Descuento City con promociones y locales"
        tabindex="0">

        <!-- Indicadores accesibles -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
            class="active" aria-current="true" aria-label="Promociones vigentes"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
            aria-label="Locales participantes"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
            aria-label="Ofertas especiales"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3"
            aria-label="Promociones de temporada"></button>
        </div>

        <!-- Imágenes del carrusel -->
        <div class="carousel-inner">
            <div class="carousel-item active" role="group" aria-label="1 de 4">
                <img src="/Descuento-City/assets/img/carrusel/dc.png" class="d-block w-100 carousel-img" alt="Banner de promociones  vigentes en Descuento City">
            </div>

            <div class="carousel-item" role="group" aria-label="2 de 4">
                <img src="/Descuento-City/assets/img/carrusel/40578.png"
                class="d-block w-100 carousel-img"
                alt="Imagen de locales participantes del shopping Descuento City">
            </div>

            <div class="carousel-item" role="group" aria-label="3 de 4">
                <img src="/Descuento-City/assets/img/carrusel/wilsonsalebanner.webp"
                class="d-block w-100 carousel-img"
                alt="Banner de promociones y descuentos Wilson en Descuento City">
            </div>

            <div class="carousel-item" role="group" aria-label="4 de 4">
                <img src="/Descuento-City/assets/img/carrusel/banner.png"
                class="d-block w-100 carousel-img"
                alt="Promociones y eventos actuales de Descuento City">
            </div>
    </div>

    <!-- Controles accesibles -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev" aria-label="Anterior">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next" aria-label="Siguiente">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
    </div>

    <h2 class="fw-bold mb-2 text-center mb-4">NUESTROS LOCALES</h2>


    <!-- Buscador -->
    <div class="container-fluid w-50">
        <form class="d-flex" role="search" onsubmit="return false;">
            <input class="form-control me-2" type="search" id="buscar" onkeyup="filtrarLocales(this.value);" placeholder="Buscar locales..." aria-label="Search"/>
            <button class="btn btn-outline-primary" type="button" onclick="filtrarLocales(document.getElementById('buscar').value);">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    </div>


    <div class="container my-4">
    <!--  filtros -->
    <form class="row mb-4" method="POST">
    
    <div class="col-md-6 mb-2">
        <select name="local" class="form-select">
            <option value="" hidden selected>Buscar por local</option>
            <option value="">Todos los locales</option>
            <?php 
           
            while ($local = mysqli_fetch_assoc($resultado_nombres)) { 
            
                $selected = ($nombreSeleccionado == $local['codLocal']) ? 'selected' : '';
            ?>
            <option value="<?= $local['codLocal'] ?>" <?= $selected ?>>
                <?= $local['nombreLocal'] ?>
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
                    <?= $rubro['rubroLocal'] ?>
                </option>
            <?php } ?>
        </select>
    </div>
       
    <div class="col-12 text-center mt-3">
        <button type="button" class="btn btn-outline-secondary me-2" onclick="limpiarTodosFiltros()">
            <i class="bi bi-arrow-clockwise"></i> Borrar Filtros
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i> Filtrar
        </button>
    </div>
                </form>
            </div>
        </div>
    </div>


    <!--  RESULTADOS LOCALES -->
    <section class="locales" id="locales">        
        <div class="container">
            <!-- Mensaje de búsqueda -->
            <div id="mensaje-busqueda" class="alert alert-info text-center" style="display: none;">
                <i class="bi bi-search"></i> Mostrando resultados para: <span id="termino-busqueda"></span>
            </div>
            
            <!-- Mensaje de no encontrado -->
            <div id="mensaje-no-encontrado" class="alert alert-warning text-center" style="display: none;">
                <i class="bi bi-exclamation-triangle"></i> No se encontraron locales que coincidan con la búsqueda.
            </div>
            
        <?php
            if ($resultado_locales && mysqli_num_rows($resultado_locales) > 0) { ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                while ($local = mysqli_fetch_assoc($resultado_locales)) {
                    // Consulta para obtener la imagen del local
                    $codLocal = $local['codLocal'];
                    $sql_imagen = "SELECT rutaArchivo FROM imagenes WHERE idIdentidad = '$codLocal' AND tipoImg = 'logo' LIMIT 1";
                    $resultado_imagen = mysqli_query($conexion, $sql_imagen);
                    $imagen = mysqli_fetch_assoc($resultado_imagen);
                    $rutaImagen = $imagen ? $imagen['rutaArchivo'] : '/Descuento-City/assets/img/default-local.jpg';
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-white p-5" style="min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                <img src="<?= htmlspecialchars($rutaImagen) ?>" class="img-fluid" alt="Logo <?= htmlspecialchars($local['nombreLocal']) ?>" style="max-height: 100px; max-width: 100%; object-fit: contain;">

                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($local['nombreLocal']) ?></h5>
                                <p class="card-text">
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($local['ubicacionLocal']) ?></small><br>
                                    <small class="text-muted"><i class="bi bi-tag"></i> <?= htmlspecialchars($local['rubroLocal']) ?></small><br>
                                    <small class="text-muted"><i class="bi bi-tag"></i> <?= htmlspecialchars($local['codLocal']) ?></small>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                </div>
                
                <!-- Botón "Ver más locales" fuera de las cards -->
                <div class="text-center mt-4">
                    <a href="/Descuento-City/localesUsuarios.php" class="btn btn-outline-primary btn-lg">
                        Ver más locales <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
                
            <?php } else { ?>
                <div class="text-center">
                    <p class="text-muted">No hay locales disponibles en este momento.</p>
                </div>
            <?php } ?>
        </div>
    </section>

    <!-- PROMOCIONES Y NOVEDADES. Solo son dos card con un boton que diga ver mas y lo mande a promociones o novedades.-->
    <!-- Faltaria agregarle una imagen de fondo -->

    <section class="py-5">
        <div class="container container-portadas">
            <h2 class="fw-bold mb-2 text-center mb-4">PROMOCIONES Y NOVEDADES</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card-portada h-100 text-center">
                        <div class="card-body-p d-flex flex-column">
                            <i class="bi bi-percent display-1 text-primary mb-3"></i>
                            <h5 class="card-title">PROMOCIONES</h5>
                            <p class="card-text">Descubre todas las promociones disponibles en nuestros locales participantes.</p>
                            <div class="mt-auto">
                                <a href="/Descuento-City/promocionesUsuario.php" class="btn btn-primary btn-lg">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-portada card-portada-n h-100 text-center">
                        <div class="card-body-n d-flex flex-column">
                            <i class="bi bi-newspaper display-1 text-success mb-3"></i>
                            <h5 class="card-title">NOVEDADES</h5>
                            <p class="card-text">Mantente al día con las últimas noticias y actualizaciones de Descuento City.</p>
                            <div class="mt-auto">
                                <a href="/Descuento-City/novedadesUsuarios.php" class="btn btn-success btn-lg">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<!-- SECCIÓN SERVICIOS -->
    <section class="py-5 text-center text-black servicios-container">
    <div class="container">
        <h2 class="fw-bold mb-4">SERVICIOS</h2>
        <p class="text-light mb-5">Estamos listos para recibirte</p>

        <div class="row g-4 justify-content-center">

            <!-- Servicio -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mx-auto mb-3">
                <i class="bi bi-car-front"></i>
                </div>
                <p>Estacionamiento</p>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mx-auto mb-3">
                <i class="bi bi-wifi"></i>
                </div>
                <p>WiFi libre</p>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mx-auto mb-3">
                <i class="bi bi-envelope"></i>
                </div>
                <p>Correo</p>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mx-auto mb-3">
                <i class="bi bi-cart"></i>
                </div>
                <p>Supermercado</p>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mx-auto mb-3">
                <i class="bi bi-airplane"></i>
                </div>
                <p>Agencia de viajes</p>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mx-auto mb-3">
                <i class="bi bi-cash-stack"></i>
                </div>
                <p>Rapipago</p>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mx-auto mb-3">
                <i class="bi bi-cup-straw"></i>
                </div>
                <p>Patio de comidas</p>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mx-auto mb-3">
                    <i class="bi bi-camera-reels"></i>
                </div>
                <p>Cine</p>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="service-icon mx-auto mb-3">
                <i class="bi bi-joystick"></i>
                </div>
                <p>Sala de juegos</p>
            </div>

        </div>
    </div>
    </section>


    <!-- FOOTER -->
    <?php
    include("includes/footer.php");
    ?>  
    
    <!-- Scripts -->
     
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        function filtrarLocales(termino) {
            const cards = document.querySelectorAll('.row.row-cols-1 .col-lg-3');
            const mensajeBusqueda = document.getElementById('mensaje-busqueda');
            const mensajeNoEncontrado = document.getElementById('mensaje-no-encontrado');
            const terminoBusqueda = document.getElementById('termino-busqueda');
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
                const titulo = card.querySelector('.card-title') ? card.querySelector('.card-title').textContent.toLowerCase() : '';
                const ubicacion = card.querySelector('.bi-geo-alt') ? card.querySelector('.bi-geo-alt').parentNode.textContent.toLowerCase() : '';
                const rubro = card.querySelector('.bi-tag') ? card.querySelector('.bi-tag').parentNode.textContent.toLowerCase() : '';
                const textoCompleto = titulo + ' ' + ubicacion + ' ' + rubro;
                
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
        
        function limpiarBusqueda() {
            document.getElementById('buscar').value = '';
            filtrarLocales('');
        }
        
        function limpiarTodosFiltros() {
            // Limpiar el buscador de texto
            document.getElementById('buscar').value = '';
            filtrarLocales('');
            
            // Redirigir a la página sin parámetros POST para limpiar los selects
            window.location.href = window.location.pathname;
        }
        
        // Auto-limpiar cuando se borre el input
        document.getElementById('buscar').addEventListener('input', function(e) {
            if (e.target.value === '') {
                filtrarLocales('');
            }
        });
    </script>
</body>
</html>