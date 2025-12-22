

<?php

//para hashear contra

$contraseña  = "g123";

$hash = password_hash($contraseña, PASSWORD_DEFAULT);

echo $hash;



?>

