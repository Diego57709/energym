<?php
session_start();

// Si la sesión no está iniciada o no se accede mediante POST, redirige al error 404
if (!isset($_SESSION['usuario'])) {
    include '404.php';
    exit();
}

// Si todo es correcto, destruir la sesión
session_unset();
session_destroy();

// Redirige al login u otra página
header("Location: login.php");
exit();
?>
