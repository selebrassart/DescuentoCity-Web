<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // ajustá si tu vendor está en otra carpeta

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar"])) {

    $nombre = filter_input(INPUT_POST, "nombre", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL));
    $asuntoRecibido = trim(filter_input(INPUT_POST, "asunto", FILTER_SANITIZE_SPECIAL_CHARS));
    $mensaje = trim(filter_input(INPUT_POST, "mensaje", FILTER_SANITIZE_SPECIAL_CHARS));

    if (!empty($nombre) && !empty($email) && !empty($asuntoRecibido) && !empty($mensaje)) {

        $mail = new PHPMailer(true);

        try {
            // CONFIG SMTP HOSTINGER
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'descuentocity@descuentocity.shop';
            $mail->Password   = '';//solicitar contraseña especial a admin 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // REMITENTE Y DESTINO
            $mail->setFrom('descuentocity@descuentocity.shop', 'Descuento City');
            $mail->addReplyTo($email, $nombre);
            $mail->addAddress('descuentocity@descuentocity.shop', 'Soporte Descuento City'); // destinatario real

            // CONTENIDO
            $mail->isHTML(true);
            $mail->Subject = 'Mensaje de contacto DC - ' . $asuntoRecibido;
            $mail->Body = "
                <html>
                    <head><title>Mensaje de contacto</title></head>
                    <body>
                        <h2>Nuevo mensaje de contacto</h2>
                        <p><strong>Nombre:</strong> " . htmlspecialchars($nombre) . "</p>
                        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                        <p><strong>Asunto:</strong> " . htmlspecialchars($asuntoRecibido) . "</p>
                        <p><strong>Mensaje:</strong></p>
                        <div style='background:#f5f5f5; padding:15px; border-left:4px solid #007bff;'>
                            " . nl2br(htmlspecialchars($mensaje)) . "
                        </div>
                        <hr>
                        <p><small>Este mensaje fue enviado desde el formulario de contacto de Descuento City</small></p>
                    </body>
                </html>
            ";

            $mail->send();
            $_SESSION["mensaje_exito"] = "Mensaje enviado correctamente. Te responderemos pronto.";

        } catch (Exception $e) {
            $_SESSION["mensaje_error"] = "Error al enviar: " . $mail->ErrorInfo;
        }

    } else {
        $_SESSION["mensaje_error"] = "Complete todos los campos.";
    }

    header("Location: ../contacto.php");
    exit();

} 


    