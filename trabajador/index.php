<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';
include '../partials/db.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

session_start();

// Si no hay un usuario autenticado, redirigimos al login
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}
if (time() > $_SESSION['timeout']) {
  session_unset(); 
  session_destroy();
  header('Location: login.html');
  exit();
}

// Obtenemos los datos del trabajador
$id_trabajador = $_SESSION['id'];
$sql = "SELECT * FROM trabajadores WHERE trabajador_id = '$id_trabajador'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 1) {
    $trabajador = mysqli_fetch_assoc($result);
}

$nombreTrabajador = $trabajador['nombre'];
$rol = $trabajador['rol'];
$_SESSION['rol'] = $trabajador['rol'];
$_SESSION['email'] = $trabajador['email'];
$fechaContratacion = date('d F Y', strtotime($trabajador['fecha_contratacion']));

// ** Generar nuevo QR token cada vez que se carga la página **
$qrToken = bin2hex(random_bytes(16)); // Generar un token aleatorio de 32 caracteres
$updateTokenSql = "UPDATE trabajadores SET qr_token = '$qrToken' WHERE trabajador_id = '$id_trabajador'";
if (!mysqli_query($conn, $updateTokenSql)) {
    die("Error al actualizar QR token: " . mysqli_error($conn));
}

// Generar QR Code con el nuevo token
$options = new QROptions([
    'version' => 5,
    'eccLevel' => QRCode::ECC_L,
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'scale' => 5,
]);
$url = "https://energym.ddns.net/qr_verificacion.php?token={$qrToken}&trabajador_id={$id_trabajador}";
$qrcode = (new QRCode($options))->render($url);

// Consulta de última asistencia
$sqlUltimaAsistencia = "SELECT fecha_hora FROM asistencias WHERE usuario_id = '$id_trabajador' ORDER BY fecha_hora DESC LIMIT 1";
$resultUltimaAsistencia = mysqli_query($conn, $sqlUltimaAsistencia);
$ultimaAsistencia = mysqli_fetch_assoc($resultUltimaAsistencia)['fecha_hora'] ?? 'Sin asistencias registradas';

// Consulta de asistencias del mes actual
$mesActual = date('Y-m');
$sqlAsistenciasMes = "SELECT COUNT(*) AS total_asistencias FROM asistencias WHERE usuario_id = '$id_trabajador' AND DATE_FORMAT(fecha_hora, '%Y-%m') = '$mesActual'";
$resultAsistenciasMes = mysqli_query($conn, $sqlAsistenciasMes);
$totalAsistenciasMes = mysqli_fetch_assoc($resultAsistenciasMes)['total_asistencias'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bienvenido, <?php echo htmlspecialchars($nombreTrabajador); ?> | EnerGym</title>
  
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
    .btn-custom {
      background-color: rgb(15, 139, 141);
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 5px;
      text-decoration: none;
    }
    .btn-custom:hover {
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
              ¡Hola, <?php echo htmlspecialchars($nombreTrabajador); ?>!
            </h1>
            <p class="text-center">
              <strong>Rol:</strong> <?php echo htmlspecialchars($rol); ?><br>
              <strong>Fecha de Contratación:</strong> <?php echo $fechaContratacion; ?>
            </p>

            <p class="text-center">Escanea tu QR para la verificación:</p>
            <img src="<?php echo $qrcode; ?>" alt="QR Code" class="qr-img">

            <!-- Botones -->
            <div class="d-flex justify-content-center mt-4">
              <a href="trabajadorModificar.php" class="btn-custom me-2">Modificar datos</a>
              <a href="trabajadorCambiarPassword.php" class="btn-custom">Cambiar contraseña</a>
            </div>

            <!-- Cerrar sesión -->
            <form action="../logoutProcesar.php" method="post" class="text-center mt-4">
              <button class="btn btn-danger">Cerrar sesión</button>
            </form>
          </div>
        </div>

        <!-- COLUMNA LATERAL -->
        <div class="col-md-4">
          <!-- ADMIN PANEL BUTTON (VISIBLE FOR ADMIN ROLES) -->
          <?php if (strtolower($rol) === 'manager'): ?>
          <div class="card p-4 mb-4">
            <h5 class="mb-3 text-center">Acceso a Panel</h5>
            <a href="../admin/index.php" class="btn btn-warning w-100">Panel de Admin</a>
          </div>
          <?php endif; ?>

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
              ¡Mantente activo y sigue progresando en EnerGym!
            </p>
            <!-- Link al historial -->
            <a href="trabajadorHistorialAsistencias.php" class="btn btn-sm btn-info">
              Ver historial de asistencias
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php require '../partials/footer.view.php'; ?>
</body>
</html>
