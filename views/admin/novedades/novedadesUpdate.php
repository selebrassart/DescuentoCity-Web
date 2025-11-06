<?php

session_start();

require("../../../conexionBD.php");

$codNovedad = isset($_GET['codNovedad']) ? intval($_GET['codNovedad']) : 0;
if ($codNovedad > 0) {
    
    // consulto datos novedad 
    $consulta = "SELECT * FROM novedades WHERE codNovedad = $codNovedad";
    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $nov = mysqli_fetch_assoc($resultado);

        // consulto datos imagen (portada)
        $consultaImg = "SELECT * FROM imagenes WHERE idIdentidad = '$codNovedad' AND tipoImg = 'portada' AND tipoIdentidad = 'novedad'";
        $resultadoImg = mysqli_query($conexion, $consultaImg);

        //Verifico si existe img
        if ($resultadoImg && mysqli_num_rows($resultadoImg) > 0) {
            $img = mysqli_fetch_assoc($resultadoImg);
        }

    } else {
        echo "No se encontró la novedad";
        mysqli_close($conexion);
        exit;
    }

} else {
    echo "No se recibió el ID de novedad..";
    mysqli_close($conexion);
    exit;
}



?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Editar Novedad - Admin</title>
    <link rel="icon" type="image/png" href="/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <?php include("../../../includes/navbar.php");?>

    <!-- Mensajes de alerta -->
    <div class="container mt-3">
        <?php
        // Alertas organizadas por tipo
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
    </div>

    <!-- Formulario de editar novedad -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Editar Novedad #<?= $nov['codNovedad'] ?></h1>

                <form action="/controllers/novedadesCtrl/editarNovedadController.php" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-white">
                    
                    <!-- Codigo Novedad (hidden) -->
                    <input type="hidden" name="codNovedad" value="<?php echo isset($nov['codNovedad']) ? htmlspecialchars($nov['codNovedad']) : ''; ?>">
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-type-bold"></i></span>
                        <input type="text" class="form-control" name="tituloNovedad" placeholder="Título de la novedad" aria-label="Título" value="<?php echo isset($nov['tituloNovedad']) ? htmlspecialchars($nov['tituloNovedad']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="textoNovedad" class="form-label"><i class="bi bi-file-text"></i> Descripción de la novedad</label>
                        <textarea name="textoNovedad" id="textoNovedad" class="form-control" rows="4" placeholder="Descripción detallada de la novedad..." required><?php echo isset($nov['textoNovedad']) ? htmlspecialchars($nov['textoNovedad']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" class="form-control" name="fechaDesdeNovedad" aria-label="Fecha Inicio" value="<?php echo isset($nov['fechaDesdeNovedad']) ? htmlspecialchars($nov['fechaDesdeNovedad']) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="bi bi-calendar-x"></i></span>
                                <input type="date" class="form-control" name="fechaHastaNovedad" aria-label="Fecha Fin" value="<?php echo isset($nov['fechaHastaNovedad']) ? htmlspecialchars($nov['fechaHastaNovedad']) : ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-people"></i></span>
                        <select name="categoriaCliente" class="form-select" aria-label="Categoría Cliente" required>
                            <option value="">Dirigido a...</option>
                            <option value="Inicial" <?= ($nov['categoriaCliente'] == 'Inicial') ? 'selected' : '' ?>>Inicial</option>
                            <option value="Medium" <?= ($nov['categoriaCliente'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                            <option value="Premium" <?= ($nov['categoriaCliente'] == 'Premium') ? 'selected' : '' ?>>Premium</option>
                        </select>
                    </div>

                    <!-- Imagen actual -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-image"></i> Imagen Actual</label>
                        <div class="text-center p-3 border rounded bg-light">
                            <?php
                            // ruta guardada en BD (por ejemplo "uploads/fondoNovedad/nombre.jpg")
                            $ruta = $img["rutaArchivo"] ?? '';
                            $rutaPublica = '/' . ltrim($ruta, '/\\');

                            if (!empty($ruta)): ?>
                                <img src="<?php echo htmlspecialchars($rutaPublica); ?>" alt="Portada de la novedad" class="img-thumbnail" style="max-width: 300px; max-height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="text-muted">
                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                    <p class="mb-0">Sin imagen</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nuevaImg" class="form-label"><i class="bi bi-upload"></i> Cambiar Imagen</label>
                        <input type="file" class="form-control" name="nuevaImg" id="nuevaImg" accept="image/*">
                        <div class="form-text">Selecciona una nueva imagen para la novedad (JPG, PNG, GIF, WEBP)</div>
                    </div>

                    <input type="hidden" name="existeImg" value="<?php echo htmlspecialchars($ruta); ?>">
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="novedades.php" class="btn btn-secondary me-md-2">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" name="confirm" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("../../../includes/footer.php"); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>

<?php
mysqli_close($conexion);
?>