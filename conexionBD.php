<?php

try{
    $conexion = mysqli_connect("localhost","root","","descuentocitydb");

}
catch(mysqli_sql_exception){
    echo "Ocurrio un error al conectar base de datos...";
}

?>





//conexion BD descuento City (Hostinger)
/*
try{
    $conexion = mysqli_connect("localhost","u442652021_descuentocity","Descuentocity123","u442652021_descuentocity");

}
catch(mysqli_sql_exception){
    echo "Ocurrio un error al conectar base de datos...";
}

?>
*/
