<?php
include '../partials/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../components/phpmailer/src/Exception.php';
require '../components/phpmailer/src/PHPMailer.php';
require '../components/phpmailer/src/SMTP.php';

session_start();

// Verificar si el cliente está logueado
if (!isset($_SESSION['id']) || ($_SESSION['usuario'] !== 'cliente')) {
    header("Location: login.php");
    exit();
}
function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
$email = $_SESSION['email'];
$nombre = $_SESSION['usuario'];

$token = bin2hex(random_bytes(16)); // Token aleatorio de 32 caracteres

$idCliente = $_SESSION['id'];
$updateSql = "UPDATE clientes SET reset_token = '$token' WHERE cliente_id = $idCliente";
mysqli_query($conn, $updateSql);

// Enlace para cambiar contraseña
$linkCambiarPassword = "http://energym.ddns.net/cliente/crear_password.php?token=" . urlencode($token);

// Configuración de PHPMailer para enviar el correo
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPAuth = true;
    $mail->Username = 'energym.asir@gmail.com';
    $mail->Password = 'wvaz qdrj yqfm bnub';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    // Configuración del remitente y destinatario
    $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
    $mail->addAddress($email, $nombre);

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Solicitud de cambio de contraseña - EnerGym';
    $client_ip = get_client_ip();

    $mail->Body = "
        <h2>Hola, $nombre</h2>
        <p>Hemos recibido una solicitud para cambiar tu contraseña desde la dirección IP: <strong>$client_ip</strong>.</p>
        <p>Si has realizado esta solicitud, haz clic en el siguiente enlace para crear tu nueva contraseña:</p>
        <p><a href='$linkCambiarPassword' target='_blank'>Cambiar Contraseña</a></p>
        <p>Si el enlace no funciona, copia y pega esta URL en tu navegador:</p>
        <p>$linkCambiarPassword</p>
        <br>
        <p>Si no has solicitado cambiar tu contraseña, puedes ignorar este mensaje.</p>
        <br>
        <p>Atentamente,<br>El equipo de EnerGym</p>
    ";


    $mail->send();
} catch (Exception $e) {
    echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
    exit();
}
require '../partials/header1.view.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de cambio de contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .info-container {
            width: 400px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
    </style>
</head>
<body>

<div class="main">
    <div class="info-container">
        <h2 class="mb-4">Solicitud enviada</h2>
        <p>Hemos enviado un correo a tu dirección <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
        <p>Por favor, revisa tu bandeja de entrada y sigue el enlace para cambiar tu contraseña.</p>
        <p class="text-muted mt-3">Si no lo ves, revisa tu carpeta de spam o intenta enviar la solicitud nuevamente.</p>
        <a href="index.php" class="btn w-100 mt-3" style="background-color: #0f8b8d; color:white;">Volver a mi cuenta</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<?php require '../partials/footer.view.php'; ?>

</body>
</html>
