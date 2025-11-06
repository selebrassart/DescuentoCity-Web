

<?php

//para hashear contra

$contraseña  = "admin123";

$hash = password_hash($contraseña, PASSWORD_DEFAULT);

echo $hash;



?>

