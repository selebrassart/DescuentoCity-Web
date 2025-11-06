
<?php
echo "Cargando PHPMailer...<br>";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require __DIR__ . '/../vendor/autoload.php';



function enviar_mail($email, $tipoUsuario, $estadoUsuario, $token) {
    $mail = new PHPMailer(true);

    try {
       
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'descuentocity@descuentocity.shop';  
        $mail->Password   = 'Descuentocity123!'; //solicitar contraseña especial a admin           
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      
        $mail->Port       = 587;                                 
        $mail->CharSet    = 'UTF-8';

        
        $mail->setFrom('descuentocity@descuentocity.shop', 'Descuento City');
        $mail->addReplyTo('descuentocity@descuentocity.shop', 'Descuento City');
        $mail->addAddress($email);

        
        if ($tipoUsuario == "cliente") {
            $mail->Subject = 'Activa tu cuenta - Descuento City';
            $url = "https://descuentocity.shop/controllers/activacionController.php?email=$email&token=$token";
            $mail->Body = "
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
            ";
        } elseif ($tipoUsuario == "dueño" && $estadoUsuario == "activo") {
            $mail->Subject = 'Tu cuenta fue activada - Descuento City';
            $mail->Body = "
                <h1>Descuento City</h1>
                <p>Tu cuenta fue activada por el administrador.</p>
                <a href='https://descuentocity.shop/views/auth/login.php' style='
                    display:inline-block;
                    padding:10px 20px;
                    background-color:#007bff;
                    color:white;
                    text-decoration:none;
                    border-radius:5px;'>Iniciar sesión</a>
            ";
        } elseif ($tipoUsuario == "dueño" && $estadoUsuario == "eliminado") {
            $mail->Subject = 'Cuenta bloqueada - Descuento City';
            $mail->Body = "<h1>Descuento City</h1><p>Tu cuenta fue BLOQUEADA.</p>";
        }

        $mail->isHTML(true);
        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
        return false;
    }
}




// Encabezados adicionales
/*
$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
$headers[] = 'From: Cumpleaños <cumpleanos@example.com>';
$headers[] = 'Cc: cumpleanos_archivo@example.com';
$headers[] = 'Bcc: cumpleanos_verif@example.com';
*/
?>