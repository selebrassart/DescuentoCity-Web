<?php
session_start();
include("../../conexionBD.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {

    $tituloNovedad = trim(filter_input(INPUT_POST,"tituloNovedad",FILTER_SANITIZE_SPECIAL_CHARS));
    $textoNovedad = trim(filter_input(INPUT_POST,"textoNovedad",FILTER_SANITIZE_SPECIAL_CHARS));
    $fechaDesdeNovedad = trim(filter_input(INPUT_POST,"fechaDesdeNovedad",FILTER_SANITIZE_SPECIAL_CHARS));
    $fechaHastaNovedad = trim(filter_input(INPUT_POST,"fechaHastaNovedad",FILTER_SANITIZE_SPECIAL_CHARS));
    $catCliente = trim(filter_input(INPUT_POST,"categoriaCliente",FILTER_SANITIZE_SPECIAL_CHARS));
    $img = $_FILES["imgNov"] ?? null;

    if (!empty($textoNovedad) && !empty($fechaDesdeNovedad) && !empty($fechaHastaNovedad) && !empty($catCliente)) {

        $consultaNovedad = "INSERT INTO novedades (tituloNovedad, textoNovedad, fechaDesdeNovedad, fechaHastaNovedad, categoriaCliente) VALUES ('$tituloNovedad', '$textoNovedad', '$fechaDesdeNovedad', '$fechaHastaNovedad', '$catCliente')";
        $resultadoInsert = mysqli_query($conexion,$consultaNovedad);

        if ($resultadoInsert) {

            if($img && $img["error"] == 0){
                
                //Defino nombre del archivo.
                $nombreArchivo = time() . "_" . basename($img["name"]);
                $rutaDestino = "../../uploads/fondoNovedad/". $nombreArchivo;
                if(!is_dir("../../uploads/fondoNovedad/")){ 

                    //Creo carpeta o archivo
                    mkdir("../../uploads/fondoNovedad/",0777,true); 
                }

                //valido que el archivo fue subido por POST , exista ubicacion temporal , valido permisos de escritura.
                if(move_uploaded_file($img["tmp_name"],$rutaDestino)){
                    $codNovedad = mysqli_insert_id($conexion);

                    $rutaBD = "uploads/fondoNovedad/" . $nombreArchivo;
                    $insertImg = "INSERT INTO imagenes (tipoImg,nombreImg,rutaArchivo,tipoIdentidad,idIdentidad,fechaSubida) VALUES ('portada','$nombreArchivo','$rutaBD','novedad','$codNovedad',NOW())";
                    
                    $resultadoImg = mysqli_query($conexion,$insertImg);
                    
                    if($resultadoImg){
                        $_SESSION["mensaje_exito"] = "Noevedad e imagen creadas con éxito";
                    } else {
                        $_SESSION["mensaje_warning"] = "Novedad creada, pero error al guardar imagen: " . mysqli_error($conexion);
                    }
                } else {
                    $_SESSION["mensaje_warning"] = "Novedad creada, pero error al subir el archivo de imagen";
                }

            } else {
                if($img && $img["error"] != 0){
                    $_SESSION["mensaje_warning"] = "Novedad creada, pero error en archivo de imagen: " . $img["error"];
                } else {
                    $_SESSION["mensaje_exito"] = "Novedad creada sin imagen";
                }
            }

            header("location: ../../views/admin/novedades/novedades.php");
            exit();
        }
        else{
            $_SESSION["mensaje_error"] = "Error al cargar datos...";
            header("location: ../../views/admin/novedades/novedades.php");
            exit();
        }

    }
    else{
        $_SESSION["mensaje_warning"] = "Complete todos los campos";
        header("location: ../../views/admin/novedades/novedades.php");
        exit();
    }
}




?>