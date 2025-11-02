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
    <link rel="icon" type="image/png" href="assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <?php include("../../../includes/admin/adminHeader.php");?>

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
                                <img src="../../../<?= $nov["rutaArchivo"] ?>" alt="Portada novedad" width="80" height="40" style="object-fit:cover;border-radius:8px;">

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
    <?php

    mysqli_free_result($listaNovedades);

    mysqli_close($conexion);

    ?>
    <div class='paginacion mt-3'>
    <?php
    for($i = 1;$i <= $total_paginas;$i++){
        if($pagina == $i){
            echo $pagina . "";
        }
        else{
            echo "<a href='novedades.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1' id='paginacion'>$i</a>";
        }
    }
    ?>
    </div>
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
    
    // Compatibilidad con mensaje simple (por si algún controlador aún no está actualizado)
    if(isset($_SESSION["mensaje"])){
        echo "<div class='alert alert-info alert-dismissible fade show' role='alert'>";
        echo "<i class='bi bi-info-circle-fill'></i> " . $_SESSION['mensaje'];
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
        unset($_SESSION['mensaje']);
    }
    ?>
    

    <div class="main-center">
        <div class="form__container-locales">
            <h1>Crear novedad</h1>
            <form class="form-locales" action="../../../controllers/novedadesCtrl/novedadesController.php" method="POST" enctype="multipart/form-data">
                <!-- Título novedad -->
                <label>Título de la novedad</label><br>
                <input type="text" name="tituloNovedad" placeholder="Ej: Descuento especial en tecnología" required><br>
                <!-- Texto novedad -->
                <label>Descripción de la novedad</label><br>
                <textarea name="textoNovedad" rows="5" cols="40" required></textarea><br>
                <!-- Fecha inicio  -->
                <label>Fecha de inicio de novedad</label><br>
                <input type="date" name="fechaDesdeNovedad" required><br>
                <!-- Fecha fin -->
                <label>Fecha de finalización de novedad</label><br>
                <input type="date" name="fechaHastaNovedad" required><br>
                <!-- Categoria -->
                <label>Dirigido a:</label><br>
                <select name="categoriaCliente" required>
                    <option value="">Seleccionar categoría</option>
                    <option value="inicial">Inicial</option>
                    <option value="medium">Medium</option>
                    <option value="premium">Premium</option>
                </select><br>
                <!-- Imagen -->
                <label>Imagen de la novedad</label><br>
                <input class="input-img" type="file" name="imgNov" accept="image/*"><br>

                <input class="button-form" type="submit" name="confirm" value="Crear Novedad">
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>