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
$cliente_id    = intval(trim($_GET['cliente_id'] ?? ''));
$monto         = floatval(trim($_GET['monto'] ?? ''));
$metodo_pago   = trim($_GET['metodo_pago'] ?? 'PayPal');  // valor por defecto: PayPal

// Si no existe el cliente_id o monto, redirige o muestra error
if (empty($cliente_id) || empty($monto)) {
    header("Location: 404.php");
    exit();
}


// 1) Verificamos que el cliente existe
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

$cliente   = mysqli_fetch_assoc($resCliente);
$dni       = $cliente['dni'];
$nombre    = $cliente['nombre'];
$apellidos = $cliente['apellidos'];
$email     = $cliente['email'];
$end_sub   = $cliente['end_sub'];


// 2) Obtener la duración (en días) de la extensión desde la tabla planes
$plan_id = $cliente['plan'] ?? null;
if ($plan_id) {
    $sqlPlan = "SELECT duracion_dias FROM planes WHERE plan_id = '$plan_id' LIMIT 1";
    $resPlan = mysqli_query($conn, $sqlPlan);
    if ($resPlan && mysqli_num_rows($resPlan) > 0) {
        $plan = mysqli_fetch_assoc($resPlan);
        $extender_dias = (int)$plan['duracion_dias'];
    } else {
        $extender_dias = 30;
    }
} else {
    $extender_dias = 30;
}


// 3) Extender suscripción sumándole la duración obtenida
$nuevaFechaFin = date('Y-m-d H:i:s', strtotime($end_sub . " +{$extender_dias} days"));
$updateSQL = "UPDATE clientes SET end_sub = '$nuevaFechaFin' WHERE cliente_id = '$cliente_id'";
mysqli_query($conn, $updateSQL);


// 4) Registrar el pago en historial_pagos (usando prepared statements)
$sqlHistorial = "INSERT INTO historial_pagos (cliente_id, metodo_pago, total, recurrente) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sqlHistorial);
if (!$stmt) {
    error_log("Error al preparar el statement: " . $conn->error);
} else {
    $recurrente = 1; // Indicador de pago recurrente
    $stmt->bind_param("isdi", $cliente_id, $metodo_pago, $monto, $recurrente);
    if (!$stmt->execute()) {
        error_log("Error al ejecutar el statement: " . $stmt->error);
    }
    $stmt->close();
}


// 5) Enviar correo de confirmación
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
    $mail->Subject = 'Renovación de Suscripción - EnerGym';
    $mail->Body    = "
        <h2>¡Hola, $nombre $apellidos!</h2>
        <p>Tu suscripción ha sido renovada con éxito durante $extender_dias días más.</p>
        <p>Nueva fecha de expiración: <b>" . date('d-m-Y H:i', strtotime($nuevaFechaFin)) . "</b></p>
        <p>¡Gracias por seguir con nosotros!</p>
    ";
    $mail->send();

} catch (Exception $e) {
    error_log("Error enviando correo: " . $mail->ErrorInfo);
}

// 6) Mostrar página de éxito
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
                <h2>¡Suscripción Renovada!</h2>
                <p>Se ha extendido la suscripción hasta el <b><?php echo date('d-m-Y', strtotime($nuevaFechaFin)); ?></b>.</p>
                <p>Se ha enviado un correo de confirmación a <b><?php echo htmlspecialchars($email); ?></b>.</p>
                <p>Redirigiendo al panel de cliente...</p>
                <script>
                    setTimeout(function() {
                        window.location.href = 'https://energym.ddns.net/cliente/index.php';
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
