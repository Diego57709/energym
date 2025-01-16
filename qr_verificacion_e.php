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
    $qrToken = $queryParams['token'] ?? null;
} else {
    header("Location: camara.php?status=error&message=Formato de token inválido.");
    exit();
}

if ($trabajadorId) {
    // Procesar como trabajador
    $sql = "SELECT trabajador_id FROM trabajadores WHERE trabajador_id = '$trabajadorId' AND qr_token = '$qrToken' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $fechaHora = date("Y-m-d H:i:s");
        $sqlAsistencia = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo_usuario) VALUES ('$trabajadorId', '$fechaHora', 'trabajador')";
        mysqli_query($conn, $sqlAsistencia);

        $updateTokenSql = "UPDATE trabajadores SET qr_token = NULL WHERE trabajador_id = '$trabajadorId'";
        mysqli_query($conn, $updateTokenSql);

        header("Location: camara.php?status=success&message=Asistencia de trabajador registrada correctamente!");
        exit();
    } else {
        header("Location: camara.php?status=error&message=Token inválido o ya utilizado para trabajador.");
        exit();
    }
} elseif ($clienteId) {
    // Procesar como cliente
    $sql = "SELECT cliente_id FROM clientes WHERE cliente_id = '$clienteId' AND qr_token = '$qrToken' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $fechaHora = date("Y-m-d H:i:s");
        $sqlAsistencia = "INSERT INTO asistencias (usuario_id, fecha_hora, tipo_usuario) VALUES ('$clienteId', '$fechaHora', 'cliente')";
        mysqli_query($conn, $sqlAsistencia);

        $updateTokenSql = "UPDATE clientes SET qr_token = NULL WHERE cliente_id = '$clienteId'";
        mysqli_query($conn, $updateTokenSql);

        header("Location: camara.php?status=success&message=Asistencia de cliente registrada correctamente!");
        exit();
    } else {
        header("Location: camara.php?status=error&message=Token inválido o ya utilizado para cliente.");
        exit();
    }
} else {
    header("Location: camara.php?status=error&message=No se especificó un tipo de usuario válido.");
    exit();
}
