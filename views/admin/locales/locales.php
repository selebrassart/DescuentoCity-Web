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
    <title>Locales</title>
    <link rel="icon" type="image/png" href="assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <?php include("../../../includes/navbar.php");?>

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
                    <img src="../../../<?= $fila["rutaArchivo"] ?>" alt="Logo del local" width="70" height="50" style="object-fit:cover;border-radius:8px;">
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
            </td>
            <td>
                <form action="../../../controllers/localesCtrl/localesController.php" method="POST">

                    <!-- Si local esta eliminado -->
                    <?php if($fila["estadoLocal"] == 'eliminado' ):?>
                        <input type="hidden" name="codLocal" value="<?= $fila["codLocal"] ?>">
                        <button type="submit" name="activar" class="button-activar">Activar</button>
                        <!-- Boton para editar -->
                        <button type="submit" name="editar" class="button-editar"><a href="/Descuento-City/views/admin/locales/localUpdate.php?codLocal=<?php echo $fila['codLocal']; ?>">Editar</a></button>

                    <!-- Si local esta activo, Permitir editar o eliminar -->
                    <?php elseif($fila["estadoLocal"] == 'activo' ):?>
                        <input type="hidden" name="codLocal" value="<?= $fila["codLocal"] ?>">
                        <!-- Boton para editar -->
                        <button type="submit" name="editar" class="button-editar"><a href="/Descuento-City/views/admin/locales/localUpdate.php?codLocal=<?php echo $fila['codLocal']; ?>">Editar</a></button>
                        <button type="submit" name="eliminar" class="button-eliminar">Eliminar</button>
                    <?php endif;?>
                </form>
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

    // Alertas organizadas por tipo
    if(isset($_SESSION['mensaje_exito'])){
        echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> ".$_SESSION['mensaje_exito']."</div>";
        unset($_SESSION['mensaje_exito']);
    }
    if(isset($_SESSION['mensaje_error'])){
        echo "<div class='alert alert-danger'><i class='bi bi-exclamation-circle-fill'></i> ".$_SESSION['mensaje_error']."</div>";
        unset($_SESSION['mensaje_error']);
    }
    if(isset($_SESSION['mensaje_warning'])){
        echo "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle-fill'></i> ".$_SESSION['mensaje_warning']."</div>";
        unset($_SESSION['mensaje_warning']);
    }
    if(isset($_SESSION['mensaje_info'])){
        echo "<div class='alert alert-info'><i class='bi bi-info-circle-fill'></i> ".$_SESSION['mensaje_info']."</div>";
        unset($_SESSION['mensaje_info']);
    }

    ?>


    
    <div class="main-center">
        <div class="form__container-locales">
            <h1>Crear local</h1>
            <form class="form-locales" action="../../../controllers/localesCtrl/localesController.php" method="POST" enctype="multipart/form-data">
                <label>Codigo Dueño</label><br>
                <input type="text" name="codDueño" required><br>
                <label >Nombre de local</label><br>
                <input type="text" name= "nombreLocal" ><br>
                <label >Rubro</label><br>
                <input type="text" name="rubroLocal"><br>
                <label>Ubicacion</label><br>
                <input type="text" name="ubicacionLocal"><br>
                <label>Logo</label><br>
                <input class="input-img" type="file" name="imgLocal" accept="image/*"><br>
                <input class="button-form" type="submit" name="confirm" value="Crear local">
            </form>
        </div>
    </div>
</body>
</html>