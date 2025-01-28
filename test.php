<?php
$nombre='usuario';
$valor='Carlos';

$expiracion = time() + 60 * 30;

$seguridad = false;
$solohttp = true;

setcookie($nombre, $valor, $expiracion, $ruta, $dominio, $seguridad, $dolohttp);

$nombre='password';
$valor='12345';
setcookie($nombre, $valor);
echo "cookie establecida";

echo "<pre>";
var_dump($_COOKIE);
echo "</pre>";
?>