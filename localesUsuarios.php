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
    <title>Promociones - Invitado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link rel="icon" type="image/png" href="assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
<?php include("includes/header.php"); ?>
 <!-- Hero Section con breadcrumb superpuesto -->
    <div class="position-relative">
        <div class="portada-novedades">
            <img src="/Descuento-City/assets/img/locales-portada.png" class="img-fluid w-100" alt="Portada Locales" style="height: 300px; object-fit: cover;">
        </div>
        <div class="breadcrumb-overlay position-absolute top-0 start-0 w-100 text-white p-3">
            <div class="container">
                <?php include 'includes/breadcrumb.php'; ?>
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
       
        <div class="col-12 text-center mt-2">
            <button type="reset" class="btn btn-outline-secondary me-2">
            Borrar Filtros
        </button>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <!-- RESULTADOS -->
    <div class="row">
        <?php if ($resultado_locales && mysqli_num_rows($resultado_locales) > 0) { ?>
            <?php while ($local = mysqli_fetch_assoc($resultado_locales)) { ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm text-center">
                        <img src="/Descuento-City/<?= $local['logo'] ?: 'assets/img/default-logo.png'; ?>" 
                             class="card-img-top p-3" alt="<?= $local['nombreLocal']; ?>"
                             style="max-height: 150px; object-fit: contain;">
                        <div class="card-body">
                            <h5 class="card-title"><?= $local['nombreLocal']; ?></h5>
                            <p class="card-text text-muted"><?= $local['rubroLocal']; ?></p>
                            <p class="card-text"><small class="text-secondary"><?= $local['ubicacionLocal']; ?></small></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p class="text-center mt-3">No se encontraron locales activos.</p>
        <?php } ?>
    </div>

</div>

<?php include("includes/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
