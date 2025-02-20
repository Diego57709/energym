<?php
include 'partials/db.php';

if (!isset($_GET['token'])) {
    header("Location: camara.php?status=error&message=Token no proporcionado.");
    exit();
}

$qrToken      = $_GET['token'] ?? null;
$trabajadorId = $_GET['trabajador_id'] ?? null;
$clienteId    = $_GET['cliente_id'] ?? null;
$entrenadorId = $_GET['entrenador_id'] ?? null;

// Función para registrar asistencia
function registrarAsistencia($conn, $usuarioId, $tipoUsuario, $qrToken, $tabla, $campoId) {
    // Comprobar si existe una "ENTRADA" previa sin "SALIDA"
    $sqlCheckPrevious = "SELECT asistencia_id, tipo FROM asistencias WHERE usuario_id = ? AND tipo_usuario = ? ORDER BY fecha_hora DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sqlCheckPrevious);
    if (!$stmt) {
        header("Location: camara.php?status=error&message=Error en la consulta.");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "is", $usuarioId, $tipoUsuario);
    mysqli_stmt_execute($stmt);
    $resultCheckPrevious = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $fechaHora = date("Y-m-d H:i:s");

    if (mysqli_num_rows($resultCheckPrevious) === 1) {
        $row = mysqli_fetch_assoc($resultCheckPrevious);
        if ($row['tipo'] === 'ENTRADA') {
            // Registrar "SALIDA" usando sentencia preparada
            $sqlSalida = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo, tipo_usuario) VALUES (?, ?, 'SALIDA', ?)";
            $stmtSalida = mysqli_prepare($conn, $sqlSalida);
            if (!$stmtSalida) {
                header("Location: camara.php?status=error&message=Error en la consulta de salida.");
                exit();
            }
            mysqli_stmt_bind_param($stmtSalida, "iss", $usuarioId, $fechaHora, $tipoUsuario);
            mysqli_stmt_execute($stmtSalida);
            mysqli_stmt_close($stmtSalida);

            header("Location: camara.php?status=success&message=Salida registrada correctamente para $tipoUsuario.");
            exit();
        }
    }

    // Registrar "ENTRADA" usando sentencia preparada
    $sqlEntrada = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo, tipo_usuario) VALUES (?, ?, 'ENTRADA', ?)";
    $stmtEntrada = mysqli_prepare($conn, $sqlEntrada);
    if (!$stmtEntrada) {
        header("Location: camara.php?status=error&message=Error en la consulta de entrada.");
        exit();
    }
    mysqli_stmt_bind_param($stmtEntrada, "iss", $usuarioId, $fechaHora, $tipoUsuario);
    mysqli_stmt_execute($stmtEntrada);
    mysqli_stmt_close($stmtEntrada);

    // Actualizar estado del token con sentencia preparada
    $updateTokenSql = "UPDATE $tabla SET qr_token = NULL WHERE $campoId = ?";
    $stmtUpdate = mysqli_prepare($conn, $updateTokenSql);
    if (!$stmtUpdate) {
        header("Location: camara.php?status=error&message=Error en la consulta de actualización.");
        exit();
    }
    mysqli_stmt_bind_param($stmtUpdate, "i", $usuarioId);
    mysqli_stmt_execute($stmtUpdate);
    mysqli_stmt_close($stmtUpdate);

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
?>
