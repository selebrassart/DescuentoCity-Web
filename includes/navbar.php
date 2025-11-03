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
    
    if (navbarToggler && navbarCollapse) {
        // Función para alternar el menú
        navbarToggler.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle manual del menú
            if (navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
                navbarToggler.setAttribute('aria-expanded', 'false');
            } else {
                navbarCollapse.classList.add('show');
                navbarToggler.setAttribute('aria-expanded', 'true');
            }
        });
        
        // Cerrar menú al hacer clic en un enlace (solo en móvil)
        const navLinks = navbarCollapse.querySelectorAll('.nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) { // Solo en dispositivos móviles
                    navbarCollapse.classList.remove('show');
                    navbarToggler.setAttribute('aria-expanded', 'false');
                }
            });
        });
        
        // Cerrar menú al hacer clic fuera de él
        document.addEventListener('click', function(e) {
            if (!navbarToggler.contains(e.target) && !navbarCollapse.contains(e.target)) {
                if (navbarCollapse.classList.contains('show')) {
                    navbarCollapse.classList.remove('show');
                    navbarToggler.setAttribute('aria-expanded', 'false');
                }
            }
        });
        
        // Cerrar menú al cambiar el tamaño de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992 && navbarCollapse.classList.contains('show')) {
                navbarCollapse.classList.remove('show');
                navbarToggler.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>

<style>
/* Altura aumentada del navbar */
.navbar-height {
    min-height: 80px;
    padding-top: 1rem;
    padding-bottom: 1rem;
}

/* Altura reducida en móvil */
@media (max-width: 767px) {
    .navbar-height {
        min-height: 60px;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
}

/* Espaciador responsivo para navbar fixed-top */
.navbar-spacer {
    height: 100px;
}

@media (max-width: 767px) {
    .navbar-spacer {
        height: 70px;
    }
}

/* Logo fijo a la izquierda en pantallas grandes */
@media (min-width: 1200px) {
    .navbar .container-fluid {
        padding-left: 2rem;
        padding-right: 2rem;
    }
    
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