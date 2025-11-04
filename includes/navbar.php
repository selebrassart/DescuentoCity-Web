<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top navbar-height" style="z-index:1050;">
  <div class="container-fluid">
    <!-- LOGO -->
    <a class="navbar-brand fw-bold d-flex align-items-center position-fixed-logo" href="<?php 
      if (isset($_SESSION['tipoUsuario'])) {
        switch($_SESSION['tipoUsuario']) {
          case 'cliente':
            echo '/Descuento-City/views/cliente/promociones.php';
            break;
          case 'dueño':
            echo '/Descuento-City/views/dueño/mis_promos.php';
            break;
          case 'admin':
            echo '/Descuento-City/views/admin/dueños/dueños.php';
            break;
          default:
            echo '/Descuento-City/index.php';
        }
      } else {
        echo '/Descuento-City/index.php';
      }
    ?>">
      <img src="/Descuento-City/assets/img/logo/LOGO1.png" alt="Logo principal de Descuento City - Tu plataforma de descuentos y promociones" width="120" class="me-2">
    </a>

    <!-- BOTÓN HAMBURGUESA -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDC" aria-controls="navbarDC" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- LINKS -->
    <div class="collapse navbar-collapse bg-light p-3 p-lg-0" id="navbarDC">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">

        <?php if (!isset($_SESSION['tipoUsuario'])): ?>
          <!-- Enlaces para usuarios no logueados -->
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/localesUsuarios.php">Locales</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/promocionesUsuario.php">Promociones</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/novedadesUsuarios.php">Novedades</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/contacto.php">Contacto</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/auth/login.php"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</a></li>

        <?php elseif ($_SESSION['tipoUsuario'] === 'cliente'): ?>
          <!-- Enlaces para clientes -->
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/cliente/locales.php"><i class="bi bi-geo-alt"></i> Locales</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/cliente/promociones.php"><i class="bi bi-ticket-perforated"></i> Promociones</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/cliente/novedades.php"><i class="bi bi-newspaper"></i> Novedades</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/cliente/misUsoPromociones.php"><i class="bi bi-collection"></i> Mis Uso Promos</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/contacto.php"><i class="bi bi-envelope"></i> Contacto</a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="/Descuento-City/views/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>

        <?php elseif ($_SESSION['tipoUsuario'] === 'dueño'): ?>
          <!-- Enlaces para dueños -->
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/dueño/mis_promos.php"><i class="bi bi-shop"></i> Mis Promos</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/dueño/solicitudes.php"><i class="bi bi-clipboard-check"></i> Solicitudes</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/dueño/reporte/dueñoReporte.php"><i class="bi bi-graph-up"></i> Reportes</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/contacto.php"><i class="bi bi-envelope"></i> Contacto</a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="/Descuento-City/views/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>

        <?php elseif ($_SESSION['tipoUsuario'] === 'admin'): ?>
          <!-- Enlaces para administradores -->
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/dueños/dueños.php"><i class="bi bi-people"></i> Dueños</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/locales/locales.php"><i class="bi bi-building"></i> Locales</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/promociones/promociones.php"><i class="bi bi-tags"></i> Promociones</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/novedades/novedades.php"><i class="bi bi-newspaper"></i> Novedades</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/views/admin/reportes/reportes.php"><i class="bi bi-graph-up"></i> Reportes</a></li>
          <li class="nav-item"><a class="nav-link" href="/Descuento-City/contacto.php"><i class="bi bi-envelope"></i> Contacto</a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="/Descuento-City/views/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<!-- espacio porque es fixed-top -->
<div class="navbar-spacer"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('#navbarDC');
    
    if (!navbarToggler || !navbarCollapse) return;
    
    // Función para alternar el menú
    function toggleNavbar() {
        const isOpen = navbarCollapse.classList.contains('show');
        
        if (isOpen) {
            navbarCollapse.classList.remove('show');
            navbarToggler.setAttribute('aria-expanded', 'false');
            navbarToggler.classList.add('collapsed');
        } else {
            navbarCollapse.classList.add('show');
            navbarToggler.setAttribute('aria-expanded', 'true');
            navbarToggler.classList.remove('collapsed');
        }
    }
    
    // Función para cerrar el menú
    function closeNavbar() {
        navbarCollapse.classList.remove('show');
        navbarToggler.setAttribute('aria-expanded', 'false');
        navbarToggler.classList.add('collapsed');
    }
    
    // Evento click en el botón hamburguesa
    navbarToggler.addEventListener('click', function(e) {
        e.preventDefault();
        toggleNavbar();
    });
    
    // Cerrar menú al hacer clic en un enlace (solo móvil)
    const navLinks = navbarCollapse.querySelectorAll('.nav-link');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                closeNavbar();
            }
        });
    });
    
    // Cerrar menú al hacer clic fuera (solo móvil)
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 992) {
            const isClickInsideNav = navbarCollapse.contains(e.target) || navbarToggler.contains(e.target);
            if (!isClickInsideNav && navbarCollapse.classList.contains('show')) {
                closeNavbar();
            }
        }
    });
});
</script>

<style>


.navbar-height {
    min-height: 100px;
    padding-top: 1rem;
    padding-bottom: 1rem;
}

@media (max-width: 767px) {
    .navbar-height {
        min-height: 70px;
        padding-top: 0.7rem;
        padding-bottom: 0.5rem;
    }
}

.navbar-spacer {
    height: 100px;
}

@media (max-width: 767px) {
    .navbar-spacer {
        height: 80px;
    }
}


@media (min-width: 1200px) {
    .navbar .container-fluid {
        padding-left: 2rem;
        padding-right: 2rem;
    }

    /* LOGO FIJO */
    
    .position-fixed-logo {
        position: absolute;
        left: 2rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1060;
    }
    
    .navbar-collapse {
        margin-left: 160px; /* Espacio para el logo */
    }
}

@media (min-width: 1400px) {
    .navbar .container-fluid {
        padding-left: 3rem;
        padding-right: 3rem;
    }
    
    .position-fixed-logo {
        left: 3rem;
    }
    
    .navbar-collapse {
        margin-left: 180px; 
    }
}

/* En pantallas medianas y pequeñas mantener comportamiento normal */
@media (max-width: 1199px) {
    .position-fixed-logo {
        position: relative;
        left: auto;
        top: auto;
        transform: none;
    }
    
    .navbar-collapse {
        margin-left: 0;
    }
}
</style>