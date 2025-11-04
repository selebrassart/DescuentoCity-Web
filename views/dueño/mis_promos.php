<?php

session_start();

// Verificar que el usuario esté logueado y sea dueño
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] !== 'dueño') {
    header('Location: ../../views/auth/login.php');
    exit();
}

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

    echo "<div class='table-responsive'>";
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
                <form action="../../controllers/dueñoCtrl/promocionesDueñoController.php" method="POST" class="d-inline">
                    <!-- Si local esta eliminado -->
                    <?php if($fila["estadoPromo"] == 'pendiente' OR 'aprobada' OR 'denegada'):?>
                        <input type="hidden" name="codPromo" value="<?= $fila["codPromo"] ?>">
                        <button type="submit" name="eliminar" class="btn btn-secondary btn-sm rounded-circle" title="Eliminar promoción">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    <?php endif;?>
                </form>
            </td>
        </tr>
    <?php
    }
    echo "</table></div>";

    mysqli_free_result($listaPromociones);?>

    <div class='paginacion mt-3 text-center'>
    <?php
    for($i = 1;$i <= $total_paginas;$i++){
        if($pagina == $i){
            echo "<span class='btn btn-primary btn-sm mx-1'>$pagina</span>";
        }
        else{
            echo "<a href='mis_promos.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1'>$i</a>";
        }
    }
    
    ?>
    </div>

    <!-- Mensajes de alerta -->
    <div class="container">
        <?php
        // Sistema de mensajes organizados
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

    <!-- Formulario de crear promoción -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">

                <h1 class="text-center mb-4">Crear Promoción</h1>

                <form action="../../controllers/dueñoCtrl/promocionesDueñoController.php" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-white">
                    
                    <div class=" mb-3">
                        <label for="fechaDesde" class="form-label fw-bold"><i class="bi bi-card-text"></i> Descripcion</label>
                        <input type="text" class="form-control" name="textoPromo" placeholder="Descripción de la promoción" aria-label="Descripción" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fechaDesde" class="form-label fw-bold"><i class="bi bi-calendar-check"></i> Fecha Desde</label>
                            <input type="date" class="form-control" name="fechaDesdePromo" id="fechaDesde" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fechaHasta" class="form-label fw-bold"><i class="bi bi-calendar-x"></i> Fecha Hasta</label>
                            <input type="date" class="form-control" name="fechaHastaPromo" id="fechaHasta" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="bi bi-calendar-week"></i> Disponibilidad Semanal</label>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="seleccionarTodos" value="semCompleta">
                            <label class="form-check-label fw-bold" for="seleccionarTodos">
                                Seleccionar todos los días
                            </label>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="diasSemana[]" value="Lunes" id="lunes">
                                    <label class="form-check-label" for="lunes">Lunes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="diasSemana[]" value="Martes" id="martes">
                                    <label class="form-check-label" for="martes">Martes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="diasSemana[]" value="Miércoles" id="miercoles">
                                    <label class="form-check-label" for="miercoles">Miércoles</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="diasSemana[]" value="Jueves" id="jueves">
                                    <label class="form-check-label" for="jueves">Jueves</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="diasSemana[]" value="Viernes" id="viernes">
                                    <label class="form-check-label" for="viernes">Viernes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="diasSemana[]" value="Sábado" id="sabado">
                                    <label class="form-check-label" for="sabado">Sábado</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="diasSemana[]" value="Domingo" id="domingo">
                                    <label class="form-check-label" for="domingo">Domingo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-star"></i></span>
                        <select class="form-select" name="categoriaCliente" aria-label="Categoría Cliente" required>
                            <option value="">Seleccionar categoría de cliente</option>
                            <option value="inicial">Inicial</option>
                            <option value="medium">Medium</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="imgPromo" class="form-label fw-bold"><i class="bi bi-image"></i> Imagen de la Promoción</label>
                        <input type="file" class="form-control" name="imgPromo" id="imgPromo" accept="image/*">
                        <div class="form-text">Selecciona una imagen para la promoción (JPG, PNG, GIF)</div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="confirm" class="btn btn-primary btn-lg rounded-pill">
                            <i class="bi bi-plus-circle"></i> Crear Promoción
                        </button>
                    </div>
                </form>
            </div>
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

    <?php 
    include("../../includes/footer.php");
    mysqli_close($conexion);

    ?>

</body>
</html>



