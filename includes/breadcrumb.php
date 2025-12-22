<?php
// obtiene la ruta en la q estamos con la var global $_server['request_uri']
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Definir una lista de elementos que deben ser ignorados en el Breadcrumb
$ignore_segments = [
    'index.php',
    'index.html',
    'descuento-city',
    'views',
    'auth',
    'cliente',
    'dueños',
    'dueño',
    'admin',
    'locales',
    'promociones',
    'novedades',
    'reportes',
    'reporte',

];

$segments = array_filter(array_map('rawurldecode', explode('/', $path)));



$segments_filtrados = [];
foreach ($segments as $segment) {
    $seg = rawurldecode($segment);
    if (!in_array(mb_strtolower($seg, 'UTF-8'), $ignore_segments)) {
        $segments_filtrados[] = $seg;
    }
}



$segments = $segments_filtrados; // lista limpia

// Si no hay segmentos filtrados, raíz (Home)
if (empty($segments)) {
    echo '<nav style="--bs-breadcrumb-divider: \'>\';" aria-label="breadcrumb">';
    echo '<ol class="breadcrumb">';
    echo '<li class="breadcrumb-item active" aria-current="page">Inicio</li>';
    echo '</ol>';
    echo '</nav>';
    return;
}

// Función para crear enlaces 
function crear_enlace_breadcrumb($segments, $index) {
    global $path;
    
    // Si estamos en una página de cliente, dueño o admin, crear enlaces apropiados
    if (strpos($path, 'views/cliente') !== false) {
        // Para páginas de cliente, enlazar a secciones de cliente
        switch(strtolower($segments[$index])) {
            case 'locales':
                return '/views/cliente/locales.php';
            case 'promociones':
                return '/views/cliente/promociones.php';
            case 'novedades':
                return '/views/cliente/novedades.php';
            default:
                return '/index.php';
        }
    } elseif (strpos($path, 'views/dueño') !== false || strpos($path, 'views/due') !== false) {
        // Para páginas de dueño
        switch(strtolower($segments[$index])) {
            case 'mis_promos':
                return '/views/dueño/mis_promos.php';
            case 'solicitudes':
                return '/views/dueño/solicitudes.php';
            case 'reporte':
                return '/views/dueño/reporte/dueñoReporte.php';
            default:
                return '/index.php';
        }
    } elseif (strpos($path, 'views/admin') !== false) {
        // Para páginas de admin
        switch(strtolower($segments[$index])) {
            case 'locales':
                return '/views/admin/locales/locales.php';
            case 'promociones':
                return '/views/admin/promociones/promociones.php';
            case 'novedades':
                return '/views/admin/novedades/novedades.php';
            case 'dueños':
                return '/views/admin/dueños/dueños.php';
            default:
                return '/index.php';
        }
    } else {
        // Para páginas públicas
        switch(strtolower($segments[$index])) {
            case 'locales':
                return '/localesUsuarios.php';
            case 'promociones':
                return '/promocionesUsuario.php';
            case 'novedades':
                return '/novedadesUsuarios.php';
            case 'contacto':
                return '/contacto.php';
            default:
                return '/index.php';
        }
    }
}

//componente de Bootstrap
echo '<nav style="--bs-breadcrumb-divider: \'>\';" aria-label="breadcrumb">';
echo '<ol class="breadcrumb">';

// Enlace de Home específico según el tipo de usuario
$home_link = '/index.php'; // Por defecto
if (isset($_SESSION['tipoUsuario'])) {
    switch($_SESSION['tipoUsuario']) {
        case 'cliente':
            $home_link = '/views/cliente/locales.php';
            break;
        case 'dueño':
            $home_link = '/views/dueño/mis_promos.php';
            break;
        case 'admin':
            $home_link = '/views/admin/dueños/dueños.php';
            break;
    }
}

echo '<li class="breadcrumb-item"><a href="' . $home_link . '">Inicio</a></li>';

$total_segments = count($segments);

//recorre los segmentos restantes (que serán las migas reales)
foreach ($segments as $index => $segment) {
    
    $segment_limpio = str_replace(array('.php', '.html', '-'), ' ', $segment);
    $segment_display = ucwords($segment_limpio); 
    
    // Determina si es el último elemento
    if ($index + 1 == $total_segments) {
        
        $texto_final = isset($breadcrumb_titulo_activo) ? $breadcrumb_titulo_activo : $segment_display;
        
        // ES EL ÚLTIMO: Estilo ACTIVE
        echo '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($texto_final) . '</li>';
        
    } else {
        // NO ES EL ÚLTIMO: Estilo ENLACE
        $enlace = crear_enlace_breadcrumb($segments, $index);
        echo '<li class="breadcrumb-item"><a href="' . htmlspecialchars($enlace) . '">' . htmlspecialchars($segment_display) . '</a></li>';
    }
}

echo '</ol>';
echo '</nav>';
?>