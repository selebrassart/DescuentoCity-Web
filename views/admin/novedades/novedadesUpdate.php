<?php

session_start();
require("../../../conexionBD.php");
include("../../../includes/admin/adminHeader.php");

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
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <title>Editar Novedad</title>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../../views/admin/adminDashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="novedades.php">Novedades</a></li>
                        <li class="breadcrumb-item active">Editar Novedad #<?= $nov['codNovedad'] ?></li>
                    </ol>
                </nav>

                <!-- Mensajes del sistema -->
                <?php
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

                <!-- Formulario responsive -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-pencil-square"></i> Editar Novedad #<?= $nov['codNovedad'] ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="/Descuento-City/controllers/novedadesCtrl/editarNovedadController.php" method="POST" enctype="multipart/form-data">   
                            <!-- Codigo Novedad -->
                            <input type="hidden" name="codNovedad" value="<?php echo isset($nov['codNovedad']) ? htmlspecialchars($nov['codNovedad']) : ''; ?>">
                            
                            <!-- Título de la novedad -->
                            <div class="mb-3">
                                <label for="tituloNovedad" class="form-label">
                                    <i class="bi bi-type-bold"></i> Título de la novedad
                                </label>
                                <input type="text" name="tituloNovedad" id="tituloNovedad" class="form-control" placeholder="Ej: Descuento especial en tecnología" value="<?php echo isset($nov['tituloNovedad']) ? htmlspecialchars($nov['tituloNovedad']) : ''; ?>">
                            </div>
                            
                            <!-- Descripción de la novedad -->
                            <div class="mb-3">
                                <label for="textoNovedad" class="form-label">
                                    <i class="bi bi-file-text"></i> Descripción de la novedad *
                                </label>
                                <textarea name="textoNovedad" id="textoNovedad" class="form-control" rows="5" required><?php echo isset($nov['textoNovedad']) ? htmlspecialchars($nov['textoNovedad']) : ''; ?></textarea>
                            </div>
                            
                            <!-- Fechas en fila responsive -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fechaDesdeNovedad" class="form-label">
                                            <i class="bi bi-calendar-event"></i> Fecha de inicio *
                                        </label>
                                        <input type="date" name="fechaDesdeNovedad" id="fechaDesdeNovedad" class="form-control" value="<?php echo isset($nov['fechaDesdeNovedad']) ? htmlspecialchars($nov['fechaDesdeNovedad']) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fechaHastaNovedad" class="form-label">
                                            <i class="bi bi-calendar-x"></i> Fecha de finalización *
                                        </label>
                                        <input type="date" name="fechaHastaNovedad" id="fechaHastaNovedad" class="form-control" value="<?php echo isset($nov['fechaHastaNovedad']) ? htmlspecialchars($nov['fechaHastaNovedad']) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Categoria -->
                            <div class="mb-3">
                                <label for="categoriaCliente" class="form-label">
                                    <i class="bi bi-people"></i> Dirigido a *
                                </label>
                                <select name="categoriaCliente" id="categoriaCliente" class="form-select" required>
                                    <option value="">Seleccione una categoría</option>
                                    <option value="Inicial" <?= ($nov['categoriaCliente'] == 'Inicial') ? 'selected' : '' ?>>Inicial</option>
                                    <option value="Medium" <?= ($nov['categoriaCliente'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                                    <option value="Premium" <?= ($nov['categoriaCliente'] == 'Premium') ? 'selected' : '' ?>>Premium</option>
                                </select>
                            </div>

                            <!-- Portada actual y nueva -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-image"></i> Imagen de la novedad
                                </label>
                                
                                <?php
                                // ruta guardada en BD (por ejemplo "uploads/fondoNovedad/nombre.jpg")
                                $ruta = $img["rutaArchivo"] ?? '';
                                $rutaPublica = '/Descuento-City/' . ltrim($ruta, '/\\');

                                if (!empty($ruta)): ?>
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-2">Imagen actual:</small>
                                        <div class="text-center">
                                            <img src="<?php echo htmlspecialchars($rutaPublica); ?>" 
                                                 alt="Portada de la novedad" 
                                                 class="img-fluid img-thumbnail" 
                                                 style="max-width: 300px; max-height: 200px; object-fit: cover;">
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> Sin portada actual
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Cambiar portada -->
                                <label for="nuevaImg" class="form-label">
                                    <?= !empty($ruta) ? 'Cambiar portada (opcional)' : 'Subir nueva portada' ?>
                                </label>
                                <input type="file" name="nuevaImg" id="nuevaImg" class="form-control" accept="image/*">
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Formatos permitidos: JPG, PNG, GIF, WEBP
                                    <?php if (!empty($ruta)): ?>
                                        <br>Deje vacío si no desea cambiar la imagen actual.
                                    <?php endif; ?>
                                </div>
                                
                                <input type="hidden" name="existeImg" value="<?php echo htmlspecialchars($ruta); ?>">
                            </div>

                            <!-- Botones responsive -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="novedades.php" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" name="confirm" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
mysqli_close($conexion);
?>