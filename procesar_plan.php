<?php
require 'partials/header2.view.php';
include 'partials/db.php';

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: 404.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_REQUEST['dni'];
    $plan = $_REQUEST['plan'];
    $extras = $_REQUEST['extrasSelected'];
    $nombre = $_REQUEST['nombre'];
    $email = $_REQUEST['email'];
    $telefono = $_REQUEST['telefono'];
    $direccion = $_REQUEST['direccion'];
    $codigo_postal = $_REQUEST['codigo_postal'];
    $fecha_nacimiento = $_REQUEST['fecha_nacimiento'];
    $genero = $_REQUEST['genero'];
    $metodo_pago = $_REQUEST['metodo_pago'];

    // Map plan name to its corresponding ID
    if ($plan === "Comfort") {
        $plan_id = 1;
    } elseif ($plan === "Premium") {
        $plan_id = 2;
    }

    $start_sub = date("Y-m-d");
    $end_sub = date("Y-m-d", strtotime("+30 days"));
    $created_at = date("Y-m-d");

    // Insert query
    $sql = "INSERT INTO 
            clientes (dni, plan, extrasSelected, nombre, email, telefono, direccion, codigo_postal, fecha_nacimiento, genero, metodo_pago, start_sub, end_sub, created_at) 
            VALUES ('$dni', '$plan_id', '$extras', '$nombre', '$email', '$telefono', '$direccion', '$codigo_postal', '$fecha_nacimiento', '$genero', '$metodo_pago', '$start_sub', '$end_sub', '$created_at')";

    // Execute query
    $result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .inner-text {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="main">
    <div class="container text-center">
        <h2 class="mb-4">Registro</h2>
        <?php if ($result): ?>
            <div class="alert alert-success" role="alert">
                <h3 class="inner-text">Usuario creado correctamente.</h3>
                <p class="mt-3">Volviendo al inicio de sesión...</p>
            </div>
            <?php header("Refresh: 3; URL=login.php"); ?>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                <h3 class="inner-text">Error: Usuario no creado.</h3>
                <p class="mt-3">Volviendo a planes...</p>
            </div>
            <?php header("Refresh: 3; URL=planes.php"); ?>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
<?php
}
require 'partials/footer.view.php';
?>
