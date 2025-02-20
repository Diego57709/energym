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
    // Consulta preparada para revisar si existe una "ENTRADA" previa sin "SALIDA"
    $sqlCheckPrevious = "SELECT asistencia_id, tipo FROM asistencias WHERE usuario_id = ? AND tipo_usuario = ? ORDER BY fecha_hora DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sqlCheckPrevious);
    mysqli_stmt_bind_param($stmt, "is", $usuarioId, $tipoUsuario);
    mysqli_stmt_execute($stmt);
    $resultCheckPrevious = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $fechaHora = date("Y-m-d H:i:s");

    if (mysqli_num_rows($resultCheckPrevious) === 1) {
        $row = mysqli_fetch_assoc($resultCheckPrevious);
        if ($row['tipo'] === 'ENTRADA') {
            // Registrar "SALIDA" con sentencia preparada
            $sqlSalida = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo, tipo_usuario) VALUES (?, ?, 'SALIDA', ?)";
            $stmtSalida = mysqli_prepare($conn, $sqlSalida);
            mysqli_stmt_bind_param($stmtSalida, "iss", $usuarioId, $fechaHora, $tipoUsuario);
            mysqli_stmt_execute($stmtSalida);
            mysqli_stmt_close($stmtSalida);

            header("Location: camara.php?status=success&message=Salida registrada correctamente para $tipoUsuario.");
            exit();
        }
    }

    // Registrar "ENTRADA" con sentencia preparada
    $sqlEntrada = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo, tipo_usuario) VALUES (?, ?, 'ENTRADA', ?)";
    $stmtEntrada = mysqli_prepare($conn, $sqlEntrada);
    mysqli_stmt_bind_param($stmtEntrada, "iss", $usuarioId, $fechaHora, $tipoUsuario);
    mysqli_stmt_execute($stmtEntrada);
    mysqli_stmt_close($stmtEntrada);

    // Actualizar token: aquí $tabla y $campoId son variables dinámicas (no se pueden parametrizar)
    $updateTokenSql = "UPDATE $tabla SET qr_token = NULL WHERE $campoId = ?";
    $stmtUpdate = mysqli_prepare($conn, $updateTokenSql);
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
