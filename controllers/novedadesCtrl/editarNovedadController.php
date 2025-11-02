<?php

session_start();

include("../../conexionBD.php"); 


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guardarCambios"])){


    $codNovedad = $_POST["codNovedad"] ?? '';
    $textoNovedad = $_POST["textoNovedad"] ?? '';
    $fechaDesdeNovedad = $_POST["fechaDesdeNovedad"] ?? '';
    $fechaHastaNovedad = $_POST["fechaHastaNovedad"] ?? '';
    $tipoUsuario = $_POST["tipoUsuario"] ?? '';
    
   if(empty($codNovedad) || empty($textoNovedad) || empty($fechaDesdeNovedad) || empty($fechaHastaNovedad) || empty($tipoUsuario)){
        $_SESSION["mensaje_error"] = "Todos los campos son obligatorios para actualizar.";
    } else {
       $consultaUpdate = "UPDATE novedades SET 
                            textoNovedad='$textoNovedad',
                            fechaDesdeNovedad='$fechaDesdeNovedad',
                            fechaHastaNovedad='$fechaHastaNovedad',
                            tipoUsuario='$tipoUsuario' 
                          WHERE codNovedad='$codNovedad'";

       $resultadoUpdate = mysqli_query($conexion, $consultaUpdate);

       if(!$resultadoUpdate){
            // error de la consulta
            $_SESSION["mensaje_error"] = "Error al actualizar la novedad: " . mysqli_error($conexion);
       }
       else{
            // Verificación de filas afectadas y mensaje de éxito
            if(mysqli_affected_rows($conexion) > 0){
                $_SESSION["mensaje_exito"] = "Novedad actualizada correctamente";
            } else {
                 //  si los datos eran iguales 
                 $_SESSION["mensaje_aviso"] = "No se realizaron cambios o no se encontró la novedad.";
            }
       }
    } else {
        $_SESSION["mensaje_error"] = "Todos los campos son obligatorios para actualizar.";
    }
}

header("location:../../views/admin/listadoNovedades.php"); 
exit();


mysqli_close($conexion); 

?>