<?php
session_start();
require_once '../../partials/db.php';
require_once '../../components/vendor/autoload.php';

use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

// 1) Verificar si el usuario está autenticado
if (empty($_SESSION['id']) || $_SESSION['usuario'] !== 'trabajador') {
    header("Location: ../login.php?error=no_2fa_data");
    exit();
}

// 2) Obtener los datos del formulario
$codigoIngresado = $_POST['token'] ?? '';
$secretBase32 = $_POST['secret'] ?? '';

if (empty($codigoIngresado) || empty($secretBase32)) {
    header("Location: trabajadorHabilitar2FA.php?error=faltan_datos");
    exit();
}

// 3) Validar que el secreto sea un Base32 válido antes de usarlo
if (!preg_match('/^[A-Z2-7]+=*$/', $secretBase32)) {
    die("Error: El secreto recibido no es un Base32 válido: $secretBase32");
}

// 4) Crear la instancia OTPHP con el secreto recibido
$totp = TOTP::create($secretBase32);
$isValid = $totp->verify($codigoIngresado, time(), 1); // Permite 1 ventana de tolerancia

if (!$isValid) {
    header("Location: trabajadorHabilitar2FA.php?error=codigo_invalido");
    exit();
}

// 5) Guardar el secreto en la BD después de la verificación exitosa
$id_usuario = $_SESSION['id'];
$sqlUpdate = "UPDATE trabajadores SET google_2fa_secret = ? WHERE trabajador_id = ?";
$stmt = mysqli_prepare($conn, $sqlUpdate);
mysqli_stmt_bind_param($stmt, "si", $secretBase32, $id_usuario);
mysqli_stmt_execute($stmt);

// Redirigir con éxito
header("Location: /trabajador/index.php?mensaje=2fa_habilitado");
exit();
