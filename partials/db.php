<?php

$db_server = "localhost";
$db_user = "energym";
$db_pass = "energym12345";
$db_name = "gimnasiodb";

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name) 
    or die("Error al conectar a la base de datos: ");
