<?php

session_start();

include("../../../conexionBD.php");
require("../../../funciones/funcionesSQL.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Locales - Admin</title>
    <link rel="icon" type="image/png" href="/Descuento-City/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
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

    <?php

    //paginacion

    $cant_por_pag =3;
    $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
    if(!$pagina){

    $inicio = 0;
    $pagina=1;

    }
    else{
        $inicio = ($pagina - 1) * $cant_por_pag;
    }

    //Total locales
    $consulta = "SELECT l.* , i.rutaArchivo FROM locales l LEFT JOIN imagenes i ON i.idIdentidad = l.codLocal AND i.tipoImg = 'logo'";
    $resultado = mysqli_query($conexion,$consulta);
    $total_registros = mysqli_num_rows($resultado);

    //consulta Páginada
    $consultaLocales = "SELECT l.* , i.rutaArchivo FROM locales l LEFT JOIN imagenes i ON i.idIdentidad = l.codLocal AND i.tipoImg = 'logo' ORDER BY FIELD(LOWER(estadoLocal), 'eliminado'), codLocal DESC  LIMIT $inicio,$cant_por_pag";
    $listaLocales = mysqli_query($conexion,$consultaLocales);

    $total_paginas = ceil($total_registros / $cant_por_pag);

    echo "<table class='tabla table table-striped'>";
    echo "<caption>Locales</caption>";
    echo "<tr>
            <th>Codigo</th>
            <th>Nombre</th>
            <th>Logo</th>
            <th>Ubicacion</th>
            <th>Rubro</th>
            <th>Codigo Dueño</th>
            <th>Estado</th>
            <th>Editar/Eliminar</th>
        </th>";

    while($fila = mysqli_fetch_assoc($listaLocales)){
        ?>
        <tr>
            <td> #<?= $fila["codLocal"]?></td>
            <td> <?= $fila["nombreLocal"]?></td>
            <!-- Logo -->
            <td>
                <?php if(!empty($fila["rutaArchivo"])):?>
                    <img src="../../../<?= $fila["rutaArchivo"] ?>" alt="Logo corporativo del local" width="70" height="50" style="object-fit:cover;border-radius:8px;">
                <?php else: ?>
                    <span style="color: gray;">Sin logo</span>
                <?php endif; ?>
            </td>
            <td> <?= $fila["ubicacionLocal"]?></td>
            <td> <?= $fila["rubroLocal"]?></td>
            <td> #<?= $fila["codUsuario"]?></td>
            <td>
                <?php
                $estado = $fila['estadoLocal'];
                $estado_class = '';
                $estado_icon = '';
                switch($estado) {
                    case 'activo':
                        $estado_class = 'bg-success';
                        $estado_icon = 'bi bi-check';
                        break;
                    case 'eliminado':
                        $estado_class = 'bg-danger';
                        $estado_icon = 'bi bi-x';
                        break;
                }
                ?>
                <span class="badge <?= $estado_class ?>">
                    <i class="<?= $estado_icon ?>"></i> <?= ucfirst($estado) ?>
                </span>
            <td>
                <div class="d-flex flex-wrap gap-1 justify-content-center">
                    <!-- Si local esta eliminado -->
                    <?php if($fila["estadoLocal"] == 'eliminado' ):?>
                        <form action="../../../controllers/localesCtrl/localesController.php" method="POST" class="d-inline">
                            <input type="hidden" name="codLocal" value="<?= $fila["codLocal"] ?>">
                            <button type="submit" name="activar" class="btn btn-success btn-sm rounded-pill px-3" title="Activar local">
                                <i class="bi bi-check-lg"></i> Activar
                            </button>
                        </form>
                        <!-- Boton para editar -->
                        <a href="/Descuento-City/views/admin/locales/localUpdate.php?codLocal=<?php echo $fila['codLocal']; ?>" 
                           class="btn btn-warning btn-sm rounded-pill px-3" title="Editar local">
                            <i class="bi bi-pencil"></i> Editar
                        </a>

                    <!-- Si local esta activo, Permitir editar o eliminar -->
                    <?php elseif($fila["estadoLocal"] == 'activo' ):?>
                        <!-- Boton para editar -->
                        <a href="/Descuento-City/views/admin/locales/localUpdate.php?codLocal=<?php echo $fila['codLocal']; ?>" 
                           class="btn btn-warning btn-sm rounded-pill px-3" title="Editar local">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form action="../../../controllers/localesCtrl/localesController.php" method="POST" class="d-inline">
                            <input type="hidden" name="codLocal" value="<?= $fila["codLocal"] ?>">
                            <button type="submit" name="eliminar" class="btn btn-danger btn-sm rounded-pill px-3" title="Eliminar local">
                                <i class="bi bi-x-lg"></i> Eliminar
                            </button>
                        </form>
                    <?php endif;?>
                </div>
            </td>
        </tr>
    <?php
    }
    echo "</table>";

    mysqli_free_result($listaLocales);

    mysqli_close($conexion);

    echo "<div class='paginacion mt-3'>";
    for($i = 1;$i <= $total_paginas;$i++){
        if($pagina == $i){
            echo $pagina . "";
        }
        else{
            echo "<a href='locales.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1' id='paginacion'>$i</a>";
        }
    }
    echo "</div><br>";

    ?>
    </div> <!-- Cierre del contenedor principal -->

    <!-- Formulario de crear local -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <h1 class="text-center mb-4">Crear Local</h1>

                <form action="../../../controllers/localesCtrl/localesController.php" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-white">
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" class="form-control" name="codDueño" placeholder="Código del Dueño" aria-label="Código Dueño" required>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-shop"></i></span>
                        <input type="text" class="form-control" name="nombreLocal" placeholder="Nombre del local" aria-label="Nombre Local" required>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-tags"></i></span>
                        <input type="text" class="form-control" name="rubroLocal" placeholder="Rubro del local" aria-label="Rubro" required>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" name="ubicacionLocal" placeholder="Ubicación del local" aria-label="Ubicación" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="imgLocal" class="form-label"><i class="bi bi-image"></i> Logo del Local</label>
                        <input type="file" class="form-control" name="imgLocal" id="imgLocal" accept="image/*" required>
                        <div class="form-text">Selecciona una imagen para el logo del local (JPG, PNG, GIF)</div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="confirm" class="btn btn-primary btn-lg rounded-pill">
                            <i class="bi bi-plus-circle"></i> Crear Local
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("../../../includes/footer.php"); ?>
    
</body>
</html>