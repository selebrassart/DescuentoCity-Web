<?php

session_start();

include("../conexionBD.php");

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje_warning'] = "Método no permitido";
    header("Location: ../views/editarDatos.php");
    exit;
}

//Recupero datos de formulario editarDatos

if(isset($_POST["confirm"])){

    $codUsuario = $_POST["codUsuario"];
    $nombreUsuario = trim(filter_input(INPUT_POST,"nombreUsuario",FILTER_SANITIZE_EMAIL));
    $claveUsuario = trim(filter_input(INPUT_POST,"claveUsuario",FILTER_SANITIZE_SPECIAL_CHARS));
    $claveUsuario2 = trim(filter_input(INPUT_POST,"claveUsuario2",FILTER_SANITIZE_SPECIAL_CHARS));

    if(!empty($nombreUsuario)  && !empty($claveUsuario)){

        //Si usuario quiere editar solo contraseña. No modifica email 
        $consultaEmail = "SELECT nombreUsuario , claveUsuario FROM usuarios WHERE codUsuario='$codUsuario'"; 
        $resultado1 = mysqli_query($conexion,$consultaEmail);
        $datos = mysqli_fetch_assoc($resultado1);

        if($datos["nombreUsuario"] == $nombreUsuario){ //Verifico que email no se haya modificado. => Actualizo solo contraseña.
            if($claveUsuario == $claveUsuario2){

                $clave_hash = password_hash($claveUsuario, PASSWORD_DEFAULT);
                $consultaUpdate = "UPDATE usuarios SET claveUsuario='$clave_hash' WHERE codUsuario='$codUsuario'";
                $resultado1 = mysqli_query($conexion,$consultaUpdate);

                if($resultado1){
                    $_SESSION['mensaje_exito'] = "Contraseña modificada correctamente";
                    header("Location: ../views/editarDatos.php");
                }
                else{
                    $_SESSION['mensaje_error'] = "Error al guardad dato";
                    header("Location: ../views/editarDatos.php");
                    exit();
                }

            }
            elseif($claveUsuario2 == ''){
                $_SESSION['mensaje_warning'] = "No se modifico ningun dato";
                header("Location: ../views/editarDatos.php");
                exit();
            }
            else{
                $_SESSION['mensaje_error'] = "Contraseñas no coinciden";
                header("Location: ../views/editarDatos.php");
                exit();
            }

        }
        else{ // Si email seleccionado no coindice con el nuevo

            //Verifico si nombre de usuario ya existe
            $consultaUsuario = "SELECT * FROM usuarios WHERE nombreUsuario='$nombreUsuario'";
            $resultado = mysqli_query($conexion,$consultaUsuario);
            if(mysqli_num_rows($resultado) == 0){ //Si no existe

                if($claveUsuario2 == ''){ // Actualizo solo mail 

                    $consultaUpdate = "UPDATE usuarios SET nombreUsuario='$nombreUsuario' WHERE codUsuario='$codUsuario'";
                    $resultado2 = mysqli_query($conexion,$consultaUpdate);
                    mysqli_query($conexion,$consultaUpdate);
                    if($resultado2){
                        $_SESSION['mensaje_exito'] = "Email actualizados correctamente";
                        header("Location: ../views/editarDatos.php");
        
                    }
                    else{
                        $_SESSION['mensaje_error'] = "Error al guardar dato";
                        header("Location: ../views/editarDatos.php");
                        exit();
        
                    }

                }
                else{ // Actualizo todo 

                    if($claveUsuario == $claveUsuario2){

                        $clave_hash = password_hash($claveUsuario, PASSWORD_DEFAULT);
                        $consultaUpdate = "UPDATE usuarios SET claveUsuario='$clave_hash' , nombreUsuario='$nombreUsuario' WHERE codUsuario='$codUsuario'";
                        $resultado3 = mysqli_query($conexion,$consultaUpdate);
                        if($resultado3){
                            $_SESSION['mensaje_exito'] = " Email y contraseña actualizados correctamente ";
                            header("Location: ../views/editarDatos.php");
            

                        }else{
                            $_SESSION['mensaje_error'] = "Error al guardar dato";
                            header("Location: ../views/editarDatos.php");
                            exit();
            
                        }

                    }
                    else{
                        $_SESSION['mensaje_error'] = "Contraseñas no coinciden ";
                        header("Location: ../views/editarDatos.php");
                        exit();

                    }

                }

            }
            else{

                $_SESSION['mensaje_warning'] = " Email ya existente ";
                header("Location: ../views/editarDatos.php");
                exit();

            }

        }

        $_SESSION['nombreUsuario'] = $nombreUsuario;   
        $_SESSION['claveUsuario'] = $claveUsuario;

    }else{
        $_SESSION['mensaje_warning'] = " Complete los campos ";
        header("Location: ../views/editarDatos.php");
        exit();
    }

}