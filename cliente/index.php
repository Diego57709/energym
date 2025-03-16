<?php
declare(strict_types=1);
include '../partials/db.php';
include '../partials/encrypt.php';
date_default_timezone_set('Europe/Madrid');

require_once __DIR__ . '/../components/vendor/autoload.php';
include '../partials/db.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || ($_SESSION['usuario'] !== 'cliente')) {
    header("Location: ../login.php");
    exit;
}
if (time() > $_SESSION['timeout']) {
  session_unset(); 
  session_destroy();
  header('Location: ../login.html');
  exit();
}

$id_usuario = $_SESSION['id'];
$sql = "SELECT * FROM clientes WHERE cliente_id = '$id_usuario'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $clientes = mysqli_fetch_assoc($result);
}

$nombreCliente   = $clientes['nombre'];
$apellidosCliente= $clientes['apellidos'];

$fechaFin = strtotime($clientes['end_sub']);
$fechaActual = time();
$diferenciaSegundos = $fechaFin - $fechaActual;

$mostrarQR = true;
$mensaje   = "Tu suscripción ha expirado.";

if ($diferenciaSegundos <= 0) {
    $mostrarQR = false;
    $dias = 0;
} else {
    $dias    = floor($diferenciaSegundos / 86400);
    $horas   = floor(($diferenciaSegundos % 86400) / 3600);
    $minutos = floor(($diferenciaSegundos % 3600) / 60);
    $mensaje = "$dias días, $horas horas y $minutos minutos";
}

$plan = ($clientes['plan'] == 1) ? 'Premiun' : 'Comfort';

$sqlLastPayment = "
    SELECT total 
    FROM historial_pagos
    WHERE cliente_id = '$id_usuario'
    ORDER BY fecha_pago DESC
    LIMIT 1
";
$resLast = mysqli_query($conn, $sqlLastPayment);

if ($resLast && mysqli_num_rows($resLast) > 0) {
    $rowLast    = mysqli_fetch_assoc($resLast);
    $precioPlan = (float)$rowLast['total'];
} else {
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

$sqlSiguienteClase = "
    SELECT cg.fecha_hora_c 
    FROM clases_inscripciones ci
    JOIN clases_grupales cg ON ci.clase_id = cg.clase_id
    WHERE ci.cliente_id = '$id_usuario' AND cg.fecha_hora_c > NOW()
    ORDER BY cg.fecha_hora_c ASC
    LIMIT 1
";
$resultSiguienteClase = mysqli_query($conn, $sqlSiguienteClase);
if ($resultSiguienteClase && mysqli_num_rows($resultSiguienteClase) > 0) {
    $rowSiguienteClase = mysqli_fetch_assoc($resultSiguienteClase);
    $SiguienteClaseDate = strtotime($rowSiguienteClase['fecha_hora_c']);
} else {
    $SiguienteClaseDate = false;
}

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
        <div class="col-md-8">
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


            <!-- Botones -->
             <!-- Botón para abrir el modal de conexiones -->
              <button type="button" class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#modalConexiones">
                  🔗 Conexión a Aplicaciones
              </button>

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
            
        <!-- COLUMNA LATERAL -->
        <div class="col-md-4">
          <!-- BLOQUE DE CLASES -->
          <div class="card p-4">
            <h5 class="mb-3">Clases</h5>
            <p>
              <strong>Tienes una clase el día:</strong><br>
              <?php 
                if ($SiguienteClaseDate) {
                  echo date('d F Y, H:i', $SiguienteClaseDate);
                } else {
                    echo "No tienes ninguna clase programada.";
                }
              ?>
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
        </div>
      </div>
    </div>
  </div>
  
  <?php require '../partials/chatbot.php'; ?>

  <!-- PayPal Payment Trigger -->
  <script>
let paypalRendered = false;

const payButton = document.getElementById('payWithPaypal');

payButton.addEventListener('click', function(event) {
    event.preventDefault();

    if (paypalRendered) {
        return;
    }
    paypalRendered = true;

    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo number_format($precioPlan, 2, '.', ''); ?>' 
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                let transactionId = details.id;
                window.location.href = "clienteAmpliar.php?transaction_id=" + transactionId 
                    + "&cliente_id=<?php echo $id_usuario; ?>" 
                    + "&monto=<?php echo number_format($precioPlan, 2, '.', ''); ?>";
            });
        },
        onError: function(err) {
            console.error('Error en PayPal:', err);
            alert('Hubo un problema con el pago. Inténtalo de nuevo.');
            paypalRendered = false;
            payButton.classList.remove('disabled');
            payButton.textContent = 'Ampliar Suscripción (<?php echo number_format($precioPlan, 2, '.', ''); ?>€)';
        }
    }).render('#payWithPaypal');
});

  </script>

  <!-- Footer -->
  <?php require '../partials/footer.view.php'; ?>
  <!-- Modal de Conexión a Aplicaciones -->
  <div class="modal fade" id="modalConexiones" tabindex="-1" aria-labelledby="modalConexionesLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="modalConexionesLabel">Conectar Aplicaciones</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body text-center">
                  
                  <!-- 🔵 Conectar con Telegram -->
                  <p>📲 Conecta tu cuenta con Telegram:</p>
                  <?php if (!empty($clientes['chat_id'])): ?>
                      <p class="text-success">✅ Conectado a Telegram</p>
                      <a href="clienteDesconectarTelegram.php" class="btn btn-danger w-100 mb-3">
                          ❌ Desconectar de Telegram
                      </a>
                  <?php else: ?>
                      <a href="tg://resolve?domain=energymAsir_bot&start=<?php echo urlencode($encrypted_id); ?>" 
                        class="btn btn-outline-primary w-100 mb-3">
                          Conectar con Telegram
                      </a>
                  <?php endif; ?>

                  <!-- 🔒 Conectar con Google Authenticator -->
                  <p>🔒 Habilitar 2FA con Google Authenticator:</p>
                  <?php if (!empty($clientes['google_2fa_secret'])): ?>
                      <p class="text-success">✅ Google 2FA activado</p>
                      <a href="2FA/clienteDesactivar2FA.php" class="btn btn-danger w-100">
                          ❌ Desactivar Google 2FA
                      </a>
                  <?php else: ?>
                      <a href="2FA/clienteHabilitar2FA.php" class="btn btn-outline-dark w-100">
                          Activar Google 2FA
                      </a>
                  <?php endif; ?>

              </div>
          </div>
      </div>
  </div>

</body>
</html>
