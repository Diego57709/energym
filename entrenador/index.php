<?php 
require_once __DIR__ . '/../components/vendor//autoload.php';
include '../partials/db.php';
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || ($_SESSION['usuario'] !== 'entrenador')) {
    header("Location: /login.php");
    exit();
}
if (time() > $_SESSION['timeout']) {
  session_unset(); 
  session_destroy();
  header('Location: /login.html');
  exit();
}

$id_entrenador = $_SESSION['id'];
$sql = "SELECT * FROM entrenadores WHERE entrenador_id = '$id_entrenador'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 1) {
    $entrenador = mysqli_fetch_assoc($result);
}

$nombreTrabajador = $entrenador['apellidos'] . ', ' . $entrenador['nombre'];
$_SESSION['email'] = $entrenador['email'];
$fechaContratacion = date('d F Y', strtotime($entrenador['fecha_contratacion']));

$qrToken = bin2hex(random_bytes(16));
$updateTokenSql = "UPDATE entrenadores SET qr_token = '$qrToken' WHERE entrenador_id = '$id_entrenador'";
if (!mysqli_query($conn, $updateTokenSql)) {
    die("Error al actualizar QR token: " . mysqli_error($conn));
}

$options = new QROptions([
    'version' => 5,
    'eccLevel' => QRCode::ECC_L,
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'scale' => 5,
]);
$url = "https://energym.ddns.net/qr_verificacion.php?token={$qrToken}&entrenador_id={$id_entrenador}";
$qrcode = (new QRCode($options))->render($url);

$sqlUltimaAsistencia = "SELECT fecha_hora FROM asistencias WHERE usuario_id = '$id_entrenador' ORDER BY fecha_hora DESC LIMIT 1";
$resultUltimaAsistencia = mysqli_query($conn, $sqlUltimaAsistencia);
$ultimaAsistencia = mysqli_fetch_assoc($resultUltimaAsistencia)['fecha_hora'] ?? 'Sin asistencias registradas';

$mesActual = date('Y-m');
$sqlAsistenciasMes = "SELECT COUNT(*) AS total_asistencias FROM asistencias WHERE usuario_id = '$id_entrenador' AND DATE_FORMAT(fecha_hora, '%Y-%m') = '$mesActual'";
$resultAsistenciasMes = mysqli_query($conn, $sqlAsistenciasMes);
$totalAsistenciasMes = mysqli_fetch_assoc($resultAsistenciasMes)['total_asistencias'] ?? 0;

$sqlSiguienteClase = "SELECT fecha_hora_c FROM clases_grupales WHERE entrenador_id = '$id_entrenador' AND fecha_hora_c > NOW() ORDER BY fecha_hora_c ASC LIMIT 1";
$resultSiguienteClase = mysqli_query($conn, $sqlSiguienteClase);
if ($resultSiguienteClase && mysqli_num_rows($resultSiguienteClase) > 0) {
  $rowSiguienteClase = mysqli_fetch_assoc($resultSiguienteClase);
  $SiguienteClaseDate = strtotime($rowSiguienteClase['fecha_hora_c']);
} else {
  $SiguienteClaseDate = false;
}
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
              <strong>Fecha de Contratación:</strong> <?php echo $fechaContratacion; ?>
            </p>

            <p class="text-center">Escanea tu QR para la verificación:</p>
            <img src="<?php echo $qrcode; ?>" alt="QR Code" class="qr-img">

            <!-- Botones -->
            <div class="d-flex justify-content-center mt-4">
              <a href="entrenadorModificar.php" class="btn-custom me-2">Modificar datos</a>
              <a href="entrenadorCambiarPassword.php" class="btn-custom">Cambiar contraseña</a>
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
                      echo date('d F Y', $SiguienteClaseDate);
                  } else {
                      echo "No hay clases programadas";
                  }
                  ?>
              </p>
              <div class="d-flex justify-content-center">
                <a href="entrenadorClasesGrupales.php" class="btn btn-primary me-2">Clases grupales</a>
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
              ¡Mantente activo y sigue progresando en EnerGym!
            </p>
            <!-- Link al historial -->
            <a href="entrenadorHistorialAsistencias.php" class="btn btn-sm btn-info">
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
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</html>
