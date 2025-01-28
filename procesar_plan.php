<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Carga de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'components/phpmailer/src/Exception.php';
require 'components/phpmailer/src/PHPMailer.php';
require 'components/phpmailer/src/SMTP.php';

require 'partials/header2.view.php';
include 'partials/db.php';

// Validar que el método sea POST
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: 404.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos datos del formulario
    $dni              = trim($_POST['dni'] ?? '');
    $plan             = trim($_POST['plan'] ?? '');
    $extras           = trim($_POST['extrasSelected'] ?? '');
    $nombre           = trim($_POST['nombre'] ?? '');
    $apellidos        = trim($_POST['apellidos'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $telefono         = trim($_POST['telefono'] ?? '');
    $direccion        = trim($_POST['direccion'] ?? '');
    $codigo_postal    = trim($_POST['codigo_postal'] ?? '');
    $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
    $genero           = trim($_POST['genero'] ?? '');
    $metodo_pago      = trim($_POST['metodo_pago'] ?? '');

    // Array para acumular posibles errores (duplicados)
    $errores = [];

    // ------------------------------------------------------------------------
    // 1) Verificar duplicados (email, dni, telefono) con una sola consulta
    // ------------------------------------------------------------------------
    $sqlCheck = "
        SELECT email, dni, telefono 
        FROM clientes
        WHERE email = '$email' OR dni = '$dni' OR telefono = '$telefono'
    ";
    $resultCheck = mysqli_query($conn, $sqlCheck);

    // Si encontramos filas, verificamos qué campo coincide
    while ($row = mysqli_fetch_assoc($resultCheck)) {
        if ($row['email'] === $email) {
            $errores[] = 'El correo electrónico ya está registrado.';
        }
        if ($row['dni'] === $dni) {
            $errores[] = 'El DNI ya está registrado.';
        }
        if ($row['telefono'] === $telefono) {
            $errores[] = 'El número de teléfono ya está registrado.';
        }
    }

    // ------------------------------------------------------------------------
    // 2) Si existen errores (ej: duplicados), mostramos la pantalla de error
    // ------------------------------------------------------------------------
    if (!empty($errores)) {
        $error_message = implode('<br>', $errores);
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
                    padding: 0;
                }
                body {
                    display: flex;
                    flex-direction: column;
                    background-color: #f8f9fa;
                }
                .main {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                }
                .container {
                    max-width: 600px;
                    padding: 20px;
                }
                .alert {
                    text-align: center;
                    font-size: 1.2rem;
                }
            </style>
        </head>
        <body>
        <div class="main">
            <div class="container">
                <div class="alert alert-danger" role="alert">
                    <h2>Error en el Registro</h2>
                    <p><?php echo $error_message; ?></p>
                    <p>Por favor, corrige los problemas e inténtalo de nuevo.</p>
                </div>
            </div>
        </div>
        </body>
        </html>
        <?php
        require 'partials/footer.view.php';
        exit();
    }

    // ------------------------------------------------------------------------
    // 3) Si no hay duplicados, procedemos con la inserción
    // ------------------------------------------------------------------------
    $duracion_dias = 30;
    $sqlPlan = "SELECT duracion_dias FROM planes WHERE plan_id = '$plan'";
    $resultPlan = mysqli_query($conn, $sqlPlan);

    if ($resultPlan && $row = mysqli_fetch_assoc($resultPlan)) {
        $duracion_dias = (int) $row['duracion_dias'];
    }

    // Calculate subscription dates
    $start_sub = date("Y-m-d H:i:s");
    $end_sub = date("Y-m-d H:i:s", strtotime("+$duracion_dias days"));
    $created_at = date("Y-m-d H:i:s");
    $total      = ($plan === '1') ? 19.99 : 25.99; // Calcula el total según el plan

    $sqlInsert = "
        INSERT INTO clientes 
        (dni, plan, extrasSelected, nombre, apellidos, email, telefono, 
         direccion, codigo_postal, fecha_nacimiento, genero, metodo_pago, 
         start_sub, end_sub, created_at)
        VALUES (
            '$dni',
            '$plan',
            '$extras',
            '$nombre',
            '$apellidos',
            '$email',
            '$telefono',
            '$direccion',
            '$codigo_postal',
            '$fecha_nacimiento',
            '$genero',
            '$metodo_pago',
            '$start_sub',
            '$end_sub',
            '$created_at'
        )
    ";
    $resultado = mysqli_query($conn, $sqlInsert);

    if ($resultado) {
        $cliente_id = mysqli_insert_id($conn);

        $sqlHistorial = "
            INSERT INTO historial_pagos 
            (cliente_id, metodo_pago, total, recurrente) 
            VALUES (
                '$cliente_id',
                '$metodo_pago',
                '$total',
                '1'
            )
        ";
        mysqli_query($conn, $sqlHistorial);

        $token = bin2hex(random_bytes(16));
        $linkCrearPassword = "http://energym.ddns.net/cliente/crear_password.php?token=" . urlencode($token);

        $updateTokenSql = "
            UPDATE clientes
            SET reset_token = '$token'
            WHERE cliente_id = $cliente_id
        ";
        mysqli_query($conn, $updateTokenSql);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'energym.asir@gmail.com';
            $mail->Password   = 'wvaz qdrj yqfm bnub';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
            $mail->addAddress($email, $nombre);

            $mail->isHTML(true);
            $mail->Subject = 'Bienvenido a EnerGym - Crea tu contraseña';
            $mail->Body    = "
                <h2>¡Hola, $nombre $apellidos!</h2>
                <p>Te damos la bienvenida a nuestro servicio. Para completar tu registro, haz clic en el enlace de abajo para crear tu contraseña:</p>
                <p><a href='$linkCrearPassword'>Crear contraseña</a></p>
            ";
            $mail->send();

            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro Exitoso</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    html, body {
                        height: 100%;
                        margin: 0;
                        padding: 0;
                    }
                    body {
                        display: flex;
                        flex-direction: column;
                        background-color: #f8f9fa;
                    }
                    .main {
                        flex: 1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                    }
                    .container {
                        max-width: 600px;
                        padding: 20px;
                    }
                    .alert {
                        text-align: center;
                        font-size: 1.2rem;
                    }

                </style>
            </head>
            <body>
            <div class="main">
                <div class="container">
                    <div class="alert alert-success" role="alert">
                        <h2>¡Registro Exitoso!</h2>
                        <p>Usuario registrado correctamente.</p>
                        <p>Se ha enviado un correo electrónico para que puedas crear tu contraseña.</p>
                        <p class="text-muted mt-3">Si no lo ves, revisa tu carpeta de spam o intenta enviar la solicitud nuevamente.</p>
                        <p>Redirigiendo al inicio de sesión...</p>
                        <script>
                            setTimeout(function () {
                                window.location.href = 'login.php';
                            }, 5000); // Redirigir después de 5 segundos
                        </script>
                    </div>
                </div>
            </div>
            </body>
            </html>
            <?php

        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Error al enviar el correo: ' . $mail->ErrorInfo . '</div>';
        }

    } else {
        echo '<div class="alert alert-danger">Error al registrar al cliente.</div>';
    }
}

require 'partials/footer.view.php';
?>
