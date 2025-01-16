<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    $apellidos = $_REQUEST['apellidos'];
    $email  = $_REQUEST['email'];
    $telefono = $_REQUEST['telefono'];
    $direccion = $_REQUEST['direccion'];
    $codigo_postal = $_REQUEST['codigo_postal'];
    $fecha_nacimiento = $_REQUEST['fecha_nacimiento'];
    $genero = $_REQUEST['genero'];
    $metodo_pago = $_REQUEST['metodo_pago'];

    // Check for duplicate email
    $email_check_sql = "SELECT * FROM clientes WHERE email = '$email'";
    $email_check_result = mysqli_query($conn, $email_check_sql);
    if (mysqli_num_rows($email_check_result) > 0) {
        $error_message = 'El correo electrónico ya está registrado.';
    }
    // Check for duplicate dni
    $dni_check_sql = "SELECT * FROM clientes WHERE dni = '$dni'";
    $dni_check_result = mysqli_query($conn, $dni_check_sql);
    if (mysqli_num_rows($dni_check_result) > 0) {
        $error_message = 'El DNI ya está registrado.';
    }
    // Check for duplicate telefono
    $telefono_check_sql = "SELECT * FROM clientes WHERE telefono = '$telefono'";
    $telefono_check_result = mysqli_query($conn, $telefono_check_sql);
    if (mysqli_num_rows($telefono_check_result) > 0) {
        $error_message = 'El número de teléfono ya está registrado.';
    }

    // If any duplicates are found, display an error page
    if (isset($error_message)) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error - Registro</title>
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
                    <h3 class="inner-text">Error: <?php echo $error_message; ?></h3>
                    <p class="mt-3">Por favor, corrige el problema e intenta nuevamente.</p>
                    <p class="mt-3">Volviendo a la página de planes...</p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'planes.php';
                    }, 5000);
                </script>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
        require 'partials/footer.view.php';
        exit();
    }

    // Proceed with insertion if no duplicates are found
    $start_sub  = date("Y-m-d");
    $end_sub    = date("Y-m-d", strtotime("+30 days"));
    $created_at = date("Y-m-d");

    // Insertar el nuevo cliente
    $sql = "INSERT INTO clientes 
        (dni, plan, extrasSelected, nombre, apellidos, email, telefono, direccion, codigo_postal, fecha_nacimiento, genero, metodo_pago, start_sub, end_sub, created_at)
        VALUES ('$dni', '$plan', '$extras', '$nombre', '$apellidos', '$email', '$telefono', 
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
        $linkCrearPassword = "http://energym.ddns.net/crear_password.php?token=" . urlencode($token);

        // Instanciamos PHPMailer (ya importado arriba)
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'energym.asir@gmail.com';
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
                <h2>¡Hola, $nombre $apellidos!</h2>
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
            <title>Error - Registro</title>
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
                    <h3 class="inner-text">Error: No se pudo crear el usuario.</h3>
                    <p class="mt-3">Hubo un problema al registrar tus datos. Por favor, intenta nuevamente.</p>
                    <p class="mt-3">Volviendo a la página de planes...</p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'planes.php';
                    }, 5000);  // Redirection to 'planes.php' after 5 seconds
                </script>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
    }
}

?>
