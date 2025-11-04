<?php
// localUpdate.php - form para editar local (corregido)
session_start();
require("../../../conexionBD.php");


$codLocal = isset($_GET['codLocal']) ? intval($_GET['codLocal']) : 0;
if ($codLocal >= 0) {
    
    // consulto datos local 
    $consulta = "SELECT * FROM locales WHERE codLocal = $codLocal";
    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $local = mysqli_fetch_assoc($resultado);

        // consulto datos imagen (logo)
        $consultaImg = "SELECT * FROM imagenes WHERE idIdentidad = '$codLocal' AND tipoImg = 'logo'";
        $resultadoImg = mysqli_query($conexion, $consultaImg);

        //Verifico si existe img
        if ($resultadoImg && mysqli_num_rows($resultadoImg) > 0) {
            $img = mysqli_fetch_assoc($resultadoImg);
        }

    } else {
        echo "No se encontro el local";
        mysqli_close($conexion);
        exit;
    }

} else {
    echo "No se recibio el ID local..";
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
    <title>Editar Local - Admin</title>
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

    <!-- Formulario de editar local -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Editar Local</h1>

                <form action="/controllers/localesCtrl/localesUpdateController.php" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-white">
                    
                    <!-- Codigo Local (hidden) -->
                    <input type="hidden" name="codLocal" value="<?php echo isset($local['codLocal']) ? htmlspecialchars($local['codLocal']) : ''; ?>">
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-shop"></i></span>
                        <input type="text" class="form-control" name="nombreLocal" placeholder="Nombre del local" aria-label="Nombre Local" value="<?php echo isset($local['nombreLocal']) ? htmlspecialchars($local['nombreLocal']) : ''; ?>" required>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-tags"></i></span>
                        <input type="text" class="form-control" name="rubroLocal" placeholder="Rubro del local" aria-label="Rubro" value="<?php echo isset($local['rubroLocal']) ? htmlspecialchars($local['rubroLocal']) : ''; ?>" required>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" name="ubicacionLocal" placeholder="Ubicación del local" aria-label="Ubicación" value="<?php echo isset($local['ubicacionLocal']) ? htmlspecialchars($local['ubicacionLocal']) : ''; ?>" required>
                    </div>

                    <!-- Logo actual -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-image"></i> Logo Actual</label>
                        <div class="text-center p-3 border rounded bg-light">
                            <?php
                            // ruta guardada en BD (por ejemplo "uploads/logos/nombre.jpg")
                            $ruta = $img["rutaArchivo"] ?? '';

                            // Construyo ruta absoluta segura usando DOCUMENT_ROOT + ruta relativa en BD
                            // Asegurate que la ruta en BD sea relativa a la raíz del proyecto (ej: "uploads/logos/xxx.png")
                            $rutaPublica = '/' . ltrim($ruta, '/\\');

                            if (!empty($ruta)): ?>
                                <img src="<?php echo htmlspecialchars($rutaPublica); ?>" alt="Logo del local" class="img-thumbnail" style="max-width: 200px; max-height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="text-muted">
                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                    <p class="mb-0">Sin logo</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nuevoLogo" class="form-label"><i class="bi bi-upload"></i> Cambiar Logo</label>
                        <input type="file" class="form-control" name="nuevoLogo" id="nuevoLogo" accept="image/*">
                        <div class="form-text">Selecciona una nueva imagen para el logo del local (JPG, PNG, GIF)</div>
                    </div>

                    <input type="hidden" name="existeLogo" value="<?php echo htmlspecialchars($ruta); ?>">
                    
                    <div class="d-grid">
                        <button type="submit" name="confirm" class="btn btn-primary btn-lg">
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