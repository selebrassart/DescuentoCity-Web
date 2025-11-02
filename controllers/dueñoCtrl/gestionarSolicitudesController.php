<?php

session_start();

include("../../conexionBD.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $codDueño = $_SESSION["codUsuario"];
    $id_solicitud = $_POST["id_solicitud"] ?? '';

    
    // Verificar que la solicitud pertenezca a una promoción del local del dueño
    $consultaVerificar = "SELECT sd.id_solicitud, sd.estado, sd.codCliente, sd.codPromo, p.codLocal, l.codUsuario, u.nombreUsuario as cliente
                      FROM solicitudes_descuentos sd
                      JOIN promociones p ON sd.codPromo = p.codPromo
                      JOIN locales l ON p.codLocal = l.codLocal
                      JOIN usuarios u ON sd.codCliente = u.codUsuario
                      WHERE sd.id_solicitud = '$id_solicitud' AND l.codUsuario = '$codDueño'";
    
    $resultado_verificar = mysqli_query($conexion, $consultaVerificar);
    
    if (!$resultado_verificar || mysqli_num_rows($resultado_verificar) == 0) {
        $_SESSION["mensaje_error"] = "Solicitud no encontrada o no autorizada";
        header("location:../../views/dueño/solicitudes.php");
        exit();
    }
    
    $solicitud = mysqli_fetch_assoc($resultado_verificar);

    $codCliente = $solicitud["codCliente"];
    $codPromo = $solicitud["codPromo"];


    // Actualizo estado
    $nuevo_estado = '';
    $mensaje = '';

    if(isset($_POST["aceptar"])){
        $nuevo_estado = "aceptada";
        $mensaje = " aceptada";
    }
    elseif(isset($_POST["rechazar"])){
        $nuevo_estado = "rechazada";
        $mensaje = "rechazada";

    }
    elseif(isset($_POST["eliminar"])){
        $nuevo_estado = "eliminada";
        $mensaje = "eliminada";


    }
    
    
    // Actualizo estado
    $sql_actualizar = "UPDATE solicitudes_descuentos SET estado = '$nuevo_estado' WHERE id_solicitud = '$id_solicitud'";
    
    $resultado_actualizar = mysqli_query($conexion, $sql_actualizar);
    
    if ($resultado_actualizar) {

        $_SESSION["mensaje_exito"] = "Solicitud de " .$solicitud['cliente']." ha sido $mensaje exitosamente";

        //Consulo usos de cliente
        if($nuevo_estado != "eliminada"){

            $consultaUsos = "SELECT * FROM uso_promociones WHERE codCliente='$codCliente' AND codPromo='$codPromo'";
            $resultadoUso = mysqli_query($conexion,$consultaUsos);
            if($resultadoUso && mysqli_num_rows($resultadoUso)){

                if($nuevo_estado == "aceptada" || $nuevo_estado == "rechazada" ){
                    $consultaUpdate = "UPDATE uso_promociones SET estado='$nuevo_estado' WHERE codCliente='$codCliente' AND codPromo='$codPromo'";
                    $resultadoUpdate = mysqli_query($conexion,$consultaUpdate);

                    if(!$resultadoUpdate){
                        $_SESSION["mensaje_error"] = "Error al actualizar uso de promocion del cliente " . mysqli_error($conexion);
                    }

                }
            }
            else{ //Si no exite uso , inserto el nuevo.
                $consultaInsert = "INSERT INTO uso_promociones (codCliente,codPromo,estado) VALUES ('$codCliente','$codPromo','$nuevo_estado')";

                $resultadoInsert = mysqli_query($conexion,$consultaInsert);

                if(!$resultadoInsert){
                    $_SESSION["mensaje_error"] = "Error al registrar el uso de promoción al cliente: " . mysqli_error($conexion);

                }
            }
        }


    } else {
        $_SESSION["mensaje_error"] = "Error al procesar la solicitud: " . mysqli_error($conexion);
    }


    
} else {
    $_SESSION["mensaje_error"] = "Método no permitido";
}

mysqli_close($conexion);
header("location:../../views/dueño/solicitudes.php");
exit();

?>