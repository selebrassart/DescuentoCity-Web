


<?php

//Consulto : nombreUsuario = email | token = token | estadoUsuario = 'Pendiente '
function consultaSQL($con,$email,$token){
    $consultaEstado = "SELECT * FROM usuarios WHERE nombreUsuario = '$email' AND token= '$token' AND estadoUsuario= 'pendiente' ";
    $resultado = mysqli_query($con,$consultaEstado);
    return $resultado;
}


//Consulto : tipoUsuario ="dueños" | estadoUsuario="pendiente/activo/bloquedo" ordenados . DESC para que aparezca el mas reciente primero 
function consultaDueños($con){
    $consultaDueños = "SELECT * FROM usuarios WHERE  tipoUsuario='dueño' ORDER BY FIELD(estadoUsuario, 'pendiente', 'activo', 'bloqueado') , fechaRegistro DESC";
    $resultado = mysqli_query($con,$consultaDueños);
    return $resultado;

}

/*
//Actualizo estado de local.
function update_estado($con,$accion,$codigo){

    if($accion == 'activar'){
        $consulta = "UPDATE locales SET estadoLocal='activo' WHERE codLocal ='$codigo'";
    }
    else{
        $consulta ="UPDATE locales SET estadoLocal='eliminado' WHERE codLocal ='$codigo'";
    }

    $resultado = mysqli_query($con,$consulta);
    return $resultado;

}
*/

function consultaLocales($con){
    $consultaLocales = "SELECT * FROM locales";
    $resultado = mysqli_query($con,$consultaLocales);
    return $resultado;
}



// Actualizar estado pendiente -> activo.
function update_estado_SQL($con,$email){
    $consulta_update = "UPDATE usuarios SET estadoUsuario='activo', token=NULL  WHERE nombreUsuario='$email'";
    $resultado_update = mysqli_query($con,$consulta_update);
    return $resultado_update;
}


//actualizar estado promocion ('Aprobada','Denegada') y eliminar de BD
function cambiarEstado($conexion,$codPromo,$accion){

    $consultaSQL = '';

    if($accion == 'aprobar'){
        $consultaSQL = "UPDATE promociones SET estadoPromo='aprobada' WHERE codPromo='$codPromo'";   
    }
    elseif($accion == 'denegar'){
        $consultaSQL = "UPDATE promociones SET estadoPromo='denegada' WHERE codPromo='$codPromo'";     
    }
    elseif($accion == 'eliminar'){
        $consultaSQL = "DELETE FROM promociones WHERE codPromo='$codPromo'";    
    }

    if($consultaSQL != ''){
        $resultado = mysqli_query($conexion,$consultaSQL);
        return $resultado;
    }
    
    return false;
}


function subirCategoria($codCliente,$conexion){

    $codCliente = $codCliente;
    
    // Obtener categoría actual del cliente
    $consultaCategoria = "SELECT categoriaCliente FROM usuarios WHERE codUsuario='$codCliente'";
    $resultadoCategoria = mysqli_query($conexion,$consultaCategoria);
    $categoriaActual = mysqli_fetch_assoc($resultadoCategoria)["categoriaCliente"];
    
    // Conslto cantidad usos aceptados de un cliente
    $consulta = "SELECT COUNT(*) AS totalUsos FROM uso_promociones WHERE codCliente='$codCliente' AND estado='aceptada'";
    $resultado = mysqli_query($conexion,$consulta);
    $totalUsos = mysqli_fetch_assoc($resultado)["totalUsos"];

    $nuevaCategoria = $categoriaActual; 
    
    //defino valores 
    if($totalUsos >= 20 && $categoriaActual != 'premium'){
        $nuevaCategoria = 'premium';
    }
    elseif($totalUsos >= 8 && $categoriaActual == 'inicial'){
        $nuevaCategoria = 'medium';
    }
    
    if($nuevaCategoria != $categoriaActual){
        $consultaUpdate = "UPDATE usuarios SET categoriaCliente='$nuevaCategoria' WHERE codUsuario ='$codCliente'";
        $resultado = mysqli_query($conexion,$consultaUpdate);
                // Retornar información del cambio
        
        return [
            'actualizado' => true,
            'categoria_anterior'=>$categoriaActual,
            'categoria_nueva' => $nuevaCategoria,
            'total_usos' => $totalUsos
        ];
    }
    
    // Si no hubo cambios
    return [
        'actualizado' => false,
        'categoria_anterior'=>$categoriaActual,
        'categoria_nueva' => $nuevaCategoria,
        'total_usos' => $totalUsos
    ];
    }
    

?>