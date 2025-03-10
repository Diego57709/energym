<?php
session_start();
include 'partials/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use OTPHP\TOTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'components/vendor/autoload.php';

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

$ip_usuario = get_client_ip();

// 1) Verificar si la sesión tiene los datos necesarios para 2FA
if (empty($_SESSION['2fa_user_id']) || empty($_SESSION['2fa_user_type']) || empty($_SESSION['2fa_email'])) {
    header("Location: login.php?error=no_2fa_data");
    exit();
}

// 2) Si el usuario aún no ha ingresado un código, mostrar formulario
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    include 'partials/header1.view.php'; // 🔹 Incluir header
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verificación 2FA</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
            body {
                display: flex;
                flex-direction: column;
            }
            .main {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-container {
                width: 400px;
                padding: 20px;
                background-color: #f8f9fa;
                border-radius: 10px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
                margin: 5rem auto;
            }
            .alert-danger {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.95rem;
            }
            .alert-danger i {
                font-size: 1.3rem;
                color: #dc3545;
            }
            .code-inputs {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .code-inputs input {
                width: 50px;
                height: 50px;
                font-size: 24px;
                text-align: center;
                border: 2px solid #ced4da;
                border-radius: 8px;
            }
            .code-inputs input:focus {
                border-color: #28a745;
                outline: none;
            }
            .separator {
                font-size: 24px;
                font-weight: bold;
                color: #6c757d;
                margin: 0 5px;
            }
        </style>
    </head>
    <body>

    <div class="main">
        <div class="login-container mx-auto">
            <h2 class="text-center mb-4">Verificación 2FA</h2>

            <form action="loginProcesar2FA.php" method="POST" id="2fa-form">
                <!-- Campo Código 2FA -->
                <div class="mb-3 text-center">
                    <label for="2fa_code" class="form-label">Introduce tu código de autenticación:</label>
                    <div class="code-inputs">
                        <input type="text" maxlength="1" oninput="moveFocus(this, 'digit2')" id="digit1" required>
                        <input type="text" maxlength="1" oninput="moveFocus(this, 'digit3')" id="digit2" required>
                        <input type="text" maxlength="1" oninput="moveFocus(this, 'digit4')" id="digit3" required>
                        <span class="separator">-</span>
                        <input type="text" maxlength="1" oninput="moveFocus(this, 'digit5')" id="digit4" required>
                        <input type="text" maxlength="1" oninput="moveFocus(this, 'digit6')" id="digit5" required>
                        <input type="text" maxlength="1" oninput="combineCode()" id="digit6" required>
                    </div>
                    <input type="hidden" id="2fa_code" name="2fa_code">
                </div>

                <!-- Mensaje de error -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        Código incorrecto. Por favor, inténtalo de nuevo.
                    </div>
                <?php endif; ?>

                <!-- Botón de verificación -->
                <button type="submit" class="btn w-100 mt-3" style="background-color:#28a745; color:white;">Verificar Código</button>
            </form>
        </div>
    </div>

    <?php include 'partials/footer.view.php'; ?>

    <script>
        function moveFocus(current, nextFieldId) {
            if (current.value.length >= current.maxLength) {
                document.getElementById(nextFieldId)?.focus();
            }
        }

        function combineCode() {
            const digits = [
                document.getElementById("digit1").value,
                document.getElementById("digit2").value,
                document.getElementById("digit3").value,
                document.getElementById("digit4").value,
                document.getElementById("digit5").value,
                document.getElementById("digit6").value
            ];
            document.getElementById("2fa_code").value = digits.join("");
        }
    </script>

    </body>
    </html>
    <?php
    exit();
}


// 3) Obtener el código ingresado
$codigoIngresado = $_POST['2fa_code'] ?? '';

if (empty($codigoIngresado)) {
    header("Location: loginProcesar2FA.php?error=falta_codigo");
    exit();
}

// 4) Buscar en la tabla CLIENTES
$userId = $_SESSION['2fa_user_id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM clientes WHERE cliente_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
$tipoUsuario = "cliente";

// 5) Si no está en CLIENTES, buscar en la tabla TRABAJADORES
if (!$user) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM trabajadores WHERE trabajador_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    $tipoUsuario = "trabajador";
}

// 6) Si no está en TRABAJADORES, buscar en la tabla ENTRENADORES
if (!$user) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM entrenadores WHERE entrenador_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    $tipoUsuario = "entrenador";
}

// 7) Si el usuario no existe, detener la ejecución
if (!$user) {
    header("Location: login.php?error=usuario_no_existe");
    exit();
}

// 8) Recuperar el secreto TOTP de la base de datos
$secretBase32 = trim($user['google_2fa_secret'] ?? '');

if (empty($secretBase32)) {
    header("Location: login.php?error=no_2fa_secret");
    exit();
}

// 9) Verificar el código TOTP
$totp = TOTP::create($secretBase32);
$isValid = $totp->verify($codigoIngresado, time(), 1);

if (!$isValid) {
    header("Location: loginProcesar2FA.php?error=codigo_invalido");
    exit();
}

// === Si el código es correcto, completamos el login ===
unset($_SESSION['2fa_user_id'], $_SESSION['2fa_user_type'], $_SESSION['2fa_email']);

// 🔹 **Cada usuario mantiene su estructura de sesión**
if ($tipoUsuario === "cliente") {
    $_SESSION['nombre']    = $user['nombre'];
    $_SESSION['id']        = $user['cliente_id'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['usuario']   = "cliente";
    $_SESSION['timeout']   = time() + 1800;
} elseif ($tipoUsuario === "trabajador") {
    $_SESSION['nombre']    = $user['nombre'];
    $_SESSION['id']        = $user['trabajador_id'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['usuario']   = "trabajador";
    $_SESSION['rol']       = $user['rol'];
    $_SESSION['timeout']   = time() + ($user['rol'] === 'camara' ? 43200 : 1800);
} else {
    $_SESSION['nombre']    = $user['nombre'];
    $_SESSION['id']        = $user['entrenador_id'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['usuario']   = "entrenador";
    $_SESSION['timeout']   = time() + 1800;
}

// Enviar correo de login exitoso
enviarNotificacionLogin($user['email'], $user['nombre'], $ip_usuario);

// 10) Redirigir al usuario a su área correspondiente
if ($tipoUsuario === "cliente") {
    header("Location: cliente/");
} elseif ($tipoUsuario === "trabajador") {
    header("Location: trabajador/");
} else {
    header("Location: entrenador/");
}
exit();

/**
 * Función para enviar un correo de notificación de inicio de sesión exitoso
 */
function enviarNotificacionLogin($destinatario, $nombre, $ip) {
    $mail = new PHPMailer(true);
    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'energym.asir@gmail.com';
        $mail->Password   = 'wvaz qdrj yqfm bnub';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        
        $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
        $mail->addAddress($destinatario, $nombre);
        
        $mail->isHTML(true);
        $mail->Subject = 'Notificación de inicio de sesión';
        $mail->Body    = "
            <h2>Hola, $nombre</h2>
            <p>Hemos detectado un inicio de sesión exitoso en tu cuenta.</p>
            <p><strong>Dirección IP:</strong> $ip</p>
            <p>Si no fuiste tú, por favor revisa la seguridad de tu cuenta.</p>
            <p>Atentamente,<br>El equipo de EnerGym</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo de inicio de sesión: {$mail->ErrorInfo}");
    }
}
