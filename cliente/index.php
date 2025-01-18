<?php
declare(strict_types=1);
include '../partials/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluimos primero la autoload de Composer y luego la base de datos
require_once __DIR__ . '/../vendor/autoload.php';
include '../partials/db.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

session_start();

// Si no hay un usuario autenticado, redirigimos al login
if (!isset($_SESSION['usuario']) && !isset($_SESSION['id'])) {
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

$nombreCliente = $clientes['nombre'];
$apellidosCliente = $clientes['apellidos'];

// Tiempo que le queda del plan
$fechaFin = strtotime($clientes['end_sub']);
$fechaActual = time();
$diferenciaSegundos = $fechaFin - $fechaActual;

// Controlar la suscripción expirada
$mostrarQR = true;
$mensaje = "Tu suscripción ha expirado.";

if ($diferenciaSegundos <= 0) {
    $mostrarQR = false; // No mostramos el QR si expiró
    $dias = 0;
} else {
    $dias = floor($diferenciaSegundos / 86400);
    $horas = floor(($diferenciaSegundos % 86400) / 3600);
    $minutos = floor(($diferenciaSegundos % 3600) / 60);
    $mensaje = "$dias días, $horas horas y $minutos minutos";
}

// Qué tipo de plan tiene
$plan = ($clientes['plan'] == 1) ? 'Premiun' : 'Comfort';

// Generamos el QR si no está caducado
$qrToken = bin2hex(random_bytes(16)); // Token aleatorio de 32 caracteres
$updateTokenSql = "UPDATE clientes SET qr_token = '$qrToken' WHERE cliente_id = '$id_usuario'";
mysqli_query($conn, $updateTokenSql); // Guardar el token del QR en la BD

// Generar el QR con el nuevo token
if ($mostrarQR) {
    $options = new QROptions([
        'version' => 5,
        'drawLightModules' => true,
    ]);
    $url = "https://energym.ddns.net/qr_verificacion.php?token={$qrToken}&cliente_id={$id_usuario}";
    $qrcode = (new QRCode($options))->render($url);
}

// Consulta de última asistencia
$sqlUltimaAsistencia = "SELECT fecha_hora FROM asistencias WHERE usuario_id = '$id_usuario' ORDER BY fecha_hora DESC LIMIT 1";
$resultUltimaAsistencia = mysqli_query($conn, $sqlUltimaAsistencia);
$ultimaAsistencia = mysqli_fetch_assoc($resultUltimaAsistencia)['fecha_hora'] ?? 'Sin asistencias registradas';

// Consulta de asistencias del mes actual
$mesActual = date('Y-m');
$sqlAsistenciasMes = "SELECT COUNT(*) AS total_asistencias FROM asistencias WHERE usuario_id = '$id_usuario' AND DATE_FORMAT(fecha_hora, '%Y-%m') = '$mesActual'";
$resultAsistenciasMes = mysqli_query($conn, $sqlAsistenciasMes);
$totalAsistenciasMes = mysqli_fetch_assoc($resultAsistenciasMes)['total_asistencias'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bienvenido, <?php echo htmlspecialchars($nombreCliente); ?> | EnerGym</title>
  
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
        <div class="col-md-8 mb-4">
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
          <!-- BLOQUE DE PAGOS -->
          <div class="card p-4">
            <h5 class="mb-3">Pagos</h5>
            <p>
              <strong>Tu suscripción expira el:</strong><br>
              <?php echo date('d F Y', $fechaFin); ?>
            </p>
            <div class="d-flex justify-content-center">
              <a href="clientehistorialPagos.php" class="btn btn-secondary me-2">Ver Historial de Pagos</a>
              <a href="ampliarSuscripcion.php" class="btn btn-warning">Ampliar Suscripción</a>
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
            <p class="text-muted">
              ¡Sigue con tu entrenamiento y mantén tu progreso!
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
  <?php require '../partials/chatbot.php'; ?>
  <!-- Footer -->
  <?php require '../partials/footer.view.php'; ?>
</body>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</html>
