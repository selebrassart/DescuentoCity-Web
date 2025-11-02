<?php
session_start();
include("../conexionBD.php");


require("../funciones/funcionPasswordReset.php"); 

//Aca recibo el email para luego enviar token en pagina views/reestablecerContraseña.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar"])) {
    if (!empty($_POST["email"])) {
       $email = mysqli_real_escape_string($conexion, $_POST["email"]); 

    // 2. Buscar usuario por EMAIL en BD (usando nombreUsuario como email)
    $consulta_user = "SELECT codUsuario FROM usuarios WHERE nombreUsuario = '$email'";
    $resultado = mysqli_query($conexion, $consulta_user);

    }
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);
        $user_id = $usuario['codUsuario'];

        // Generar, guardar y enviar el TOKEN
        if (generar_y_enviar_token($conexion, $user_id, $email)) {
            $mensaje = "Se ha enviado un código de restablecimiento a su email.";
        } else {
            $mensaje = "Hubo un error.";
        }

    } else {
      
        $mensaje = "Si la dirección de email es correcta, recibirá un código de restablecimiento en breve.";
    }

    $_SESSION['mensaje_success'] = $mensaje;
    // Redirigir al formulario donde se ingresa el token y la nueva clave
    header("Location: ../views/auth/nueva-contrasena.php"); 
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
        header("Location: ../views/auth/nueva-contrasena.php"); 
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
