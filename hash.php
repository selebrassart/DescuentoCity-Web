

<?php

$contraseña  = "admin123";

$hash = password_hash($contraseña, PASSWORD_DEFAULT);

echo $hash;




/*
session_start();

require("../../conexionBD.php");

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm"])){


    $codLocal = $_POST["codLocal"] ?? 0;    
    $nombre = $_POST["nombreLocal"];
    $rubro = $_POST["rubroLocal"];
    $ubicacion = $_POST["ubicacionLocal"];
    $existeLogo = $_POST['existeLogo'] ?? '';

    //Actualizo local
    $consulta = "UPDATE locales SET  nombreLocal = '$nombre', ubicacionLocal='$ubicacion', rubroLocal='$rubro' WHERE codLocal = $codLocal ";
    mysqli_query($conexion,$consulta);

    // Manejo de archivo si subieron uno nuevo
    if(isset($_FILES['nuevoLogo']) && $_FILES['nuevoLogo']['error'] === UPLOAD_ERR_OK){

        $tmp = $_FILES['nuevoLogo']['tmp_name'];

        // valida imagen
        $info = getimagesize($tmp);
        if($info === false){
            
            $_SESSION['mensaje'] = "Archivo no es una imagen válida.";
            header("Location: /views/admin/locales/localUpdate.php?codLocal=$codLocal");
            exit();
        }

        $ext = image_type_to_extension($info[2]);
        $uploadDir = __DIR__ . '/../../../uploads/locales/';
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $nombreArchivo = "logo_{$codLocal}_" . time() . $ext;
        $dest = $uploadDir . $nombreArchivo;

        if(move_uploaded_file($tmp, $dest)){
            // elimino logo anterior si existe y no es vacío
            if(!empty($existingLogo)){
                $oldPath = __DIR__ . '/../../../' . ltrim($existeLogo, '/\\');
                if(file_exists($oldPath)) unlink($oldPath);
            }

            // ruta relativa para guardar en DB (ej: uploads/locales/logo_...)
            $rutaRel = 'uploads/locales/' . $nombreArchivo;

            // Si ya existe fila en imagenes para este idIdentidad/tipoImg -> update, si no -> insert
            $check = mysqli_query($conexion, "SELECT idImg FROM imagenes WHERE idIdentidad='$codLocal' AND tipoImg='logo' LIMIT 1");
            if($check && mysqli_num_rows($check) > 0){
                mysqli_query($conexion, "UPDATE imagenes SET rutaArchivo='".mysqli_real_escape_string($conexion,$rutaRel)."' WHERE idIdentidad='$codLocal' AND tipoImg='logo'");
            } else {
                mysqli_query($conexion, "INSERT INTO imagenes (idIdentidad, tipoImg, rutaArchivo) VALUES ('$codLocal','logo','".mysqli_real_escape_string($conexion,$rutaRel)."')");
            }
        } else {
            $_SESSION['mensaje'] = "Error moviendo el archivo subido.";
            header("Location: /views/admin/locales/localUpdate.php?codLocal=$codLocal");
            exit();
        }
    }

    $_SESSION['mensaje'] = "Cambios guardados correctamente.";
    header("Location: /views/admin/locales/localUpdate.php?codLocal=$codLocal");
    exit();
}
?>
*/

?>

