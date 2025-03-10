<?php
include 'partials/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'partials/header1.view.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'components/phpmailer/src/Exception.php';
require 'components/phpmailer/src/PHPMailer.php';
require 'components/phpmailer/src/SMTP.php';

// Función para obtener la IP del cliente
function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

// Obtener el correo del formulario
$email = trim($_POST['email'] ?? '');
if (empty($email)) {
    $status = 'empty';
} else {
    // Obtener la IP del solicitante
    $ip_usuario = get_client_ip();
    
    // Inicializar variables
    $tablaEncontrada = null;
    $idUsuario = null;
    $token = bin2hex(random_bytes(16)); // Generar un token aleatorio
    $linkCambiarPassword = "";

    // Verificar en clientes
    $stmtClientes = mysqli_prepare($conn, "SELECT cliente_id AS id FROM clientes WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtClientes, "s", $email);
    mysqli_stmt_execute($stmtClientes);
    $resClientes = mysqli_stmt_get_result($stmtClientes);
    if ($resClientes && mysqli_num_rows($resClientes) === 1) {
        $usuario = mysqli_fetch_assoc($resClientes);
        $idUsuario = $usuario['id'];
        $tablaEncontrada = 'clientes';
        $linkCambiarPassword = "http://energym.ddns.net/cliente/crear_password.php?token=" . urlencode($token);
    }
    mysqli_stmt_close($stmtClientes);

    // Verificar en trabajadores
    if (!$tablaEncontrada) {
        $stmtTrabajadores = mysqli_prepare($conn, "SELECT trabajador_id AS id FROM trabajadores WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmtTrabajadores, "s", $email);
        mysqli_stmt_execute($stmtTrabajadores);
        $resTrabajadores = mysqli_stmt_get_result($stmtTrabajadores);
        if ($resTrabajadores && mysqli_num_rows($resTrabajadores) === 1) {
            $usuario = mysqli_fetch_assoc($resTrabajadores);
            $idUsuario = $usuario['id'];
            $tablaEncontrada = 'trabajadores';
            $linkCambiarPassword = "http://energym.ddns.net/trabajador/crear_password.php?token=" . urlencode($token);
        }
        mysqli_stmt_close($stmtTrabajadores);
    }

    // Verificar en entrenadores
    if (!$tablaEncontrada) {
        $stmtEntrenadores = mysqli_prepare($conn, "SELECT entrenador_id AS id FROM entrenadores WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmtEntrenadores, "s", $email);
        mysqli_stmt_execute($stmtEntrenadores);
        $resEntrenadores = mysqli_stmt_get_result($stmtEntrenadores);
        if ($resEntrenadores && mysqli_num_rows($resEntrenadores) === 1) {
            $usuario = mysqli_fetch_assoc($resEntrenadores);
            $idUsuario = $usuario['id'];
            $tablaEncontrada = 'entrenadores';
            $linkCambiarPassword = "http://energym.ddns.net/entrenador/crear_password.php?token=" . urlencode($token);
        }
        mysqli_stmt_close($stmtEntrenadores);
    }

    // Si no se encontró en ninguna tabla
    if (!$tablaEncontrada) {
        $status = 'notfound';
    } else {
        // Actualizar el token en la tabla correspondiente usando consulta preparada
        if ($tablaEncontrada === 'clientes') {
            $stmtUpdate = mysqli_prepare($conn, "UPDATE clientes SET reset_token = ? WHERE cliente_id = ?");
        } elseif ($tablaEncontrada === 'trabajadores') {
            $stmtUpdate = mysqli_prepare($conn, "UPDATE trabajadores SET reset_token = ? WHERE trabajador_id = ?");
        } elseif ($tablaEncontrada === 'entrenadores') {
            $stmtUpdate = mysqli_prepare($conn, "UPDATE entrenadores SET reset_token = ? WHERE entrenador_id = ?");
        }
        mysqli_stmt_bind_param($stmtUpdate, "si", $token, $idUsuario);
        mysqli_stmt_execute($stmtUpdate);
        mysqli_stmt_close($stmtUpdate);

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
            $mail->addAddress($email);

            // Contenido del correo, incluyendo la IP de la solicitud
            $mail->isHTML(true);
            $mail->Subject = 'Solicitud de cambio de contraseña - EnerGym';
            $mail->Body = "
                <h2>Hola,</h2>
                <p>Hemos recibido una solicitud para cambiar tu contraseña.</p>
                <p><strong>Dirección IP de la solicitud:</strong> $ip_usuario</p>
                <p>Haz clic en el siguiente enlace para crear tu nueva contraseña:</p>
                <p><a href='$linkCambiarPassword' target='_blank'>Cambiar Contraseña</a></p>
                <p>Si el enlace no funciona, copia y pega esta URL en tu navegador:</p>
                <p>$linkCambiarPassword</p>
                <br>
                <p>Si no has solicitado cambiar tu contraseña, puedes ignorar este mensaje.</p>
                <br>
                <p>Atentamente,<br>El equipo de EnerGym</p>
            ";

            $mail->send();
            $status = 'success';
        } catch (Exception $e) {
            $status = 'error';
        }
    }
}

// Mostrar la página con el estado correspondiente
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperación de contraseña</title>
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
        .container-box {
            max-width: 400px;
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
    <div class="container-box mt-5 mb-5">
        <?php if ($status === 'empty'): ?>
            <h2>Correo no ingresado</h2>
            <p>Por favor, proporciona tu correo electrónico.</p>
        <?php elseif ($status === 'notfound'): ?>
            <h2>Correo no encontrado</h2>
            <p>No hemos encontrado una cuenta asociada con este correo.</p>
        <?php elseif ($status === 'success'): ?>
            <h2>Solicitud enviada</h2>
            <p>Hemos enviado un correo a <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
            <p>Sigue las instrucciones para recuperar tu contraseña.</p>
        <?php else: ?>
            <h2>Error al enviar el correo</h2>
            <p>Hubo un problema al intentar enviar el correo. Por favor, inténtalo de nuevo más tarde.</p>
        <?php endif; ?>
        <a href="login.php" class="btn btn-primary mt-3">Volver al inicio de sesión</a>
    </div>
</div>
<?php include 'partials/footer.view.php'; ?>
</body>
</html>
