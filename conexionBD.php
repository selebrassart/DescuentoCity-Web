<?php

//conexion BD descuento City
try{
    $conexion = mysqli_connect("localhost","u442652021_descuentocity","Descuentocity123","u442652021_descuentocity");

}
catch(mysqli_sql_exception){
    echo "Ocurrio un error al conectar base de datos...";
}

?>

