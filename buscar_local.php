<?php
include("conexionBD.php");

if(isset($_POST['buscar'])) {
    
    $busqueda = $_POST['buscar'];
    
    // Sanitizar la entrada
    $busqueda = mysqli_real_escape_string($conexion, $busqueda);
    
    // Consulta para buscar por código de local o nombre
    $sql = "SELECT 
                l.codLocal, 
                l.nombreLocal, 
                l.ubicacionLocal, 
                l.rubroLocal,
                i.rutaArchivo AS logo
            FROM locales l
            LEFT JOIN imagenes i ON i.idIdentidad = l.codLocal AND i.tipoImg = 'logo' AND i.tipoIdentidad = 'local'
            WHERE l.estadoLocal = 'activo' 
            AND (l.codLocal LIKE '%$busqueda%' 
                 OR l.nombreLocal LIKE '%$busqueda%' 
                 OR l.rubroLocal LIKE '%$busqueda%')
            ORDER BY l.nombreLocal ASC
            LIMIT 5";
    
    $resultado = mysqli_query($conexion, $sql);
    
    if($resultado && mysqli_num_rows($resultado) > 0) {
        while($local = mysqli_fetch_assoc($resultado)) {
            $logo = $local['logo'] ? $local['logo'] : '/Descuento-City/assets/img/default-local.jpg';
            ?>
            <div class="border-bottom p-2 hover-bg-light" style="cursor: pointer;">
                <div class="row align-items-center">
                    <div class="col-2">
                        <img src="<?= htmlspecialchars($logo) ?>" 
                             alt="<?= htmlspecialchars($local['nombreLocal']) ?>" 
                             class="img-fluid rounded" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                    </div>
                    <div class="col-10">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong><?= htmlspecialchars($local['nombreLocal']) ?></strong>
                                <small class="text-muted d-block">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($local['ubicacionLocal']) ?>
                                </small>
                            </div>
                            <small class="text-primary">#<?= $local['codLocal'] ?></small>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-tag"></i> <?= htmlspecialchars($local['rubroLocal']) ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="p-3 text-center text-muted">';
        echo '<i class="bi bi-search"></i><br>';
        echo 'No se encontraron locales con: "' . htmlspecialchars($busqueda) . '"';
        echo '</div>';
    }
} else {
    echo '<div class="p-3 text-center text-muted">Error en la búsqueda</div>';
}

mysqli_close($conexion);
?>