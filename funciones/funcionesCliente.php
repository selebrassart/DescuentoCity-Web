<?php

function verificarCategoria($categoriaCliente){
    
    $categorias_permitidas = [];

    if($categoriaCliente == "premium"){
        $categorias_permitidas = ['inicial','medium','premium'];
    }
    elseif($categoriaCliente == "medium"){
        $categorias_permitidas = ['inicial','medium'];
    }
    elseif($categoriaCliente == "inicial"){
        $categorias_permitidas = ['inicial'];
    }

    return $categorias_permitidas;
}

function devolverCategoriaEstilo($categoria){
    $estilo = [
        'badge_class' => '',
        'icon' => '',
        'color_text' => ''
    ];

    switch(strtolower($categoria)) {
        case 'premium':
            $estilo['badge_class'] = 'bg-warning text-dark';
            $estilo['icon'] = 'bi bi-gem';
            $estilo['color_text'] = 'text-warning';
            break;
        case 'medium':
            $estilo['badge_class'] = 'bg-info';
            $estilo['icon'] = 'bi bi-star-fill';
            $estilo['color_text'] = 'text-info';
            break;
        case 'inicial':
        default:
            $estilo['badge_class'] = 'bg-secondary';
            $estilo['icon'] = 'bi bi-circle-fill';
            $estilo['color_text'] = 'text-secondary';
            break;
    }

    return $estilo;
}


?>