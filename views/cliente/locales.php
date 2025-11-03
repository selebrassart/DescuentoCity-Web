<?php
session_start();



// Verificar si el usuario está logueado y es cliente
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] !== 'cliente') {
    // Debug adicional
    error_log("Sesión rechazada - tipoUsuario: " . ($_SESSION['tipoUsuario'] ?? 'NO_SET'));
    header("Location: /Descuento-City/views/auth/login.php");
    exit();
}

include("../../conexionBD.php");
require("../../funciones/funcionesCliente.php");

//var para rutas
$breadcrumb_titulo_activo = 'Locales';

$codCliente = $_SESSION["codUsuario"];

// Obtener la categoría del cliente logueado
$consulta_cliente = "SELECT categoriaCliente FROM usuarios WHERE codUsuario = '$codCliente'";
$resultado_cliente = mysqli_query($conexion, $consulta_cliente);
$cliente = mysqli_fetch_assoc($resultado_cliente);
$categoria_cliente = $cliente['categoriaCliente'];

//Obtengo alcance de la categoria cliente.
$categorias_permitidas = verificarCategoria($categoria_cliente);

//capturo filtros del form POST
$rubroSeleccionado = $_POST['rubro'] ?? '';
$nombreSeleccionado = $_POST['local'] ?? '';

// CONSULTAS AUXILIARES (para llenar los selects)
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
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link rel="icon" type="image/png" href="/Descuento-City/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
<?php include("../../includes/cliente/clienteHeader.php"); ?>

<!-- Portada -->
     <section class="portada position-relative">
        <img src="/Descuento-City/assets/img/locales-portada.png" alt="Portada Locales"class="portada-img img-fluid">
        <div class="portada-overlay text-center">
            <h1 class="portada-titulo">LOCALES</h1>
            <p class="portada-subtitulo">Descubrí todas las marcas y rubros de<strong>Descuento City</strong>.</p>
        </div>
    </section>
<!-- Breadcrumb debajo de la portada -->
<div class="container mt-3">
    <?php include '../../includes/breadcrumb.php'; ?>
</div>

<!-- Información de categoría del cliente -->
<div class="container mt-4">
    <div class="alert alert-info border border-primary" role="alert">
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

    <!-- Filtros -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <form class="row" method="POST">
                <div class="col-md-6 mb-2">
                    <select name="local" class="form-select">
                        <option value="" hidden selected>Buscar por local</option>
                        <option value="">Todos los locales</option>
                        <?php 
                        mysqli_data_seek($resultado_nombres, 0); // Reset cursor
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
                        mysqli_data_seek($resultado_rubros, 0); // Reset cursor
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
            <?php while ($local = mysqli_fetch_assoc($resultado_locales)) { 
                $codLocal = $local['codLocal'];
                
                // Consultar promociones del local disponibles para el cliente
                $hoy = date('Y-m-d');
                $condicion_categorias = "'" . implode("','", $categorias_permitidas) . "'";
                
                $consulta_promos = "SELECT COUNT(*) as total_promos FROM promociones 
                                  WHERE codLocal = '$codLocal' 
                                  AND estadoPromo = 'aprobada' 
                                  AND '$hoy' BETWEEN fechaDesdePromo AND fechaHastaPromo
                                  AND (categoriaCliente IN ($condicion_categorias) OR categoriaCliente IS NULL OR categoriaCliente = '')";
                
                $resultado_promos = mysqli_query($conexion, $consulta_promos);
                $promos_disponibles = mysqli_fetch_assoc($resultado_promos)['total_promos'];
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <!-- Header del card con logo -->
                        <div class="card-header bg-white text-center p-3" style="min-height: 120px; display: flex; align-items: center; justify-content: center;">
                            <img src="/Descuento-City/<?= $local['logo'] ?: 'assets/img/default-logo.png'; ?>" 
                                 class="img-fluid" alt="<?= htmlspecialchars($local['nombreLocal']); ?>"
                                 style="max-height: 100px; max-width: 100%; object-fit: contain;">
                        </div>
                        
                        <!-- Cuerpo del card -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-center">
                                <i class="bi bi-shop"></i> <?= htmlspecialchars($local['nombreLocal']); ?>
                            </h5>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-tag"></i> <strong>Rubro:</strong> <?= htmlspecialchars($local['rubroLocal']); ?>
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> <strong>Ubicación:</strong> <?= htmlspecialchars($local['ubicacionLocal']); ?>
                                </small>
                            </div>
                            
                            <!-- Promociones disponibles -->
                            <div class="mt-auto">
                                <?php if ($promos_disponibles > 0): ?>
                                    <div class="alert alert-success text-center mb-2">
                                        <strong><?= $promos_disponibles ?></strong> promocion<?= $promos_disponibles > 1 ? 'es' : '' ?> disponible<?= $promos_disponibles > 1 ? 's' : '' ?>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-primary" onclick="verPromociones(<?= $codLocal ?>, '<?= htmlspecialchars($local['nombreLocal']) ?>')">
                                            <i class="bi bi-eye"></i> Ver Promociones
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-secondary text-center mb-2">
                                        <i class="bi bi-info-circle"></i> Sin promociones disponibles
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No se encontraron locales activos.
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Modal para mostrar promociones -->
<div class="modal fade" id="modalPromociones" tabindex="-1" aria-labelledby="modalPromocionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPromocionesLabel">
                    <i class="bi bi-percent"></i> Promociones de <span id="nombreLocal"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoPromociones">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function filtrarLocales(termino) {
        const cards = document.querySelectorAll('.row .col-md-4');
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
            const titulo = card.querySelector('.card-title').textContent.toLowerCase();
            const smallElements = card.querySelectorAll('small');
            let textoCompleto = titulo;
            
            smallElements.forEach(small => {
                textoCompleto += ' ' + small.textContent.toLowerCase();
            });
            
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
    
    // Función para ver promociones de un local
    function verPromociones(codLocal, nombreLocal) {
        document.getElementById('nombreLocal').textContent = nombreLocal;
        document.getElementById('contenidoPromociones').innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `;
        
        // Mostrar modal
        var modal = new bootstrap.Modal(document.getElementById('modalPromociones'));
        modal.show();
        
        // Cargar promociones via AJAX
        fetch('../../controllers/buscar_promociones_local.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'codLocal=' + codLocal
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('contenidoPromociones').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('contenidoPromociones').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i> Error al cargar las promociones.
                </div>
            `;
        });
    }
</script>
</body>
</html>