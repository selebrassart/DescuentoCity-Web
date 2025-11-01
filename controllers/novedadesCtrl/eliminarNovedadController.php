<?php

session_start();
include("../../conexionBD.php"); 

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])){

  
    $codNovedad = $_POST["codNovedad"] ?? '';
    
    if($codNovedad != ''){

     
       $consultaDelete = "DELETE FROM novedades WHERE codNovedad='$codNovedad'";

       $resultadoDelete = mysqli_query($conexion, $consultaDelete);

       if(!$resultadoDelete){
           //error
            $_SESSION["mensaje_error"] = "Error al eliminar la novedad: " . mysqli_error($conexion);
       }
       else{
           //se encontro y elimino
            if(mysqli_affected_rows($conexion) > 0){
                $_SESSION["mensaje_exito"] = "Novedad eliminada correctamente";
            } else {
                 $_SESSION["mensaje_error"] = "No se encontró la novedad con el código ingresado.";
            }
       }


    } else {
        $_SESSION["mensaje_error"] = "Código de novedad no ingresado.";
    }
}

header("location:../../views/admin/listadoNovedades.php"); 
exit();


mysqli_close($conexion); 

?>

