<?php

$rol = $_GET['rol'] ?? null;
$email = $_GET['email'] ?? null;
$token = $_GET['token'] ?? null;

require("../../funciones/funcionesMail.php");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Descuento-City/assets/css/estilos.css">
    <title>Registro Exitoso - Descuento City</title>
    <link rel="icon" type="image/png" href="/Descuento-City/assets/img/logo-ventana/logo-fondo-b-circular.png"/>
</head>
<body>
    <div>
        <h2>Registro Exitoso !</h2>
        
        <!-- tipoUsuario = cliente -->
        <?php if($rol == "cliente"): 


        
        //Mando email de confirmacion
        if(enviar_mail($email,$rol,"pendiente",$token)){
            echo "Se envio email a $email. Por favor revisa tu bandeja de entrada. <br>";
            echo "Puedes cerrar esta ventana si lo desea.<br>";
        }
        else{
            echo "Hubo un error en mandar email.";
        }
            
        ?>
    

        <?php elseif($rol == "dueño"): ?>
        <p> Tu cuenta fue creada con exito.</p>
        <p> Administrador verificara tu identidad.Te notificaremos por correo.</p>
        
        <!-- Recibe notificacion vial mail cuando ADMIN aprobo/rechazo registro de cuenta. -->

        <?php endif; ?>

        <a href="../../index.php">Volver al inicio</a>
    </div>

    <?php include("../../includes/footer.php"); ?>
</body>
</html>
