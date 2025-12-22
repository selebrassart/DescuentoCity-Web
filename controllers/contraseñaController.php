<?php
session_start();

include("../conexionBD.php");


require("../funciones/funcionPasswordReset.php"); 

//Aca recibo el email para luego enviar token en pagina views/reestablecerContraseña.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar"])) {
    // Mensaje genérico por seguridad
    $mensaje_seguro = "Si la dirección de email es correcta, recibirá un código de restablecimiento en breve.";
    $email = '';

    if (!empty($_POST["email"])) {
        // Escapar el email para evitar inyecciones SQL
        $email = mysqli_real_escape_string($conexion, $_POST["email"]); 

        // Buscar usuario por email (nombreUsuario)
        $consulta_user = "SELECT codUsuario FROM usuarios WHERE nombreUsuario = '$email'";
        $resultado = mysqli_query($conexion, $consulta_user);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $usuario = mysqli_fetch_assoc($resultado);
            $user_id = $usuario['codUsuario'];
    
            // Generar, guardar y enviar el TOKEN
            if (generar_y_enviar_token($conexion, $user_id, $email)) {
                $_SESSION['mensaje_exito'] = "Se ha enviado un código de restablecimiento a su email.";
            } 
            else {
                $_SESSION['mensaje_error'] = "Hubo un error al generar o enviar el token.";
            }

        } else {
            // Email no encontrado: mensaje genérico
            $mensaje = $mensaje_seguro;
        }

    } else {
        // Si el email estaba vacío, mostramos un warning más específico.
        $_SESSION['mensaje_warning'] = "Debe ingresar un email para iniciar el proceso.";
    }

    // Redirigir al formulario donde se ingresa el token y la nueva clave
    header("Location: ../views/auth/nueva-contraseña.php"); 
    exit();
}

 //Ahora cuando ingresa LA NUEVA CONTRASEÑA (click en boton REESTABLECER en pagina views/nueva-contraseña.php)
 //donde el usuario ingresa el token recibido por mail y la nueva contraseña elegida
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["restablecer"])) {

    //  Verificar que todos los campos POST estén completos
    if (empty($_POST["token_code"]) || empty($_POST["new_clave"]) || empty($_POST["confirm_clave"])) {
        $_SESSION['mensaje_warning'] = "Complete todos los campos: código y contraseñas.";
        header("Location: ../views/auth/nueva-contraseña.php"); 
        exit();
    }
   
    $token_code    = mysqli_real_escape_string($conexion, $_POST["token_code"]);
    $new_clave     = $_POST["new_clave"];
    $confirm_clave = $_POST["confirm_clave"];
    
    // Verificar que las contraseñas coincidan
    if ($new_clave !== $confirm_clave) {
        $_SESSION['mensaje_error'] = "Las contraseñas no coinciden.";
        header("Location: ../views/auth/nueva-contraseña.php");
        exit();
    }

    // Validar token, verificar expiración y cambiar contraseña (función en funcionPasswordReset.php)
    $resultado_cambio = validar_token_y_cambiar_clave($conexion, $token_code, $new_clave);

    if ($resultado_cambio === true) {
      
        $_SESSION['mensaje_success'] = "Contraseña cambiada con éxito. Inicie sesión.";
        header("Location: ../views/auth/login.php"); 
        exit();
    } elseif ($resultado_cambio === "token_invalido") {
       
        $_SESSION['mensaje_error'] = "Código de verificación no válido";
        header("Location: ../views/auth/nueva-contraseña.php"); 
        exit();
    } else {
        // Otro error (ej. fallo de DB al actualizar)
        $_SESSION['mensaje_error'] = "Error desconocido al actualizar la contraseña.";
        header("Location: ../views/auth/nueva-contraseña.php"); 
        exit();
    }
}



mysqli_close($conexion);

?>
