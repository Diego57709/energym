<?php
// 1) Primero, tu "use" y el autoload
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

require 'partials/header2.view.php';
include 'partials/db.php';

// Asegurarnos de que se recibe POST
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: 404.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_REQUEST['dni'];
    $plan = $_REQUEST['plan'];
    $extras = $_REQUEST['extrasSelected'];
    $nombre = $_REQUEST['nombre'];
    $email  = $_REQUEST['email'];
    $telefono = $_REQUEST['telefono'];
    $direccion = $_REQUEST['direccion'];
    $codigo_postal = $_REQUEST['codigo_postal'];
    $fecha_nacimiento = $_REQUEST['fecha_nacimiento'];
    $genero = $_REQUEST['genero'];
    $metodo_pago = $_REQUEST['metodo_pago'];

    // Determinar ID del plan
    if ($plan === "Comfort") {
        $plan_id = 1;
    } elseif ($plan === "Premium") {
        $plan_id = 2;
    }

    $start_sub  = date("Y-m-d");
    $end_sub    = date("Y-m-d", strtotime("+30 days"));
    $created_at = date("Y-m-d");

    // Insertar el nuevo cliente
    $sql = "INSERT INTO clientes 
        (dni, plan, extrasSelected, nombre, email, telefono, direccion, codigo_postal, fecha_nacimiento, genero, metodo_pago, start_sub, end_sub, created_at)
        VALUES ('$dni', '$plan_id', '$extras', '$nombre', '$email', '$telefono', 
                '$direccion', '$codigo_postal', '$fecha_nacimiento', '$genero', 
                '$metodo_pago', '$start_sub', '$end_sub', '$created_at')";

    $result = mysqli_query($conn, $sql);

    // Si se insertó con éxito, generamos token y enviamos email
    if ($result) {
        // Obtenemos el último ID insertado
        $cliente_id = mysqli_insert_id($conn);

        // Generar un token aleatorio
        $token = bin2hex(random_bytes(16)); // 32 caracteres hexadecimales

        // Guardamos el token en la tabla clientes
        $updateTokenSql = "UPDATE clientes 
                           SET reset_token = '$token'
                           WHERE cliente_id = $cliente_id";
        mysqli_query($conn, $updateTokenSql);

        // Construir el link para la página de crear contraseña
        // Ajusta a tu dominio real:
        $linkCrearPassword = "http://energym.ddns.net/crear_password.php?token=" . urlencode($token);

        // Instanciamos PHPMailer (ya importado arriba)
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'energym.asir@gmail.com';
            // Usa aquí la contraseña/correo de aplicación
            $mail->Password   = 'wvaz qdrj yqfm bnub';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            // Remitente
            $mail->setFrom('energym.asir@gmail.com', 'EnerGym');

            // Destinatario
            $mail->addAddress($email, $nombre);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Bienvenido a EnerGym - Crea tu contraseña';
            $mail->Body    = "
                <h2>¡Hola, $nombre!</h2>
                <p>Te damos la bienvenida a nuestro servicio. Para completar tu registro, haz clic en el enlace de abajo para crear tu contraseña:</p>
                <p><a href='$linkCrearPassword' target='_blank'>Crear contraseña</a></p>
                <p>Si no puedes hacer clic, copia y pega esta URL en tu navegador: <br>$linkCrearPassword</p>
                <br>
                <p>¡Gracias por registrarte con nosotros!</p>
            ";

            // Enviar
            $mail->send();

            // Mostrar mensaje de éxito
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro</title>
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
                        justify-content: center;
                        align-items: center;
                    }
                    .inner-text {
                        text-align: center;
                        color: #333;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
            <div class="main">
                <div class="container text-center">
                    <h2 class="mb-4">Registro</h2>
                    <div class="alert alert-success" role="alert">
                        <h3 class="inner-text">Usuario creado correctamente.</h3>
                        <p class="mt-3">Te hemos enviado un correo para que crees tu contraseña.</p>
                        <p class="mt-3">Volviendo al inicio de sesión...</p>
                    </div>
                    <script>
                setTimeout(function(){
                    window.location.href = 'login.php';
                }, 5000);
            </script>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
            </body>
            </html>
            <?php

        } catch (Exception $e) {
            // Si ocurre un error en el envío
            echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }

    } else {
        // Error al insertar el usuario
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Registro</title>
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
                    justify-content: center;
                    align-items: center;
                }
                .inner-text {
                    text-align: center;
                    color: #333;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
        <div class="main">
            <div class="container text-center">
                <h2 class="mb-4">Registro</h2>
                <div class="alert alert-danger" role="alert">
                    <h3 class="inner-text">Error: Usuario no creado.</h3>
                    <p class="mt-3">Volviendo a planes...</p>
                </div>
                <?php header("Refresh: 3; URL=planes.php"); ?>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
    }
}

require 'partials/footer.view.php';
?>
