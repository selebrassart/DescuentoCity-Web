<?php

session_start();

include("../../conexionBD.php"); 

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])){

  
    $codNovedad = $_POST["codNovedad"] ?? '';
    
    $consultaDelete = "UPDATE novedades SET estado='eliminada' WHERE codNovedad='$codNovedad'";

    $resultadoDelete = mysqli_query($conexion, $consultaDelete);

    if($resultadoDelete){

        $_SESSION['mensaje_exito'] = "Novedad eliminada correctamente";
        header("location:../../views/admin/novedades/novedades.php"); 
        exit();
    }
    else{
        
        $_SESSION['mensaje_error'] = "Error al eliminar novedad";
        header("location:../../views/admin/novedades/novedades.php"); 
        exit();

    }

}

header("location:../../views/admin/novedades/novedades.php"); 
exit();


mysqli_close($conexion); 

?>

