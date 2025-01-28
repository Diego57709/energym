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
    // Check if there's a previous "ENTRADA" without a corresponding "SALIDA"
    $sqlCheckPrevious = "SELECT asistencia_id, tipo FROM asistencias WHERE usuario_id = '$usuarioId' AND tipo_usuario = '$tipoUsuario' ORDER BY fecha_hora DESC LIMIT 1";
    $resultCheckPrevious = mysqli_query($conn, $sqlCheckPrevious);

    $fechaHora = date("Y-m-d H:i:s");

    if (mysqli_num_rows($resultCheckPrevious) === 1) {
        $row = mysqli_fetch_assoc($resultCheckPrevious);

        if ($row['tipo'] === 'ENTRADA') {
            // Register "SALIDA"
            $sqlSalida = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo, tipo_usuario) VALUES ('$usuarioId', '$fechaHora', 'SALIDA', '$tipoUsuario')";
            mysqli_query($conn, $sqlSalida);

            header("Location: camara.php?status=success&message=Salida registrada correctamente para $tipoUsuario.");
            exit();
        }
    }

    // If no previous "ENTRADA" or last record was "SALIDA", register new "ENTRADA"
    $sqlEntrada = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo, tipo_usuario) VALUES ('$usuarioId', '$fechaHora', 'ENTRADA', '$tipoUsuario')";
    mysqli_query($conn, $sqlEntrada);

    // Update token status
    $updateTokenSql = "UPDATE $tabla SET qr_token = NULL WHERE $campoId = '$usuarioId'";
    mysqli_query($conn, $updateTokenSql);

    header("Location: camara.php?status=success&message=Entrada registrada correctamente para $tipoUsuario.");
    exit();
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
