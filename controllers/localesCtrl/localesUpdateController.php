<?php
// controllers/localesCtrl/localesUpdateController.php
// Controller simple y procedural: actualiza datos y reemplaza logo si se sube uno nuevo.

session_start();
require("../../conexionBD.php");

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje_warning'] = "Método no permitido";
    header("Location: ../../views/admin/locales/locales.php");
    exit;
}

// Recupero campos (uso mysqli_real_escape_string para evitar inyección básica)
$codLocal = isset($_POST['codLocal']) ? intval($_POST['codLocal']) : 0;
$nombreLocal = isset($_POST['nombreLocal']) ? mysqli_real_escape_string($conexion, trim($_POST['nombreLocal'])) : '';
$rubroLocal = isset($_POST['rubroLocal']) ? mysqli_real_escape_string($conexion, trim($_POST['rubroLocal'])) : '';
$ubicacionLocal = isset($_POST['ubicacionLocal']) ? mysqli_real_escape_string($conexion, trim($_POST['ubicacionLocal'])) : '';
$existeLogo = isset($_POST['existeLogo']) ? trim($_POST['existeLogo']) : '';
$nuevoLogo = isset($_FILES['nuevoLogo']) ? $_FILES['nuevoLogo'] : null;

if ($codLocal <= 0) {
    $_SESSION['mensaje_error'] = "ID de local inválido";
    header("Location: ../../views/admin/locales/locales.php");
    exit;
}
if ($nombreLocal === '' || $rubroLocal === '' || $ubicacionLocal === '') {
    $_SESSION['mensaje_warning'] = "Complete todos los campos requeridos";
    header("Location: ../../views/admin/locales/localUpdate.php?codLocal=" . urlencode($codLocal));
    exit;
}

// Update simple con mysqli_query
$consultaUpdate = " UPDATE locales SET nombreLocal = '$nombreLocal', rubroLocal = '$rubroLocal', ubicacionLocal = '$ubicacionLocal' WHERE codLocal = $codLocal
";
$resUpdate = mysqli_query($conexion, $consultaUpdate);
if (!$resUpdate) {
    $_SESSION['mensaje_error'] = "Error al actualizar local: " . mysqli_error($conexion);
    header("Location: ../../views/admin/locales/localUpdate.php?codLocal=" . urlencode($codLocal));
    exit;
}

// Si se subió nuevo logo, lo guardo y actualizo/insert en tabla imagenes
if ($nuevoLogo && isset($nuevoLogo['error']) && $nuevoLogo['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($nuevoLogo['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['mensaje_error'] = "Error al subir archivo (código: " . intval($nuevoLogo['error']) . ")";
        header("Location: ../../views/admin/locales/localUpdate.php?codLocal=" . urlencode($codLocal));
        exit;
    }

    // Validación mínima por extensión
    $ext = strtolower(pathinfo($nuevoLogo['name'], PATHINFO_EXTENSION));
    $permitidos = array('jpg','jpeg','png','gif','webp');
    if (!in_array($ext, $permitidos)) {
        $_SESSION['mensaje_warning'] = "Tipo de archivo no permitido";
        header("Location: ../../views/admin/locales/localUpdate.php?codLocal=" . urlencode($codLocal));
        exit;
    }

    // Directorio de uploads (ruta relativa para BD: uploads/logos/)
    $uploadRel = "uploads/logos/";
    $uploadDir = __DIR__ . "/../../" . $uploadRel;
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            $_SESSION['mensaje_error'] = "No se pudo crear carpeta de uploads";
            header("Location: ../../views/admin/locales/localUpdate.php?codLocal=" . urlencode($codLocal));
            exit;
        }
    }

    $nuevoNombre = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destino = $uploadDir . $nuevoNombre;
    if (!move_uploaded_file($nuevoLogo['tmp_name'], $destino)) {
        $_SESSION['mensaje_error'] = "No se pudo mover el archivo";
        header("Location: ../../views/admin/locales/localUpdate.php?codLocal=" . urlencode($codLocal));
        exit;
    }

    $rutaBD = $uploadRel . $nuevoNombre; // lo guardamos así en la BD

    // Verifico si ya hay registro en imagenes
    $consultaCheck = "SELECT idImg, rutaArchivo FROM imagenes WHERE idIdentidad = $codLocal AND tipoImg = 'logo' LIMIT 1";
    $resCheck = mysqli_query($conexion, $consultaCheck);
    if (!$resCheck) {
        $_SESSION['mensaje_error'] = "Error al consultar imagen: " . mysqli_error($conexion);
        header("Location: ../../views/admin/locales/localUpdate.php?codLocal=" . urlencode($codLocal));
        exit;
    }

    if (mysqli_num_rows($resCheck) === 1) {
        $filaImg = mysqli_fetch_assoc($resCheck);
        $idImg = intval($filaImg['idImg']);
        $rutaOld = $filaImg['rutaArchivo'];

        $consultaUpdImg = "UPDATE imagenes SET nombreImg = '" . mysqli_real_escape_string($conexion, $nuevoNombre) . "', rutaArchivo = '" . mysqli_real_escape_string($conexion, $rutaBD) . "', fechaSubida = NOW() WHERE idImg = $idImg";
        $resUpdImg = mysqli_query($conexion, $consultaUpdImg);
        if (!$resUpdImg) {
            $_SESSION['mensaje_error'] = "Error al actualizar imagen: " . mysqli_error($conexion);
            header("Location: ../../views/admin/locales/localUpdate.php?codLocal=" . urlencode($codLocal));
            exit;
        }

        // intento borrar archivo antiguo si existe
        if (!empty($rutaOld)) {
            $oldPublic = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/' . ltrim($rutaOld, '/\\');
            if (file_exists($oldPublic)) @unlink($oldPublic);
        }
    } else {
        // inserto nueva fila
        $nombreEsc = mysqli_real_escape_string($conexion, $nuevoNombre);
        $rutaEsc = mysqli_real_escape_string($conexion, $rutaBD);
        $consultaInsImg = "INSERT INTO imagenes (tipoImg, nombreImg, rutaArchivo, tipoIdentidad, idIdentidad, fechaSubida) VALUES ('logo', '$nombreEsc', '$rutaEsc', 'local', $codLocal, NOW())";
        $resInsImg = mysqli_query($conexion, $consultaInsImg);
        if (!$resInsImg) {
            $_SESSION['mensaje_error'] = "Error al insertar imagen: " . mysqli_error($conexion);
            header("Location: ../../views/admin/locales/localUpdate.php?codLocal=" . urlencode($codLocal));
            exit;
        }
    }
}

// Todo bien
$_SESSION['mensaje_exito'] = "Local actualizado correctamente";
header("location:../../views/admin/locales/locales.php");
exit;
?>