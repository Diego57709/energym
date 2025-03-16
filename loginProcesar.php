<?php
session_start();
include 'partials/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'components/phpmailer/src/Exception.php';
require 'components/phpmailer/src/PHPMailer.php';
require 'components/phpmailer/src/SMTP.php';

// Función para obtener la IP del usuario
function get_client_ip() {
    return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
}

$ip_usuario = get_client_ip();
$limite_intentos = 3;

if (!isset($_SESSION['intentos'][$ip_usuario])) {
    $_SESSION['intentos'][$ip_usuario] = ['contador' => 0, 'ultimo_intento' => time()];
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

function enviarNotificacionLogin($destinatario, $nombre, $ip) {
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

        $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
        $mail->addAddress($destinatario, $nombre);

        $mail->isHTML(true);
        $mail->Subject = 'Notificación de inicio de sesión';
        $mail->Body = "
            <h2>Hola, $nombre</h2>
            <p>Hemos detectado un inicio de sesión exitoso en tu cuenta.</p>
            <p><strong>Dirección IP:</strong> $ip</p>
            <p>Si no fuiste tú, por favor revisa la seguridad de tu cuenta.</p>
            <p>Atentamente,<br>El equipo de EnerGym</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
    }
}

function enviarAlertaIntentoFallido($destinatario, $nombre, $ip) {
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

        $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
        $mail->addAddress($destinatario, $nombre);

        $mail->isHTML(true);
        $mail->Subject = 'Alerta: Intento de acceso fallido';
        $mail->Body = "
            <h2>Hola, $nombre</h2>
            <p>Hemos detectado varios intentos fallidos de inicio de sesión en tu cuenta</p>
            <p><strong>Dirección IP:</strong> $ip</p>
            <p>Si no has sido tú, te recomendamos cambiar tu contraseña lo antes posible.</p>
            <p>Atentamente,<br>El equipo de EnerGym</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar alerta de acceso fallido: {$mail->ErrorInfo}");
    }
}

// Verificar en CLIENTES
$stmtClientes = mysqli_prepare($conn, "SELECT * FROM clientes WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmtClientes, "s", $email);
mysqli_stmt_execute($stmtClientes);
$resClientes = mysqli_stmt_get_result($stmtClientes);

if ($resClientes && mysqli_num_rows($resClientes) === 1) {
    $cliente = mysqli_fetch_assoc($resClientes);

    if (password_verify($password, $cliente['password'])) {
        if (!empty($cliente['google_2fa_secret'])) {
            $_SESSION['2fa_user_id']   = $cliente['cliente_id'];
            $_SESSION['2fa_user_type'] = 'cliente';
            $_SESSION['2fa_email']     = $cliente['email'];
            header('Location: loginProcesar2FA.php');
            exit();
        }

        $_SESSION['nombre']  = $cliente['nombre'];
        $_SESSION['id']      = $cliente['cliente_id'];
        $_SESSION['email']   = $cliente['email'];
        $_SESSION['usuario'] = "cliente";
        $_SESSION['timeout'] = time() + 1800;

        enviarNotificacionLogin($cliente['email'], $cliente['nombre'], $ip_usuario);
        header('Location: cliente/');
        exit();
    }
}

// Verificar en TRABAJADORES
$stmtTrab = mysqli_prepare($conn, "SELECT * FROM trabajadores WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmtTrab, "s", $email);
mysqli_stmt_execute($stmtTrab);
$resTrab = mysqli_stmt_get_result($stmtTrab);

if ($resTrab && mysqli_num_rows($resTrab) === 1) {
    $trabajador = mysqli_fetch_assoc($resTrab);

    if (password_verify($password, $trabajador['password'])) {
        if (!empty($trabajador['google_2fa_secret'])) {
            $_SESSION['2fa_user_id']   = $trabajador['trabajador_id'];
            $_SESSION['2fa_user_type'] = 'trabajador';
            $_SESSION['2fa_email']     = $trabajador['email'];
            header('Location: loginProcesar2FA.php');
            exit();
        }

        $_SESSION['nombre']  = $trabajador['nombre'];
        $_SESSION['id']      = $trabajador['trabajador_id'];
        $_SESSION['email']   = $trabajador['email'];
        $_SESSION['usuario'] = "trabajador";
        $_SESSION['rol']     = $trabajador['rol'];
        $_SESSION['timeout']   = time() + ($user['rol'] === 'camara' ? 43200 : 1800);

        enviarNotificacionLogin($trabajador['email'], $trabajador['nombre'], $ip_usuario);
        header('Location: trabajador/');
        exit();
    }
}

// Verificar en ENTRENADORES
$stmtEntr = mysqli_prepare($conn, "SELECT * FROM entrenadores WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmtEntr, "s", $email);
mysqli_stmt_execute($stmtEntr);
$resEntr = mysqli_stmt_get_result($stmtEntr);

if ($resEntr && mysqli_num_rows($resEntr) === 1) {
    $entrenador = mysqli_fetch_assoc($resEntr);

    if (password_verify($password, $entrenador['password'])) {
        if (!empty($entrenador['google_2fa_secret'])) {
            $_SESSION['2fa_user_id']   = $entrenador['entrenador_id'];
            $_SESSION['2fa_user_type'] = 'entrenador';
            $_SESSION['2fa_email']     = $entrenador['email'];
            header('Location: loginProcesar2FA.php');
            exit();
        }

        $_SESSION['nombre']  = $entrenador['nombre'];
        $_SESSION['id']      = $entrenador['entrenador_id'];
        $_SESSION['email']   = $entrenador['email'];
        $_SESSION['usuario'] = "entrenador";
        $_SESSION['timeout'] = time() + 1800;

        enviarNotificacionLogin($entrenador['email'], $entrenador['nombre'], $ip_usuario);
        header('Location: entrenador/');
        exit();
    }
}

// Si no se encuentra el email en ninguna tabla
header('Location: login.php?error');
exit();
?>
