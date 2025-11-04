
<?php

include("../conexionBD.php");
require ("../funciones/funcionesSQL.php");


//Recibo datos email y token
$email =  $_GET["email"] ?? '';
$token = $_GET["token"] ?? '';

// verificar token (boton verificar PRESIONADO) y estadoUsuario = pendiente.
// Si es correcto. EstadoUsuario -> activo.
// Aviso de cuenta activada. Le dejo link para redirigir a inicio de sesion.

if(!empty($email) && !empty($token)){

    //Consulto datos en BD
    $resulado = consultaSQL($conexion,$email,$token);
    if(mysqli_num_rows($resulado) > 0){

        //Actualiza datos en BD
        $resultado_update = update_estado_SQL($conexion,$email);
        if($resultado_update){
            echo "✅ Cuenta activada correctamente. <a href='/views/auth/login.php'>Iniciar sesión </a>";
        }
        else{
        echo "⚠️ Error al actualizar la cuenta. Intente nuevamente.";
        }

    }
    else {
        echo "⚠️ Token inválido, enlace expirado o cuenta ya activada.";
    }

}
else {
    echo "⚠️ Enlace inválido.";
}

mysqli_close($conexion);

?>