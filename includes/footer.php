<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Estilos inline para forzar eliminación de espacios -->
<style>
    body { margin: 0 !important; padding: 0 !important; }
    html { margin: 0 !important; padding: 0 !important; }
    body::after, html::after { display: none !important; }
    .footer { margin-bottom: 0 !important; }
</style>

<footer class="footer">
    <div class="container">
        <div class="footer-row">
            <div class="footer-links">
                <h4>Página</h4>
                <ul>
                    <?php if (!isset($_SESSION['tipoUsuario'])): ?>
                        <!-- Enlaces para usuarios no logueados -->
                        <li><a href="/index.php"><i class="bi bi-house"></i> Inicio</a></li>
                        <li><a href="/localesUsuarios.php"><i class="bi bi-geo-alt"></i> Locales</a></li>
                        <li><a href="/promocionesUsuario.php"><i class="bi bi-ticket-perforated"></i> Promociones</a></li>
                        <li><a href="/novedadesUsuarios.php"><i class="bi bi-newspaper"></i> Novedades</a></li>
                        <li><a href="/contacto.php"><i class="bi bi-envelope"></i> Contacto</a></li>
                        <li><a href="/views/auth/login.php"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</a></li>
                    
                    <?php elseif ($_SESSION['tipoUsuario'] === 'cliente'): ?>
                        <!-- Enlaces para clientes -->
                        <li><a href="/views/cliente/locales.php"><i class="bi bi-geo-alt"></i> Locales</a></li>
                        <li><a href="/views/cliente/promociones.php"><i class="bi bi-ticket-perforated"></i> Promociones</a></li>
                        <li><a href="/views/cliente/novedades.php"><i class="bi bi-newspaper"></i> Novedades</a></li>
                        <li><a href="/views/cliente/misUsoPromociones.php"><i class="bi bi-collection"></i> Mis Uso Promos</a></li>
                        <li><a href="/contacto.php"><i class="bi bi-envelope"></i> Contacto</a></li>
                    
                    <?php elseif ($_SESSION['tipoUsuario'] === 'dueño'): ?>
                        <!-- Enlaces para dueños -->
                        <li><a href="/views/dueño/mis_promos.php"><i class="bi bi-shop"></i> Mis Promos</a></li>
                        <li><a href="/views/dueño/solicitudes.php"><i class="bi bi-clipboard-check"></i> Solicitudes</a></li>
                        <li><a href="/views/dueño/reporte/dueñoReporte.php"><i class="bi bi-graph-up"></i> Reportes</a></li>
                        <li><a href="/contacto.php"><i class="bi bi-envelope"></i> Contacto</a></li>
                    
                    <?php elseif ($_SESSION['tipoUsuario'] === 'admin'): ?>
                        <!-- Enlaces para administradores -->
                        <li><a href="/views/admin/dueños/dueños.php"><i class="bi bi-people"></i> Dueños</a></li>
                        <li><a href="/views/admin/locales/locales.php"><i class="bi bi-building"></i> Locales</a></li>
                        <li><a href="/views/admin/promociones/promociones.php"><i class="bi bi-tags"></i> Promociones</a></li>
                        <li><a href="/views/admin/novedades/novedades.php"><i class="bi bi-newspaper"></i> Novedades</a></li>
                        <li><a href="/views/admin/reportes/reportes.php"><i class="bi bi-graph-up"></i> Reportes</a></li>
                        <li><a href="/contacto.php"><i class="bi bi-envelope"></i> Contacto</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Sobre nosotros</h4>
                <ul>
                    <li><a href="tel:+543413325148"><i class="fa-solid fa-phone"></i> (+54) 341-332-5148</a></li>
                    <li><a href="https://maps.google.com/?q=Jun%C3%ADn+501" target="_blank" rel="noopener"><i class="fa-solid fa-location-dot"></i> Junín 501</a></li>
                    <li><a href="mailto:info.shop@gmail.com"><i class="fa-solid fa-envelope"></i> info.shop@gmail.com</a></li>
                </ul>
            </div>

            <div class="social-link">
                <h4>Seguinos en</h4>
                <div class="social-icons">
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            
            <div class="footer-links footer-map">
                <h4>Ubicación</h4>
                <div class="map-responsive">
                    <iframe
                        src="https://maps.google.com/maps?q=Jun%C3%ADn%20501&z=15&output=embed"
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="footer-copyright text-center mt-4 pt-3">
            <hr class="footer-divider mb-3">
            <p class="copyright-text mb-0">
                © <?= date('Y') ?> <strong>Descuento City</strong>. Todos los derechos reservados.
            </p>
        </div>
    </div>
</footer>

<!-- Estilos adicionales para eliminar espacios finales -->
<style>
    body { 
        margin-bottom: 0 !important; 
        padding-bottom: 0 !important; 
    }
    html {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
    footer.footer {
        margin-bottom: 0 !important;
        padding-bottom: 20px !important;
    }
    /* Ocultar cualquier espacio después del último elemento */
    body > *:last-child {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>

<!-- Enlaces de Bootstrap Icons necesarios para el footer -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="/assets/css/footer.css">
<!-- Font Awesome para íconos de contacto -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />