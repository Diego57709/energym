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
    $fechaHora = date("Y-m-d H:i:s");

    // Verificar si el usuario tiene un qr_token válido
    $sqlCheckToken = "SELECT qr_token FROM $tabla WHERE $campoId = ?";
    $stmtToken = mysqli_prepare($conn, $sqlCheckToken);
    mysqli_stmt_bind_param($stmtToken, "i", $usuarioId);
    mysqli_stmt_execute($stmtToken);
    $resultToken = mysqli_stmt_get_result($stmtToken);
    $rowToken = mysqli_fetch_assoc($resultToken);
    mysqli_stmt_close($stmtToken);

    if (!$rowToken || is_null($rowToken['qr_token'])) {
        // Si el usuario no tiene un QR Token válido, registrar error y salir
        $logEntry = "ERROR: Intento de escaneo inválido para usuario ID: $usuarioId el " . date("Y-m-d H:i:s") . PHP_EOL;
        file_put_contents("camara/log_asistencias.txt", $logEntry, FILE_APPEND);
        file_put_contents("camara/scan_status.txt", "pending");
        header("Location: camara.php?status=error&message=El código QR ya fue usado o no es válido.");
        exit();
    }

    // Obtener el nombre del usuario
    $sqlNombre = "SELECT nombre FROM $tabla WHERE $campoId = ?";
    $stmtNombre = mysqli_prepare($conn, $sqlNombre);
    mysqli_stmt_bind_param($stmtNombre, "i", $usuarioId);
    mysqli_stmt_execute($stmtNombre);
    $resultNombre = mysqli_stmt_get_result($stmtNombre);
    $nombreUsuario = ($row = mysqli_fetch_assoc($resultNombre)) ? $row['nombre'] : "Desconocido";
    mysqli_stmt_close($stmtNombre);

    // Verificar si es una entrada o salida
    $sqlCheckAnterior = "SELECT tipo FROM asistencias WHERE usuario_id = ? AND tipo_usuario = ? ORDER BY fecha_hora DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sqlCheckAnterior);
    mysqli_stmt_bind_param($stmt, "is", $usuarioId, $tipoUsuario);
    mysqli_stmt_execute($stmt);
    $resultCheckAnterior = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($resultCheckAnterior) === 1) {
        $row = mysqli_fetch_assoc($resultCheckAnterior);
        if ($row['tipo'] === 'ENTRADA') {
            // Registrar salida
            $sqlSalida = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo, tipo_usuario) VALUES (?, ?, 'SALIDA', ?)";
            $stmtSalida = mysqli_prepare($conn, $sqlSalida);
            mysqli_stmt_bind_param($stmtSalida, "iss", $usuarioId, $fechaHora, $tipoUsuario);
            mysqli_stmt_execute($stmtSalida);
            mysqli_stmt_close($stmtSalida);
            $updateTokenSql = "UPDATE $tabla SET qr_token = NULL WHERE $campoId = ?";
            $stmtUpdate = mysqli_prepare($conn, $updateTokenSql);
            mysqli_stmt_bind_param($stmtUpdate, "i", $usuarioId);
            mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);

            // Guardar log
            file_put_contents("camara/log_asistencias.txt", "Usuario: $nombreUsuario, Fecha: $fechaHora, Tipo: SALIDA" . PHP_EOL, FILE_APPEND);
            file_put_contents("camara/scan_status.txt", "pending");

            header("Location: camara.php?status=success&message=Salida registrada correctamente para $nombreUsuario.");
            exit();
        }
    }

    // Registrar entrada
    $sqlEntrada = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo, tipo_usuario) VALUES (?, ?, 'ENTRADA', ?)";
    $stmtEntrada = mysqli_prepare($conn, $sqlEntrada);
    mysqli_stmt_bind_param($stmtEntrada, "iss", $usuarioId, $fechaHora, $tipoUsuario);
    mysqli_stmt_execute($stmtEntrada);
    mysqli_stmt_close($stmtEntrada);

    // Actualizar token (eliminarlo después de uso)
    $updateTokenSql = "UPDATE $tabla SET qr_token = NULL WHERE $campoId = ?";
    $stmtUpdate = mysqli_prepare($conn, $updateTokenSql);
    mysqli_stmt_bind_param($stmtUpdate, "i", $usuarioId);
    mysqli_stmt_execute($stmtUpdate);
    mysqli_stmt_close($stmtUpdate);

    // Guardar log
    file_put_contents("camara/log_asistencias.txt", "Usuario: $nombreUsuario, Fecha: $fechaHora, Tipo: ENTRADA" . PHP_EOL, FILE_APPEND);
    file_put_contents("camara/scan_status.txt", "pending");

    header("Location: camara.php?status=success&message=Entrada registrada correctamente para $nombreUsuario.");
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
?>
