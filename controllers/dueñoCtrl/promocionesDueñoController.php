<?php

session_start();

include("../../conexionBD.php");

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm"])){


    $textoPromo = trim(filter_input(INPUT_POST,"textoPromo",FILTER_SANITIZE_SPECIAL_CHARS));
    $fechaDesde = trim(filter_input(INPUT_POST,"fechaDesdePromo",FILTER_SANITIZE_SPECIAL_CHARS));
    $fechaHasta = trim(filter_input(INPUT_POST,"fechaHastaPromo",FILTER_SANITIZE_SPECIAL_CHARS));
    $diasSemana = isset($_POST["diasSemana"]) ? implode(',', $_POST["diasSemana"]) : ''; //implode convierte array en string
    $catCliente = trim(filter_input(INPUT_POST,"categoriaCliente",FILTER_SANITIZE_SPECIAL_CHARS));
    $img = $_FILES["imgPromo"] ?? null;

    if(!empty($textoPromo) && !empty($fechaDesde) && !empty($fechaHasta) && !empty($diasSemana) && !empty($catCliente)){

        //Consultar local de dueño.
        $codDueño = $_SESSION["codUsuario"];
        $consultaLocal = "SELECT codLocal FROM locales WHERE codUsuario='$codDueño' AND estadoLocal='activo'";
        $resultadoLocal = mysqli_query($conexion,$consultaLocal);


        //Si dueño tiene local asociado.
        if($resultadoLocal && mysqli_num_rows($resultadoLocal) > 0){
            $local = mysqli_fetch_assoc($resultadoLocal);
            $codLocal = $local["codLocal"];
        } else {
            $_SESSION["mensaje_error"] = "No se encontró un local activo para este usuario";
            header("location: ../../views/dueño/mis_promos.php");
            exit();
        }
        

        //inserto datos de promocion.
        $consultaPromo = "INSERT INTO promociones (textoPromo,fechaDesdePromo,fechaHastaPromo,categoriaCliente,diasSemana,codLocal) VALUES ('$textoPromo','$fechaDesde','$fechaHasta','$catCliente','$diasSemana',$codLocal)";

        $resultadoInsert = mysqli_query($conexion,$consultaPromo);

        if($resultadoInsert){

            if($img && $img["error"] == 0){
                
                //Defino nombre del archivo.
                $nombreArchivo = time() . "_" . basename($img["name"]);
                $rutaDestino = "../../uploads/fondoPromo/". $nombreArchivo;

                //si no existe la carpeta o archivo.
                if(!is_dir("../../uploads/fondoPromo/")){ 

                    //Creo carpeta o archivo
                    mkdir("../../uploads/fondoPromo/",0777,true); 
                }

                //valido que el archivo fue subido por POST , exista ubicacion temporal , valido permisos de escritura.

                if(move_uploaded_file($img["tmp_name"],$rutaDestino)){

                    $codPromo = mysqli_insert_id($conexion);

                    $rutaBD = "uploads/fondoPromo/" . $nombreArchivo;
                    $insertImg = "INSERT INTO imagenes (tipoImg,nombreImg,rutaArchivo,tipoIdentidad,idIdentidad,fechaSubida) VALUES ('portada','$nombreArchivo','$rutaBD','promocion','$codPromo',NOW())";
                    
                    $resultadoImg = mysqli_query($conexion,$insertImg);
                    
                    if($resultadoImg){
                        $_SESSION["mensaje_exito"] = "Promoción e imagen creadas con éxito";
                    } else {
                        $_SESSION["mensaje_warning"] = "Promoción creada, pero error al guardar imagen: " . mysqli_error($conexion);
                    }
                } else {
                    $_SESSION["mensaje_warning"] = "Promoción creada, pero error al subir el archivo de imagen";
                }

            } else {
                if($img && $img["error"] != 0){
                    $_SESSION["mensaje_warning"] = "Promoción creada, pero error en archivo de imagen: " . $img["error"];
                } else {
                    $_SESSION["mensaje_exito"] = "Promoción creada sin imagen";
                }
            }

            header("location: ../../views/dueño/mis_promos.php");
            exit();
        }
        else{
            $_SESSION["mensaje_error"] = "Error al cargar datos...";
            header("location: ../../views/dueño/mis_promos.php");
            exit();
        }

    }
    else{
        $_SESSION["mensaje_warning"] = "Complete todos los campos";
        header("location: ../../views/dueño/mis_promos.php");
        exit();
    }
}    

elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])){

    $codPromo = $_POST["codPromo"] ?? '';

    // Verificar si existen existen solicitudes para la promociones que quiero eliminar.

    $consultaVerificacion = "SELECT id_solicitud FROM solicitudes_descuentos WHERE codPromo = $codPromo";
    $resultadoVerificacion = mysqli_query($conexion, $consultaVerificacion);
    $verificacion = mysqli_fetch_assoc($resultadoVerificacion);
    
    if($verificacion && mysqli_num_rows($resultadoVerificacion) > 0) {
        // Si existen solicitudes aviso que no se puede eleminar.

        $mensaje_error = "No es posible eliminar promocion. Existen solicitudes activas en este momento.";

    } else {
        // Si no existen solicitudes, elimino promocion
        $consultaPromo = "DELETE FROM promociones WHERE codPromo = $codPromo";
        $mensaje_exito = "Promoción eliminada correctamente.";
        $mensaje_error = "Error al eliminar la promoción.";    
        $resultado = mysqli_query($conexion, $consultaPromo);
    }
    

    if($resultado){
        $_SESSION["mensaje_exito"] = $mensaje_exito;
        header("location: ../../views/dueño/mis_promos.php");
        exit();
    }
    else{
        $_SESSION["mensaje_error"] = $mensaje_error;
        header("location: ../../views/dueño/mis_promos.php");
        exit();
    }

}

mysqli_close($conexion);

?>