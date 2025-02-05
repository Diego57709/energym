<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Carga de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../components/phpmailer/src/Exception.php';
require '../components/phpmailer/src/PHPMailer.php';
require '../components/phpmailer/src/SMTP.php';

require '../partials/header2.view.php';
include '../partials/db.php';

// Validar que el método sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header("Location: 404.php");
    exit();
}

// Recoger datos enviados por PayPal/form o como necesites
$cliente_id      = trim($_GET['cliente_id'] ?? '');
$monto           = trim($_GET['monto'] ?? '');
$metodo_pago     = trim($_GET['metodo_pago'] ?? 'PayPal');  // default PayPal
$extender_dias   = 30;  // días a extender

// Si no existe el cliente_id o monto, redirige o muestra error
if (empty($cliente_id) || empty($monto)) {
    header("Location: 404.php");
    exit();
}

// ------------------------------------------------------------------------
// 1) Verificamos que el cliente existe
// ------------------------------------------------------------------------
$sqlCliente = "SELECT * FROM clientes WHERE cliente_id = '$cliente_id' LIMIT 1";
$resCliente = mysqli_query($conn, $sqlCliente);
if (!$resCliente || mysqli_num_rows($resCliente) < 1) {
    // Si no se encuentra el cliente
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error - Cliente no encontrado</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
      <div class="container mt-5">
        <div class="alert alert-danger">
          <h2>Error</h2>
          <p>No se ha encontrado el cliente especificado. Verifica los datos e inténtalo de nuevo.</p>
        </div>
      </div>
    </body>
    </html>
    <?php
    require '../partials/footer.view.php';
    exit();
}

$cliente = mysqli_fetch_assoc($resCliente);
$dni      = $cliente['dni'];
$nombre   = $cliente['nombre'];
$apellidos= $cliente['apellidos'];
$email    = $cliente['email'];
$end_sub  = $cliente['end_sub'];

// ------------------------------------------------------------------------
// 2) Extender suscripción +30 días (o lo que decidas)
// ------------------------------------------------------------------------
$nuevaFechaFin = date('Y-m-d H:i:s', strtotime($end_sub . " +{$extender_dias} days"));
$updateSQL = "UPDATE clientes SET end_sub = '$nuevaFechaFin' WHERE cliente_id = '$cliente_id'";
mysqli_query($conn, $updateSQL);

// ------------------------------------------------------------------------
// 3) Registrar el pago en historial_pagos
// ------------------------------------------------------------------------
$sqlHistorial = "
    INSERT INTO historial_pagos
    (cliente_id, metodo_pago, total, recurrente)
    VALUES
    ('$cliente_id', '$metodo_pago', '$monto', '1')
";
mysqli_query($conn, $sqlHistorial);

// ------------------------------------------------------------------------
// 4) Enviar correo de confirmación
// ------------------------------------------------------------------------
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    // Ajusta estos credenciales a los tuyos:
    $mail->Username   = 'energym.asir@gmail.com';
    $mail->Password   = 'wvaz qdrj yqfm bnub';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
    $mail->addAddress($email, $nombre);

    $mail->isHTML(true);
    $mail->Subject = 'Renovación de Suscripción - EnerGym';
    $mail->Body    = "
        <h2>¡Hola, $nombre $apellidos!</h2>
        <p>Tu suscripción ha sido renovada con éxito durante $extender_dias días más.</p>
        <p>Nueva fecha de expiración: <b>".date('d-m-Y H:i', strtotime($nuevaFechaFin))."</b></p>
        <p>¡Gracias por seguir con nosotros!</p>
    ";
    $mail->send();

} catch (Exception $e) {
    // Si ocurre un error al enviar el correo
    error_log("Error enviando correo: " . $mail->ErrorInfo);
    // Podrías mostrar un mensaje al usuario, o continuar en silencio
}

// ------------------------------------------------------------------------
// 5) Mostrar página de éxito
// ------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Extensión de Suscripción</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .main {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            padding: 20px;
        }
        .alert {
            text-align: center;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<div class="main">
    <div class="container">
        <div class="alert alert-success">
            <h2>¡Suscripción Renovada!</h2>
            <p>Se ha extendido la suscripción hasta el <b><?php echo date('d-m-Y', strtotime($nuevaFechaFin)); ?></b>.</p>
            <p>Se ha enviado un correo de confirmación a <b><?php echo htmlspecialchars($email); ?></b>.</p>
            <p>Redirigiendo al panel de cliente...</p>
            <script>
                setTimeout(function() {
                    window.location.href = 'dashboard.php'; // Ajusta a tu ruta
                }, 5000);
            </script>
        </div>
    </div>
</div>
</body>
</html>
<?php
require '../partials/footer.view.php';
exit();
