<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top" style="z-index:1050;">
  <div class="container">
    <!-- LOGO -->
    <a class="navbar-brand fw-bold d-flex align-items-center" href="/Descuento-City/index.php">
      <img src="/Descuento-City/assets/img/logo/LOGO1.png" alt="Descuento City" width="120" class="me-2">
    </a>

    <!-- BOTÓN HAMBURGUESA -->
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarDC"
            aria-controls="navbarDC"
            aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- LINKS -->
    <div class="collapse navbar-collapse bg-light p-3 p-lg-0" id="navbarDC">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">

        <!-- Comunes -->
        <li class="nav-item"><a class="nav-link" href="/Descuento-City/localesUsuarios.php">Locales</a></li>
        <li class="nav-item"><a class="nav-link" href="/Descuento-City/promocionesUsuario.php">Promociones</a></li>
        <li class="nav-item"><a class="nav-link" href="/Descuento-City/novedadesUsuarios.php">Novedades</a></li>
        <li class="nav-item"><a class="nav-link" href="/Descuento-City/contacto.php">Contacto</a></li>

        <?php if (!isset($_SESSION['rol'])): ?>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/auth/login.php"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesion</a></li>

        <?php elseif ($_SESSION['rol'] === 'cliente'): ?>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/cliente/locales.php"><i class="bi bi-ticket-perforated"></i> Locales</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/cliente/promociones.php"><i class="bi bi-ticket-perforated"></i> Promociones</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/cliente/novedades.php"><i class="bi bi-ticket-perforated"></i> Novedades</a></li>
        <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/cliente/misUsoPromociones.php"><i class="bi bi-ticket-perforated"></i> Mis Uso Promos</a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="/Descuento-City/views/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesion</a></li>

        <?php elseif ($_SESSION['rol'] === 'dueño'): ?>

          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/dueño/mis_promos.php"><i class="bi bi-shop"></i> Mis promos</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/dueño/solicitudes.php"><i class="bi bi-shop"></i> Solicitudes</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/dueño/reporte/dueñoReporte.php"><i class="bi bi-shop"></i> Reoortes/a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="/Descuento-City/views/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesion</a></li>

        <?php elseif ($_SESSION['rol'] === 'admin'): ?>

          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/dueños/dueños.php"><i class="bi bi-graph-up"></i> Dueños</a></li>

          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/locales/locales.php"><i class="bi bi-graph-up"></i> Locales</a></li>

          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/promociones/promociones.php"><i class="bi bi-graph-up"></i> Promociones</a></li>

          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/novedades/novedadedes.php"><i class="bi bi-graph-up"></i> Novedades</a></li>

          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/reportes/reportes.php"><i class="bi bi-graph-up"></i> Reportes</a></li>

          <li class="nav-item"><a class="nav-link text-danger" href="/Descuento-City/views/auth/logout.php"><i class="bi bi-box-arrow-right"></i>Cerrar Sesio</a></li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<!-- espacio porque es fixed-top -->
<div style="height:90px;"></div>