<?php
session_start();
include("../../../conexionBD.php");
require("../../../funciones/funcionesSQL.php");

// Configuración de paginación
$cant_por_pag = 5;
$pagina = isset($_GET["pagina"]) ? (int)$_GET["pagina"] : 1;
if(!$pagina) {
    $inicio = 0;
    $pagina = 1;
} else {
    $inicio = ($pagina - 1) * $cant_por_pag;
}

// Consulta principal para contar total de registros
$consultaTotal = "SELECT COUNT(*) as total FROM novedades";
$resultadoTotal = mysqli_query($conexion, $consultaTotal);
$total_registros = mysqli_fetch_assoc($resultadoTotal)['total'];

//Consulta paginada para obtener novedades con imágenes
$consultaNovedadesPag = "SELECT 
        n.codNovedad,
        n.tituloNovedad,
        n.textoNovedad,
        n.fechaDesdeNovedad,
        n.fechaHastaNovedad,
        n.categoriaCliente,
        i.rutaArchivo,
        i.fechaSubida
        FROM novedades n 
        LEFT JOIN imagenes i ON i.tipoImg = 'portada' AND i.tipoIdentidad = 'novedad' AND i.idIdentidad = n.codNovedad
        WHERE n.estado = 'activa'
        ORDER BY n.codNovedad DESC
        LIMIT $inicio, $cant_por_pag";

$listaNovedades = mysqli_query($conexion, $consultaNovedadesPag);
$total_paginas = ceil($total_registros / $cant_por_pag);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>Novedades - Admin</title>
    <link rel="icon" type="image/png" href="/Descuento-City/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <?php include("../../../includes/navbar.php");?>

    <!-- Mensajes de alerta -->
    <div class="container mt-3">
        <?php
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
        
        // Compatibilidad con mensaje simple
        if(isset($_SESSION["mensaje"])){
            echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>";
            echo "<i class='bi bi-info-circle-fill'></i> " . $_SESSION['mensaje'];
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
            echo "</div>";
            unset($_SESSION['mensaje']);
        }
        ?>
    </div>

    <div class="container mt-4">
        <div class="table-responsive">
            <table class='tabla table table-striped'>
                <caption>Novedades Activas</caption>
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Portada</th>
                        <th>Fechas</th>
                        <th>Categoria Cliente</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while($nov = mysqli_fetch_assoc($listaNovedades)){
                    ?>
                    <tr>
                        <td> #<?= $nov["codNovedad"] ?></td>
                        <td> 
                            <strong><?= !empty($nov['tituloNovedad']) ? htmlspecialchars($nov['tituloNovedad']) : 'Sin título' ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars(substr($nov["textoNovedad"], 0, 100)) ?><?= strlen($nov["textoNovedad"]) > 100 ? '...' : '' ?></small>
                        </td>
                        <td>
                            <?php 
                                $consultoImg = "SELECT * FROM imagenes WHERE tipoImg='portada'";
                                $resultadoImg = mysqli_query($conexion,$consultoImg);
                                $img = mysqli_fetch_assoc($resultadoImg);
                                if(!empty($nov["rutaArchivo"]))
                            :?>
                                <img src="../../../<?= $nov["rutaArchivo"] ?>" alt="Imagen represantita de la novedad" width="80" height="40" style="object-fit:cover;border-radius:8px;">

                            <?php else: ?>
                                <span style="color: gray;">Sin portada</p></p></span>

                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="background-color: #e8f5e8; padding: 2px 5px; border-radius: 3px; margin-bottom: 2px;">
                                Desde: <?= $nov["fechaDesdeNovedad"] ?>
                            </div>
                            <div style="background-color: #ffe8e8; padding: 2px 5px; border-radius: 3px;">
                                Hasta: <?= $nov["fechaHastaNovedad"] ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            $categoria = $nov['categoriaCliente'];
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
                                case 'nicial':
                                default:
                                    $badge_class = 'bg-secondary';
                                    $icon = 'bi bi-circle-fill';
                                    break;
                            }
                            ?>
                            <span class="badge <?= $badge_class ?>">
                                <i class="<?= $icon ?>"></i> <?= $categoria ?>
                            </span>
                        </td>
                        <td>
                            <a href="../../../views/admin/novedades/novedadesUpdate.php?codNovedad=<?= $nov['codNovedad'] ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form action="../../../controllers/novedadesCtrl/eliminarNovedadController.php" method="POST" style="display:inline;">
                                <input type="hidden" name="codNovedad" value="<?= $nov['codNovedad'] ?>">
                                <button type="submit" name="eliminar" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta novedad?')">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php
                }?>
            </tbody>
        </table>
    </div>
    
    <div class='paginacion mt-3 text-center'>
    <?php
    for($i = 1;$i <= $total_paginas;$i++){
        if($pagina == $i){
            echo "<span class='btn btn-primary btn-sm mx-1'>$pagina</span>";
        }
        else{
            echo "<a href='novedades.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1'>$i</a>";
        }
    }
    ?>
    </div>
    </div>
    ?>
    

    <!-- Formulario de crear novedad -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Crear Novedad</h1>

                <form action="../../../controllers/novedadesCtrl/novedadesController.php" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-white">
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-newspaper"></i></span>
                        <input type="text" class="form-control" name="tituloNovedad" placeholder="Título de la novedad" aria-label="Título" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="textoNovedad" class="form-label"><i class="bi bi-text-paragraph"></i> Descripción de la novedad</label>
                        <textarea class="form-control" name="textoNovedad" id="textoNovedad" rows="5" placeholder="Escribe la descripción de la novedad..." required></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fechaDesde" class="form-label"><i class="bi bi-calendar-check"></i> Fecha de inicio</label>
                            <input type="date" class="form-control" name="fechaDesdeNovedad" id="fechaDesde" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fechaHasta" class="form-label"><i class="bi bi-calendar-x"></i> Fecha de finalización</label>
                            <input type="date" class="form-control" name="fechaHastaNovedad" id="fechaHasta" required>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-star"></i></span>
                        <select class="form-select" name="categoriaCliente" aria-label="Categoría Cliente" required>
                            <option value="">Dirigido a...</option>
                            <option value="inicial">Inicial</option>
                            <option value="medium">Medium</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="imgNov" class="form-label"><i class="bi bi-image"></i> Imagen de la novedad</label>
                        <input type="file" class="form-control" name="imgNov" id="imgNov" accept="image/*">
                        <div class="form-text">Selecciona una imagen para la novedad (JPG, PNG, GIF)</div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="confirm" class="btn btn-primary btn-lg">Crear Novedad</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("../../../includes/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>