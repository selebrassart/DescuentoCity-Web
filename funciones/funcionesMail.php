

<?php

function enviar_mail($email,$tipoUsuario,$estadoUsuario,$token){
    $destino  = $email;     
    // Para enviar un correo HTML, el encabezado Content-type debe ser definido    
    $header = "MIME-Version: 1.0\r\n";
    $header .= "Content-type:text/html; charset=iso-8859-1\r\n";
    $header .= "From: <noreply45@example.com>" ."\r\n";

    if($tipoUsuario == "cliente"){

        $asunto = 'Activar cuenta - DESCUENTO CITY.';

        $url = "http://descuentocity.shop/controllers/activacionController.php?email=$email&token=$token";

        $cuerpo = "
        <html>
            <body>
                <h1>Bienvenido a Descuento City</h1>
                <p>Haz clic en el botón para activar tu cuenta:</p>    
                <a href='$url' style='
                    display:inline-block;
                    padding:10px 20px;
                    background-color:#007bff;
                    color:white;
                    text-decoration:none;
                    border-radius:5px;'>Activar cuenta</a>
                <p>Si no creaste esta cuenta, ignora este correo.</p>
                
            </body>
        </html>
        ";

    }
    elseif($tipoUsuario == "dueño" && $estadoUsuario == "activo" ){ //tipo de usuario es DUEÑO
        $asunto = 'Estado cuenta - DESCUENTO CITY';
        $cuerpo = "
        <html>
            <body>
                <h1>Descuento City</h1>
                <p>Tu cuenta ya fue activada por el administrador.</p>
                <p>Ya puedes iniciar sesion en el sitio.</p>
                <a href='http:/descuentocity.shop/views/auth/login.php' style='
                    display:inline-block;
                    padding:10px 20px;
                    background-color:#007bff;
                    color:white;
                    text-decoration:none;
                    border-radius:5px;'>Iniciar sesion</a>
            </body>
        </html>
        ";
    }
    elseif($tipoUsuario == "dueño" && $estadoUsuario == "eliminado" ){ //Cuenta eliminada.

        $asunto = 'Estado cuenta - DESCUENTO CITY';
        $cuerpo = '
        <html>
            <body>
                <h1>Descuento City</h1>
                <p>Tu cuenta fue BLOQUEADA.</p>
            </body>
        </html>
        ';
    }

    // Envío Email
    return mail($destino, $asunto, $cuerpo , $header);

}








// Encabezados adicionales
/*
$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
$headers[] = 'From: Cumpleaños <cumpleanos@example.com>';
$headers[] = 'Cc: cumpleanos_archivo@example.com';
$headers[] = 'Bcc: cumpleanos_verif@example.com';
*/


?>
