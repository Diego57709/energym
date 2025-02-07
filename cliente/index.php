<?php
declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../partials/db.php';
include '../partials/encrypt.php';

require_once __DIR__ . '/../components/vendor/autoload.php';
include '../partials/db.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

session_start();

// Si no hay un usuario autenticado, redirigimos al login
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}
if (time() > $_SESSION['timeout']) {
  session_unset(); 
  session_destroy();
  header('Location: ../login.html');
  exit();
}

// Obtenemos los datos del cliente
$id_usuario = $_SESSION['id'];
$sql = "SELECT * FROM clientes WHERE cliente_id = '$id_usuario'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $clientes = mysqli_fetch_assoc($result);
}

$nombreCliente   = $clientes['nombre'];
$apellidosCliente= $clientes['apellidos'];

// Tiempo que le queda del plan
$fechaFin       = strtotime($clientes['end_sub']);
$fechaActual    = time();
$diferenciaSegundos = $fechaFin - $fechaActual;

// Controlar la suscripción expirada
$mostrarQR = true;
$mensaje   = "Tu suscripción ha expirado.";

if ($diferenciaSegundos <= 0) {
    $mostrarQR = false; // No mostramos el QR si expiró
    $dias = 0;
} else {
    $dias    = floor($diferenciaSegundos / 86400);
    $horas   = floor(($diferenciaSegundos % 86400) / 3600);
    $minutos = floor(($diferenciaSegundos % 3600) / 60);
    $mensaje = "$dias días, $horas horas y $minutos minutos";
}

// Qué tipo de plan tiene
$plan = ($clientes['plan'] == 1) ? 'Premiun' : 'Comfort';

// Consultar último pago y definir precio plan
$sqlLastPayment = "
    SELECT total 
    FROM historial_pagos
    WHERE cliente_id = '$id_usuario'
    ORDER BY fecha_pago DESC
    LIMIT 1
";
$resLast = mysqli_query($conn, $sqlLastPayment);

if ($resLast && mysqli_num_rows($resLast) > 0) {
    // Existe un pago anterior; usamos ese total
    $rowLast    = mysqli_fetch_assoc($resLast);
    $precioPlan = (float)$rowLast['total'];
} else {
    // No hay historial de pagos, usamos precio "base" según su plan
    $precioPlan = ($clientes['plan'] == 1) ? 25.99 : 19.99;
}

// Generar QR (si no está caducado)
$qrToken = bin2hex(random_bytes(16));
$updateTokenSql = "UPDATE clientes SET qr_token = '$qrToken' WHERE cliente_id = '$id_usuario'";
mysqli_query($conn, $updateTokenSql);

if ($mostrarQR) {
    $options = new QROptions([
        'version' => 5,
        'drawLightModules' => true,
    ]);
    $url = "https://energym.ddns.net/qr_verificacion.php?token={$qrToken}&cliente_id={$id_usuario}";
    $qrcode = (new QRCode($options))->render($url);
}

// Consulta de última asistencia
$sqlUltimaAsistencia = "
    SELECT fecha_hora 
    FROM asistencias 
    WHERE usuario_id = '$id_usuario' 
    ORDER BY fecha_hora DESC 
    LIMIT 1
";
$resultUltimaAsistencia = mysqli_query($conn, $sqlUltimaAsistencia);
$ultimaAsistencia = mysqli_fetch_assoc($resultUltimaAsistencia)['fecha_hora'] ?? 'Sin asistencias registradas';

// Consulta de asistencias del mes actual
$mesActual = date('Y-m');
$sqlAsistenciasMes = "
    SELECT COUNT(*) AS total_asistencias 
    FROM asistencias 
    WHERE usuario_id = '$id_usuario'
      AND DATE_FORMAT(fecha_hora, '%Y-%m') = '$mesActual'
";
$resultAsistenciasMes = mysqli_query($conn, $sqlAsistenciasMes);
$totalAsistenciasMes = mysqli_fetch_assoc($resultAsistenciasMes)['total_asistencias'] ?? 0;

//Encriptar ID
function encryptData($data) {
  $cipher = "aes-256-cbc";
  $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
  $encrypted = openssl_encrypt($data, $cipher, ENCRYPTION_KEY, 0, $iv);
  return base64_encode($iv . $encrypted);
}
$encrypted_id = encryptData((string)$_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bienvenido, <?php echo htmlspecialchars($nombreCliente); ?> | EnerGym</title>
  <!-- PayPal SDK -->
  <script src="https://www.paypal.com/sdk/js?client-id=AbRZMXJlSsa4gssluoNXdC1mq5DMl7tU-GBK_yHfAyEimULW-WzWLzPeDRpUGp-NrHcojhsQf0SNL8kX&currency=EUR"></script>
  <!-- Bootstrap CSS -->
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
    }
    .main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      margin-bottom: 20px;
    }
    .qr-img {
      width: 250px;
      height: 250px;
      margin: 0 auto;
      display: block;
    }
    .btn-rutina {
      background-color: rgb(15, 139, 141);
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 5px;
      text-decoration: none;
    }
    .btn-rutina:hover {
      background-color: rgb(12, 110, 111);
    }
  </style>
