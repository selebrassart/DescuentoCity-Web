<?php
// controllers/novedadesCtrl/editarNovedadController.php
// Controller simple y procedural: actualiza datos de novedad y reemplaza imagen si se sube una nueva.

session_start();
require("../../conexionBD.php");

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje_warning'] = "Método no permitido";
    header("Location: ../../views/admin/novedades/novedades.php");
    exit;
}

// Recupero campos (uso mysqli_real_escape_string para evitar inyección básica)
$codNovedad = isset($_POST['codNovedad']) ? intval($_POST['codNovedad']) : 0;
$tituloNovedad = isset($_POST['tituloNovedad']) ? mysqli_real_escape_string($conexion, trim($_POST['tituloNovedad'])) : '';
$textoNovedad = isset($_POST['textoNovedad']) ? mysqli_real_escape_string($conexion, trim($_POST['textoNovedad'])) : '';
$fechaDesdeNovedad = isset($_POST['fechaDesdeNovedad']) ? mysqli_real_escape_string($conexion, trim($_POST['fechaDesdeNovedad'])) : '';
$fechaHastaNovedad = isset($_POST['fechaHastaNovedad']) ? mysqli_real_escape_string($conexion, trim($_POST['fechaHastaNovedad'])) : '';
$categoriaCliente = isset($_POST['categoriaCliente']) ? mysqli_real_escape_string($conexion, trim($_POST['categoriaCliente'])) : '';
$existeImg = isset($_POST['existeImg']) ? trim($_POST['existeImg']) : '';
$nuevaImg = isset($_FILES['nuevaImg']) ? $_FILES['nuevaImg'] : null;

if ($codNovedad <= 0) {
    $_SESSION['mensaje_error'] = "ID de novedad inválido";
    header("Location: ../../views/admin/novedades/novedades.php");
    exit;
}
if ($textoNovedad === '' || $fechaDesdeNovedad === '' || $fechaHastaNovedad === '' || $categoriaCliente === '') {
    $_SESSION['mensaje_warning'] = "Complete todos los campos requeridos";
    header("Location: ../../views/admin/novedades/editarNovedad.php?codNovedad=" . urlencode($codNovedad));
    exit;
}

// Update simple con mysqli_query
$consultaUpdate = "UPDATE novedades SET 
    tituloNovedad = '$tituloNovedad',
    textoNovedad = '$textoNovedad', 
    fechaDesdeNovedad = '$fechaDesdeNovedad', 
    fechaHastaNovedad = '$fechaHastaNovedad',
    categoriaCliente = '$categoriaCliente'
    WHERE codNovedad = $codNovedad";

$resUpdate = mysqli_query($conexion, $consultaUpdate);
if (!$resUpdate) {
    $_SESSION['mensaje_error'] = "Error al actualizar novedad: " . mysqli_error($conexion);
    header("Location: ../../views/admin/novedades/editarNovedad.php?codNovedad=" . urlencode($codNovedad));
    exit;
}

// Si se subió nueva imagen, la guardo y actualizo/insert en tabla imagenes
if ($nuevaImg && isset($nuevaImg['error']) && $nuevaImg['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($nuevaImg['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['mensaje_error'] = "Error al subir archivo (código: " . intval($nuevaImg['error']) . ")";
        header("Location: ../../views/admin/novedades/editarNovedad.php?codNovedad=" . urlencode($codNovedad));
        exit;
    }

    // Validación mínima por extensión
    $ext = strtolower(pathinfo($nuevaImg['name'], PATHINFO_EXTENSION));
    $permitidos = array('jpg','jpeg','png','gif','webp');
    if (!in_array($ext, $permitidos)) {
        $_SESSION['mensaje_warning'] = "Tipo de archivo no permitido";
        header("Location: ../../views/admin/novedades/editarNovedad.php?codNovedad=" . urlencode($codNovedad));
        exit;
    }

    // Directorio de uploads (ruta relativa para BD: uploads/fondoNovedad/)
    $uploadRel = "uploads/fondoNovedad/";
    $uploadDir = __DIR__ . "/../../" . $uploadRel;
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            $_SESSION['mensaje_error'] = "No se pudo crear carpeta de uploads";
            header("Location: ../../views/admin/novedades/editarNovedad.php?codNovedad=" . urlencode($codNovedad));
            exit;
        }
    }

    $nuevoNombre = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destino = $uploadDir . $nuevoNombre;
    if (!move_uploaded_file($nuevaImg['tmp_name'], $destino)) {
        $_SESSION['mensaje_error'] = "No se pudo mover el archivo";
        header("Location: ../../views/admin/novedades/editarNovedad.php?codNovedad=" . urlencode($codNovedad));
        exit;
    }

    $rutaBD = $uploadRel . $nuevoNombre; // lo guardamos así en la BD

    // Verifico si ya hay registro en imagenes
    $consultaCheck = "SELECT idImg, rutaArchivo FROM imagenes WHERE idIdentidad = $codNovedad AND tipoImg = 'portada' AND tipoIdentidad = 'novedad' LIMIT 1";
    $resCheck = mysqli_query($conexion, $consultaCheck);
    if (!$resCheck) {
        $_SESSION['mensaje_error'] = "Error al consultar imagen: " . mysqli_error($conexion);
        header("Location: ../../views/admin/novedades/editarNovedad.php?codNovedad=" . urlencode($codNovedad));
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
            header("Location: ../../views/admin/novedades/editarNovedad.php?codNovedad=" . urlencode($codNovedad));
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
        $consultaInsImg = "INSERT INTO imagenes (tipoImg, nombreImg, rutaArchivo, tipoIdentidad, idIdentidad, fechaSubida) VALUES ('portada', '$nombreEsc', '$rutaEsc', 'novedad', $codNovedad, NOW())";
        $resInsImg = mysqli_query($conexion, $consultaInsImg);
        if (!$resInsImg) {
            $_SESSION['mensaje_error'] = "Error al insertar imagen: " . mysqli_error($conexion);
            header("Location: ../../views/admin/novedades/editarNovedad.php?codNovedad=" . urlencode($codNovedad));
            exit;
        }
    }
}

// Todo bien
$_SESSION['mensaje_exito'] = "Novedad actualizada correctamente";
header("location:../../views/admin/novedades/novedades.php");
exit;
?>