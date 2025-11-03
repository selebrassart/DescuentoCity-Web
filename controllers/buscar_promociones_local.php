<?php
session_start();

// Verificar si el usuario está logueado y es cliente
if (!isset($_SESSION['tipoUsuario']) || $_SESSION['tipoUsuario'] !== 'cliente') {
    echo '<div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> No autorizado</div>';
    exit();
}

include("../conexionBD.php");
require("../funciones/funcionesCliente.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codLocal'])) {
    $codLocal = $_POST['codLocal'];
    $codCliente = $_SESSION["codUsuario"];
    
    // Obtener la categoría del cliente
    $consulta_cliente = "SELECT categoriaCliente FROM usuarios WHERE codUsuario = '$codCliente'";
    $resultado_cliente = mysqli_query($conexion, $consulta_cliente);
    $cliente = mysqli_fetch_assoc($resultado_cliente);
    $categoria_cliente = $cliente['categoriaCliente'];
    
    // Obtener categorías permitidas
    $categorias_permitidas = verificarCategoria($categoria_cliente);
    $condicion_categorias = "'" . implode("','", $categorias_permitidas) . "'";
    
    // Obtener promociones del local
    $hoy = date('Y-m-d');
    $dias_en_español = [
        'Monday' => 'lunes',
        'Tuesday' => 'martes',
        'Wednesday' => 'miércoles',
        'Thursday' => 'jueves',
        'Friday' => 'viernes',
        'Saturday' => 'sábado',
        'Sunday' => 'domingo',
    ];
    $dia_semana_server = date('l');
    $dia_semana = $dias_en_español[$dia_semana_server];
    
    $consulta_promociones = "
        SELECT p.*, l.nombreLocal, i.rutaArchivo 
        FROM promociones p 
        JOIN locales l ON p.codLocal = l.codLocal 
        LEFT JOIN imagenes i ON i.idIdentidad = p.codPromo AND i.tipoImg = 'portada' AND i.tipoIdentidad = 'promocion'
        WHERE p.codLocal = '$codLocal' 
        AND p.estadoPromo = 'aprobada' 
        AND l.estadoLocal = 'activo'
        AND '$hoy' BETWEEN p.fechaDesdePromo AND p.fechaHastaPromo
        AND (p.diasSemana LIKE '%$dia_semana%' OR p.diasSemana = '' OR p.diasSemana IS NULL)
        AND (p.categoriaCliente IN ($condicion_categorias) OR p.categoriaCliente IS NULL OR p.categoriaCliente = '')
        ORDER BY p.fechaDesdePromo DESC
    ";
    
    $resultado_promociones = mysqli_query($conexion, $consulta_promociones);
    
    if ($resultado_promociones && mysqli_num_rows($resultado_promociones) > 0) {
        while ($promo = mysqli_fetch_assoc($resultado_promociones)) {
            ?>
            <div class="card mb-3">
                <div class="row g-0">
                    <div class="col-md-4">
                        <?php if (!empty($promo["rutaArchivo"])): ?>
                            <img src="/Descuento-City/<?= htmlspecialchars($promo["rutaArchivo"]) ?>" 
                                 class="img-fluid rounded-start h-100" alt="Imagen promocional del descuento ofrecido por el local" 
                                 style="object-fit: cover; min-height: 200px;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light rounded-start h-100" style="min-height: 200px;">
                                <span class="text-muted"><i class="bi bi-image display-4"></i></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-percent"></i> Promoción Especial
                            </h5>
                            <p class="card-text"><?= htmlspecialchars($promo['textoPromo']) ?></p>
                            
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-plus text-success"></i> 
                                        Desde: <?= date('d/m/Y', strtotime($promo['fechaDesdePromo'])) ?>
                                    </small>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-x text-danger"></i> 
                                        Hasta: <?= date('d/m/Y', strtotime($promo['fechaHastaPromo'])) ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <?php 
                                $categoria_promo = !empty($promo['categoriaCliente']) ? $promo['categoriaCliente'] : 'inicial';
                                $estilo_promo = devolverCategoriaEstilo($categoria_promo);
                                ?>
                                <span class="badge <?= $estilo_promo['badge_class'] ?>">
                                    <i class="<?= $estilo_promo['icon'] ?>"></i> <?= ucfirst($categoria_promo) ?>
                                </span>
                                
                                <?php if (!empty($promo['diasSemana'])): ?>
                                    <small class="text-muted ms-2">
                                        <i class="bi bi-calendar-week"></i> <?= htmlspecialchars($promo['diasSemana']) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            
                            <form action="/Descuento-City/controllers/promocionesCtrl/usoPromocionController.php" method="POST" class="mt-3">
                                <input type="hidden" name="codPromo" value="<?= $promo['codPromo'] ?>">
                                <button type="submit" name="usar" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Usar Promoción
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> 
            <strong>No hay promociones disponibles</strong><br>
            <small>Este local no tiene promociones activas para tu categoría (<?= ucfirst($categoria_cliente) ?>) en este momento.</small>
        </div>
        <?php
    }
} else {
    ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle"></i> Solicitud inválida
    </div>
    <?php
}

mysqli_close($conexion);
?>