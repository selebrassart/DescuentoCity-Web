<?php

session_start();

include("../../conexionBD.php");

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="/Descuento-City/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
    <title>Mis Promociones - Descuento City</title>
</head>
<body>

    <?php
    include("../../includes/dueño/dueñoHeader.php");


    //paginacion

    $cant_por_pag =5;
    $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
    if(!$pagina){

    $inicio = 0;
    $pagina=1;

    }
    else{
        $inicio = ($pagina - 1) * $cant_por_pag;
    }

    //Total promociones del dueño
    $codUsuario = $_SESSION['codUsuario'];
    $consulta = "SELECT p.* , i.rutaArchivo FROM promociones p 
                LEFT JOIN imagenes i ON i.idIdentidad = p.codPromo AND i.tipoImg = 'portada'
                INNER JOIN locales l ON p.codLocal = l.codLocal 
                WHERE l.codUsuario = '$codUsuario' 
                AND p.estadoPromo IN ('pendiente', 'aprobada')";
                //p.* selecciona todos los campos de la tabla promociones , i.rutaArchivo => la ruta del archivo
                // LEFT JOIN => Trae todas las promos , tenga imagen o no. i.IdIdentidad = p.promociones = relaciona img con promo.
                // INNER JOIN => Trea promos que tenga un local asociado 


    $resultado = mysqli_query($conexion,$consulta);
    $total_registros = mysqli_num_rows($resultado);

    //consulta Páginada
    $consultaPromociones = "SELECT p.* , i.rutaArchivo FROM promociones p 
                           LEFT JOIN imagenes i ON i.idIdentidad = p.codPromo AND i.tipoImg = 'portada'
                           INNER JOIN locales l ON p.codLocal = l.codLocal 
                           WHERE l.codUsuario = '$codUsuario' 
                           AND p.estadoPromo IN ('pendiente', 'aprobada','denegada')
                           ORDER BY FIELD(estadoPromo,'aprobada','pendiente','denegada') LIMIT $inicio,$cant_por_pag";

    $listaPromociones = mysqli_query($conexion,$consultaPromociones);

    $total_paginas = ceil($total_registros / $cant_por_pag);

    echo "<table class='tabla table table-striped'>";
    echo "<caption>Promociones</caption>";
    echo "<tr>
            <th>Codigo</th>
            <th>Descripcion</th>
            <th>Portada</th>
            <th>fechas</th>
            <th>Cat Cliente</th>
            <th>Dias semana</th>
            <th>Estado</th>
            <th>Eliminar</th>
        </th>";

    if(mysqli_num_rows($listaPromociones) <= 0){
        ?>
        <tr>
            <td colspan="9" style="text-align: center; padding: 20px; color: #666; font-style: italic;">
                No hay promociones creadas
            </td>
        </tr>
        <?php
    }
    while($fila = mysqli_fetch_assoc($listaPromociones)){


        ?>
        <tr>
            <td> <?= $fila["codPromo"]?></td>
            <td> <?= $fila["textoPromo"]?></td>
            <!-- Logo -->
            <td>
                <?php if(!empty($fila["rutaArchivo"])):?>
                    <img src="../../<?= $fila["rutaArchivo"] ?>" alt="portada promocion" width="70" height="50" style="object-fit:cover;border-radius:8px;">
                <?php else: ?>
                    <span style="color: gray;">Sin portada</span>
                <?php endif; ?>
            </td>
            <td>
                <div style="background-color: #e8f5e8; padding: 2px 5px; border-radius: 3px; margin-bottom: 2px;">
                    Desde: <?= $fila["fechaDesdePromo"] ?>
                </div>
                <div style="background-color: #ffe8e8; padding: 2px 5px; border-radius: 3px;">
                    Hasta: <?= $fila["fechaHastaPromo"] ?>
                </div>
            </td>
            <td> <?= $fila["categoriaCliente"]?></td>
            <td> <?= $fila["diasSemana"]?></td>
            <td> <?= ucfirst($fila["estadoPromo"])?></td>
            <td>
                <form action="../../controllers/dueñoCtrl/promocionesDueñoController.php" method="POST">
                    <!-- Si local esta eliminado -->
                    <?php if($fila["estadoPromo"] == 'pendiente' OR 'aprobada' OR 'denegada'):?>
                        <input type="hidden" name="codPromo" value="<?= $fila["codPromo"] ?>">
                        <button type="submit" name="eliminar" class="button-eliminar">Eliminar</button>
                    <?php endif;?>
                </form>
            </td>
        </tr>
    <?php
    }
    echo "</table>";

    mysqli_free_result($listaPromociones);

    echo "<div class='paginacion mt-3'>";
    for($i = 1;$i <= $total_paginas;$i++){
        if($pagina == $i){
            echo $pagina . "";
        }
        else{
            echo "<a href='mis_promos.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1' id='paginacion'>$i</a>";
        }
    }
    echo "</div><br>";?>


    <div class="main-center">
        <div class="form__container-promociones">
            <form class="form-promociones" action="../../controllers/dueñoCtrl/promocionesDueñoController.php" method="POST" enctype="multipart/form-data">
                <h2>Crear promocion</h2>

                <!-- Descripcion promo / BD = textPromo -->
                <label>Descripcion</label><br>
                <input type="text" name="textoPromo" required><br>

                <!-- Fecha Desde  -->
                <label>Fecha Desde :</label>
                <input type="date" name="fechaDesdePromo" required>

                <!-- Fecha Hasta  -->
                <label>Fecha Hasta :</label>
                <input type="date" name="fechaHastaPromo" required><br>

                <!-- Dias de semana que estara disponible  -->
                <label>Disponibilidad semanal:</label><br>

                <!-- Permitir seleccionar todos los dias -->
                Seleccionar todo <input type="checkbox" id="seleccionarTodos" value="semCompleta"><br>

                <!-- Dias semana individual -->
                Lunes  <input type="checkbox" name="diasSemana[]" value="Lunes"><br>
                Martes<input type="checkbox" name="diasSemana[]" value="Martes"><br>
                Miercoles<input type="checkbox" name="diasSemana[]" value="Miércoles"><br>
                Jueves<input type="checkbox" name="diasSemana[]" value="Jueves"><br>
                Viernes<input type="checkbox" name="diasSemana[]" value="Viernes"><br>
                Sabado<input type="checkbox" name="diasSemana[]" value="Sábado"><br>
                Domingos<input type="checkbox" name="diasSemana[]" value="Domingo"><br>

                <!-- Seleccionar categoria cliente -->
                <label>Categoria cliente</label><br>
                <select name="categoriaCliente">
                    <option value="inicial">Inicial</option>
                    <option value="medium" >Medium</option>
                    <option value="premium">Premium</option>
                </select><br>

                <label>Imagen promocion</label><br>
                <input type="file" name="imgPromo" accept="image/*" ><br>

                <input class="button-form" type="submit" name="confirm" value="Crear promocion">
            </form>
            <?php

            // Sistema de mensajes organizados
            if(isset($_SESSION['mensaje_exito'])){
                echo "<div class='alert alert-success'><p>".$_SESSION['mensaje_exito']."</p></div>";
                unset($_SESSION['mensaje_exito']);
            }
            
            if(isset($_SESSION['mensaje_error'])){
                echo "<div class='alert alert-danger'><p>".$_SESSION['mensaje_error']."</p></div>";
                unset($_SESSION['mensaje_error']);
            }
            
            if(isset($_SESSION['mensaje_warning'])){
                echo "<div class='alert alert-warning'><p>".$_SESSION['mensaje_warning']."</p></div>";
                unset($_SESSION['mensaje_warning']);
            }
            
            if(isset($_SESSION['mensaje_info'])){
                echo "<div class='alert alert-info'><p>".$_SESSION['mensaje_info']."</p></div>";
                unset($_SESSION['mensaje_info']);
            }

            // Mensaje legacy (por compatibilidad)
            if(isset($_SESSION['mensaje'])){
                echo "<div class='alert alert-success'><p>".$_SESSION['mensaje']."</p></div>";
                unset($_SESSION['mensaje']);
            }

            ?>
        </div>
    </div>

    <script>
    // Selecciona el checkbox de "seleccionar todos"
    const seleccionarTodos = document.getElementById('seleccionarTodos');
    // Selecciona todos los checkbox de los días
    const dias = document.querySelectorAll('input[name="diasSemana[]"]');

    seleccionarTodos.addEventListener('change', function() {
        dias.forEach(dia => dia.checked = this.checked);
    });
</script>



</body>
</html>

<?php

mysqli_close($conexion);

?>