</head>
<body>
  <!-- Header -->
  <?php require '../partials/header1.view.php'; ?>

  <!-- Contenedor central con un ancho máximo -->
  <div class="main">
    <div class="container my-5" style="max-width: 900px;">
      <div class="row">
        
        <!-- COLUMNA PRINCIPAL -->
        <div class="<?php echo $mostrarQR ? 'col-md-8' : 'col-md-12'; ?> mb-4">
          <div class="card p-4">
            <h1 class="text-center mb-4">
              ¡Hola, <?php echo htmlspecialchars($apellidosCliente . ', ' . $nombreCliente); ?>!
            </h1>
            <p class="text-center">
              Te queda <br>
              <strong><?php echo $mensaje; ?></strong><br>
              de tu plan <strong><?php echo $plan; ?></strong>.
            </p>

            <?php if ($mostrarQR): ?>
              <img src="<?php echo $qrcode; ?>" alt="QR Code" class="qr-img mt-3">
            <?php endif; ?>
            <?php if (!empty($clientes['chat_id'])): ?>
                <p class="text-success" style="text-align: center;">Conectado a Telegram</p>
            <?php else: ?>
              <a href="tg://resolve?domain=energymAsir_bot&start=<?php echo urlencode($encrypted_id); ?>" 
                  class="btn btn-primary">
                  📲 Conectar con Telegram
                </a>

            <?php endif; ?>


            <!-- Botones -->
            <div class="d-flex justify-content-center mt-4">
              <!-- Ver datos -->
              <a href="clienteModificar.php" class="btn-rutina me-2">Modificar datos</a>
              <a href="clientecambiarPassword.php" class="btn-rutina me-2">Cambiar contraseña</a>
            </div>

            <!-- Cerrar sesión -->
            <form action="../logoutProcesar.php" method="post" class="text-center mt-4">
              <button class="btn btn-danger">Cerrar sesión</button>
            </form>
          </div>
        </div>
            
        <?php if ($mostrarQR): ?> 
        <!-- COLUMNA LATERAL -->
        <div class="col-md-4">
          <!-- BLOQUE DE CLASES -->
          <div class="card p-4">
            <h5 class="mb-3">Clases</h5>
            <p>
              <strong>Tienes una clase el día:</strong><br>
              <?php echo date('d F Y', $fechaFin); ?>
            </p>
            <div class="d-flex justify-content-center">
              <a href="clienteClasesGrupales.php" class="btn btn-primary me-2">Clases grupales</a>
            </div>
          </div>

          <!-- BLOQUE DE PAGOS -->
          <div class="card p-4">
            <h5 class="mb-3">Pagos</h5>
            <p>
              <strong>Tu suscripción expira el:</strong><br>
              <?php echo date('d F Y', $fechaFin); ?>
            </p>
            <div class="d-flex flex-column align-items-center">
              <a href="clientehistorialPagos.php" class="btn btn-secondary mb-2 w-100 text-center">
                Historial de Pagos
              </a>
              <a href="#" id="payWithPaypal" class="btn btn-warning w-100 text-center">
                Ampliar Suscripción
              </a>
            </div>
          </div>

          <!-- BLOQUE DE ASISTENCIAS -->
          <div class="card p-4">
            <h5 class="mb-3">Asistencias</h5>
            <p>
              <strong>Última asistencia:</strong><br>
              <?php
              if ($ultimaAsistencia !== 'Sin asistencias registradas') {
                  echo date('d F Y, h:i A', strtotime($ultimaAsistencia));
              } else {
                  echo $ultimaAsistencia;
              }
              ?>
            </p>
            <p>
              <strong>Asistencias este mes:</strong> <?php echo $totalAsistenciasMes; ?>
            </p>
            <!-- Link al historial -->
            <a href="clientehistorialAsistencias.php" class="btn btn-sm btn-info">
              Ver historial de asistencias
            </a>
          </div>
        </div>
        <?php endif; ?> 
        </div>
      </div>
    </div>
  </div>
  
  <?php require '../partials/chatbot.php'; ?>

  <!-- PayPal Payment Trigger -->
  <script>
    const payButton = document.getElementById('payWithPaypal');
    payButton.addEventListener('click', function(event){
        event.preventDefault();  // Evitar comportamiento de enlace


        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            // Aseguramos que el precio sea en string con dos decimales
                            value: '<?php echo number_format($precioPlan, 2, '.', ''); ?>' 
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    let transactionId = details.id;

                    // Redirigir a clienteAmpliar.php con datos del pago
                    window.location.href = "clienteAmpliar.php?transaction_id=" + transactionId 
                        + "&cliente_id=<?php echo $id_usuario; ?>" 
                        + "&monto=<?php echo number_format($precioPlan, 2, '.', ''); ?>";
                });
            },
            onError: function(err) {
                console.error('Error en PayPal:', err);
                alert('Hubo un problema con el pago. Inténtalo de nuevo.');
                // Rehabilitamos el botón si ocurre error
                payButton.classList.remove('disabled');
                payButton.textContent = 'Ampliar Suscripción (<?php echo number_format($precioPlan, 2, '.', ''); ?>€)';
            }
        }).render('#payWithPaypal');
    });
  </script>

  <!-- Footer -->
  <?php require '../partials/footer.view.php'; ?>

</body>
</html>
