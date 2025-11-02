<?php

session_start();
include("../conexionBD.php");

// Verifico si solicitud http se realizo utilizando POST y si se presiono el input confirm
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm"])) {
    if (!empty($_POST["email"]) && !empty($_POST["clave"])) {
        $email = $_POST["email"];
        $clave = $_POST["clave"];

        // Busco usuario por EMAIL en BD
        $consulta_user = "SELECT * FROM usuarios WHERE nombreUsuario = '$email'";
        $resultado = mysqli_query($conexion, $consulta_user);


        //LLamo archivo donde esta guardar_datos_user()
        require("../funciones/funcionLogin.php");
        
       
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $usuario = mysqli_fetch_assoc($resultado);
            // Comparo la clave ingresada con la clave hash de la BD
            if (password_verify($clave, $usuario["claveUsuario"])) {

                // Verificar si es activo
                if ($usuario["estadoUsuario"] == "activo") {

                    // Verificar tipo de usuario
                    if ($usuario["tipoUsuario"] == "cliente") {

                        // Guardo datos de usuario
                        guardar_datos_user($usuario);
                        header("Location: ../views/cliente/promociones.php");
                        exit();

                    } elseif ($usuario["tipoUsuario"] == "dueño") {
                        guardar_datos_user($usuario);
                        header("Location: ../views/dueño/solicitudes.php");
                        exit();

                    } elseif ($usuario["tipoUsuario"] == "admin") { // Admin
                        guardar_datos_user($usuario);
                        header("Location: ../views/admin/dueños.php");
                        exit();
                    }

                } elseif ($usuario["estadoUsuario"] == "pendiente") {
                    $_SESSION['mensaje_warning'] = "Cuenta no activada";
                    header("Location: ../views/auth/login.php");
                    exit();

                } else {
                    $_SESSION['mensaje_error'] = "Cuenta se encuentra bloqueada";
                    header("Location: ../views/auth/login.php");
                    exit();
                }
            } else {
                // Contraseña incorrecta
                $_SESSION['mensaje_error'] = "Contraseña incorrecta";
                header("Location: ../views/auth/login.php");
                exit();
            }

        } else {
            $_SESSION['mensaje_error'] = "Usuario no encontrado";
            header("Location: ../views/auth/login.php");
            exit();
        }
    } else {
        // Faltan datos
        $_SESSION['mensaje_warning'] = "Complete todos los campos";
        header("Location: ../views/auth/login.php");
        exit();
    }
}

mysqli_close($conexion);


?>




