


<?php

function guardar_datos_user($user){

    $_SESSION['usuario_logueado'] = true;
    $_SESSION['codUsuario'] = $user['codUsuario'];
    $_SESSION['nombreUsuario'] = $user['nombreUsuario'];
    $_SESSION['tipoUsuario']   = $user['tipoUsuario'];
    $_SESSION['categoriaCliente'] = $user['categoriaCliente'];
    $_SESSION['estadoUsuario'] = $user['estadoUsuario'];
    $_SESSION['fechaRegistro'] = $user['fechaRegistro'];
}

?>