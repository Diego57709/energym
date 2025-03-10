<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../partials/db.php';
require_once __DIR__ . '/../../components/vendor/autoload.php';

use OTPHP\TOTP;
use chillerlan\QRCode\QRCode;
use ParagonIE\ConstantTime\Base32;

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || $_SESSION['usuario'] !== 'cliente') {
    header("Location: ../login.php");
    exit;
}

$id_usuario = $_SESSION['id'];

// Consultamos si ya tiene un secreto en la BD
$sql = "SELECT google_2fa_secret FROM clientes WHERE cliente_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cliente = mysqli_fetch_assoc($result);

$existingSecret = $cliente['google_2fa_secret'] ?? '';

// Si no tiene un secreto, generamos uno nuevo
if (empty($existingSecret)) {
    $rawSecret = random_bytes(20);
    $secretBase32 = Base32::encodeUpper($rawSecret); // Asegurar que sea Base32 válido
} else {
    // Si ya tiene un secreto, lo usamos
    $secretBase32 = $existingSecret;
}

// Crear TOTP con el secreto
$totp = TOTP::create($secretBase32);
$totp->setLabel("EnerGym - $id_usuario");
$totp->setIssuer("EnerGym");

// Generar la URL para Google Authenticator
$url = $totp->getProvisioningUri();

// Generar el código QR
$qrcode = (new QRCode())->render($url);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Habilitar 2FA - EnerGym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require '../../partials/header1.view.php'; ?>
    
    <div class="main">
        <div class="container my-5" style="max-width: 600px;">
            <div class="card p-4">
                <h2 class="text-center">Habilitar Google Authenticator</h2>
                <p class="text-center">
                    Escanea este código QR en tu aplicación de autenticación (Google Authenticator, Authy, etc.):
                </p>
                <div class="text-center">
                    <img src="<?php echo $qrcode; ?>" alt="QR Code">
                </div>
                
                <p class="text-center mt-3">
                    O introduce este código manualmente: <strong><?php echo $secretBase32; ?></strong>
                </p>
                
                <form action="clienteVerificar2FA.php" method="POST" class="text-center">
                    <input type="hidden" name="secret" value="<?php echo $secretBase32; ?>">
                    <label>Introduce el código generado:</label>
                    <input type="text" name="token" class="form-control text-center" required>
                    <button type="submit" class="btn btn-success mt-3">Verificar Código</button>
                </form>
            </div>
        </div>
    </div>

    <?php require '../../partials/footer.view.php'; ?>
</body>
</html>
