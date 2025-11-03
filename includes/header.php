
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Incluir navbar unificado -->
<?php include($_SERVER['DOCUMENT_ROOT'] . '/Descuento-City/includes/navbar.php'); ?>



    <header class="header">
        <div class="header_log-container">
            <a href="/Descuento-City/index.php"><img src="/Descuento-City/assets/img/logo/LOGO1.png" alt="logo"></a>
        </div>
        <nav class="header__nav">
            <ul class="header__nav-list">
                <li class="header__nav-item"><a href="/Descuento-City/localesUsuarios.php">Locales</a></li>
                <li class="header__nav-item"><a href="/Descuento-City/promocionesUsuario.php">Promociones</a></li>
                <li class="header__nav-item"><a href="/Descuento-City/novedadesUsuarios.php">Novedades</a></li>
                <li class="header__nav-item"><a href="/Descuento-City/contacto.php">Contacto</a></li>
                <li class="header__nav-item login"><a href="/Descuento-City/views/auth/login.php">Iniciar Sesión</a></li>
            </ul>  
        </nav>
    </header>