<?php
include 'partials/db.php';

if (!isset($_GET['token'])) {
    header("Location: camara.php?status=error&message=Token no proporcionado.");
    exit();
}

$fullUrlToken = $_GET['token'];
$parsedUrl = parse_url($fullUrlToken);

if (isset($parsedUrl['query'])) {
    parse_str($parsedUrl['query'], $queryParams);
    $trabajadorId = $queryParams['trabajador_id'] ?? null;
    $clienteId = $queryParams['cliente_id'] ?? null;
    $entrenadorId = $queryParams['entrenador_id'] ?? null;
    $qrToken = $queryParams['token'] ?? null;
} else {
    header("Location: camara.php?status=error&message=Formato de token inválido.");
    exit();
}

// Función para registrar asistencia
function registrarAsistencia($conn, $usuarioId, $tipoUsuario, $qrToken, $tabla, $campoId) {
    $sql = "SELECT $campoId FROM $tabla WHERE $campoId = '$usuarioId' AND qr_token = '$qrToken' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $fechaHora = date("Y-m-d H:i:s");
        $sqlAsistencia = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo_usuario) VALUES ('$usuarioId', '$fechaHora', '$tipoUsuario')";
        mysqli_query($conn, $sqlAsistencia);

        $updateTokenSql = "UPDATE $tabla SET qr_token = NULL WHERE $campoId = '$usuarioId'";
        mysqli_query($conn, $updateTokenSql);

        header("Location: camara.php?status=success&message=Asistencia de $tipoUsuario registrada correctamente!");
        exit();
    } else {
        header("Location: camara.php?status=error&message=Token inválido o ya utilizado para $tipoUsuario.");
        exit();
    }
}

// Determinar el tipo de usuario y registrar asistencia
if ($trabajadorId && $qrToken) {
    registrarAsistencia($conn, $trabajadorId, 'trabajador', $qrToken, 'trabajadores', 'trabajador_id');
} elseif ($clienteId && $qrToken) {
    registrarAsistencia($conn, $clienteId, 'cliente', $qrToken, 'clientes', 'cliente_id');
} elseif ($entrenadorId && $qrToken) {
    registrarAsistencia($conn, $entrenadorId, 'entrenador', $qrToken, 'entrenadores', 'entrenador_id');
} else {
    header("Location: camara.php?status=error&message=No se especificó un tipo de usuario válido.");
    exit();
}
