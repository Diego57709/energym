<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../partials/db.php';
session_start();

// Redirigir al login si no hay sesión iniciada
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$nombreUsuario = $_SESSION['usuario'];
$id_cliente = $_SESSION['id'];

// Procesar inscripción a clase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clase_id'])) {
    $clase_id = (int) $_POST['clase_id'];
    $fecha_inscripcion = date('Y-m-d H:i:s');

    // Insertar en la tabla clases_inscripciones
    $sqlInsert = "
        INSERT INTO clases_inscripciones (clase_id, cliente_id, fecha_inscripcion) 
        VALUES ('$clase_id', '$id_cliente', '$fecha_inscripcion')
    ";
    mysqli_query($conn, $sqlInsert);
}

// Base de la consulta para listar clases
$sqlClases = "
    SELECT 
        cg.clase_id,
        cg.nombre_clase,
        cg.capacidad,
        cg.fecha_hora_c,
        cg.fecha_hora_f,
        e.entrenador_id,
        e.nombre AS nombre_entrenador
    FROM clases_grupales cg
    JOIN entrenadores e ON cg.entrenador_id = e.entrenador_id;
";

$resultClases = mysqli_query($conn, $sqlClases);
$clases = mysqli_fetch_all($resultClases, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Todos los Pagos | EnerGym</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
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
        .table-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .table th, .table td {
            text-align: center;
        }
        .btn-back {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <?php require '../partials/header1.view.php'; ?>
    <div class="main">
        <div class="container my-5">
            <div class="table-container">
                <h1 class="text-center mb-4">Todos tus Clases</h1>
                <!-- Tabla de clases -->
                <?php if (count($clases) > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Clase</th>
                                <th>Entrenador</th>
                                <th>Fecha de comienzo</th>
                                <th>Fecha de fin</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clases as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                                    <td><?= htmlspecialchars($clase['nombre_entrenador']) ?></td>
                                    <td><?= date('d F Y, h:i A', strtotime($clase['fecha_hora_c'])) ?></td>
                                    <td><?= date('d F Y, h:i A', strtotime($clase['fecha_hora_f'])) ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="clase_id" value="<?= $clase['clase_id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Apuntarse</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-muted">No hay clases registradas.</p>
                <?php endif; ?>

                <!-- Botón de regreso -->
                <div class="d-flex justify-content-center mt-4">
                    <a href="index.php" class="btn btn-danger btn-back">Volver</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php require '../partials/footer.view.php'; ?>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    ></script>
</body>
</html>
