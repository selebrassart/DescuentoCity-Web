

<?php

session_start();

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] !== 'admin') {
    header('Location: ../../../views/auth/login.php');
    exit();
}

include("../../../conexionBD.php");

//llamo a funcion consultDueños.Donde selecciono dueños que esten pendientes.
//$listaDueños = consultaDueños($conexion);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <title>Dueños - Admin</title>
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

        $cant_por_pag =6;
        $pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;

        if(!$pagina){
            $inicio = 0;
            $pagina=1;
        }
        else{
            $inicio = ($pagina - 1) * $cant_por_pag;
        }

        //total dueños
        $consulta = "SELECT * FROM usuarios WHERE tipoUsuario='dueño'";
        $resultado = mysqli_query($conexion,$consulta);
        $total_registros = mysqli_num_rows($resultado);

        //Consulta Paginada
        $consultaDueños = "SELECT * FROM usuarios WHERE tipoUsuario='dueño' ORDER BY FIELD(LOWER(estadoUsuario), 'pendiente','activo','eliminado'), fechaRegistro  DESC LIMIT $inicio,$cant_por_pag;";
        $listaDueños = mysqli_query($conexion,$consultaDueños);

        $total_paginas = ceil($total_registros / $cant_por_pag);


        //Lista dueños pendientes.
        echo "<table class='tabla table table-striped'>";
        echo "<caption>Solicitudes de Dueños</caption>";
        echo "<tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Fecha registro</th>
                <th>Activar/Eliminar</th>
            </tr>";
        
        while($dueño = mysqli_fetch_assoc($listaDueños)){
            ?>
            <tr>
                <td> <?= $dueño["codUsuario"]    ?></td>
                <td> <?= $dueño["nombreUsuario"] ?></td>
                <td>
                    <?php
                    $estado = $dueño["estadoUsuario"];
                    $estado_class = '';
                    $estado_icon = '';
                    switch($estado) {
                        case 'pendiente':
                            $estado_class = 'bg-warning text-dark';
                            $estado_icon = 'bi bi-clock';
                            break;
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
                <td> <?= $dueño["fechaRegistro"] ?></td>
                <td>
                    <form action="../../../controllers/dueñoCtrl/activacionDueñoController.php" method="POST">
                        <input type="hidden" name="codUsuario" value="<?= $dueño['codUsuario'] ?>">
                        <input type="hidden" name="nombreUsuario" value="<?= $dueño['nombreUsuario']?>">
                        <!-- Si dueño = pendiente -->
                        <?php if($dueño["estadoUsuario"] == 'pendiente'): ?>
                            <button type="submit"  name="activar"  class="button-activar">Activar</button>
                            <button type="submit"  name="eliminar" class="button-eliminar">Eliminar</button>
                            <!-- Si dueño = activo -->
                        <?php elseif($dueño["estadoUsuario"] == 'activo'):  ?>
                            <button type="submit"  name="eliminar" class="button-eliminar">Eliminar</button>
                            <!-- Si dueño = eliminado -->
                        <?php elseif($dueño["estadoUsuario"] == 'eliminado'):  ?>
                            <button type="submit"  name="activar" class="button-activar">Activar</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
        <?php
        }
        echo "</table>"; 

        mysqli_free_result($listaDueños); //libera la memoria que fue utilizada por un resultado de consulta de base de datos
    
        mysqli_close($conexion);

        echo "<div class='paginacion mt-3'>";
        for($i = 1;$i <= $total_paginas;$i++){
            if($pagina == $i){
                echo $pagina . "";
            }
            else{
                echo "<a href='dueños.php?pagina=$i' class='btn btn-outline-primary btn-sm mx-1' id='paginacion'>$i</a>";
            }
        }
        echo "</div>";?>
    </div> <!-- Cierre del contenedor principal -->

    <?php include("../../../includes/footer.php"); ?></body>
</html>