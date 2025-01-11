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
    $qrToken = $queryParams['token'] ?? $fullUrlToken;
} else {
    $qrToken = $fullUrlToken;
}

$sql = "SELECT cliente_id FROM clientes WHERE qr_token = '$qrToken' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 1) {
    $cliente = mysqli_fetch_assoc($result);
    $clienteId = $cliente['cliente_id'];

    $fechaHora = date("Y-m-d H:i:s");
    $sqlAsistencia = "INSERT INTO asistencias (cliente_id, fecha_hora) VALUES ('$clienteId', '$fechaHora')";
    mysqli_query($conn, $sqlAsistencia);

    $updateTokenSql = "UPDATE clientes SET qr_token = NULL WHERE cliente_id = '$clienteId'";
    mysqli_query($conn, $updateTokenSql);

    header("Location: camara.php?status=success&message=Token utilizado correctamente!");
    exit();
} else {
    header("Location: camara.php?status=error&message=Código QR inválido o ya utilizado.");
    exit();
}
