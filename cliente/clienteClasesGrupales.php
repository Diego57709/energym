<?php
declare(strict_types=1);
include '../partials/db.php';
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$nombreUsuario = $_SESSION['usuario'];
$id_cliente = $_SESSION['id'];

// Handle "Desapuntarse" action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'desapuntarse') {
    $clase_id = (int)$_POST['clase_id'];
    $sqlDelete = "DELETE FROM clases_inscripciones WHERE clase_id = '$clase_id' AND cliente_id = '$id_cliente'";
    mysqli_query($conn, $sqlDelete);
}

// Query for current and future classes
$sqlUpcomingClasses = "
    SELECT 
        cg.clase_id,
        cg.nombre_clase,
        cg.fecha_hora_c,
        cg.fecha_hora_f,
        e.nombre AS nombre_entrenador
    FROM clases_inscripciones ci
    JOIN clases_grupales cg ON ci.clase_id = cg.clase_id
    JOIN entrenadores e ON cg.entrenador_id = e.entrenador_id
    WHERE ci.cliente_id = '$id_cliente' 
    AND DATE(cg.fecha_hora_c) >= CURDATE()
    ORDER BY cg.fecha_hora_c ASC
";

$resultUpcoming = mysqli_query($conn, $sqlUpcomingClasses);
$upcomingClasses = mysqli_fetch_all($resultUpcoming, MYSQLI_ASSOC);

// Query for past classes
$sqlPastClasses = "
    SELECT 
        cg.clase_id,
        cg.nombre_clase,
        cg.fecha_hora_c,
        cg.fecha_hora_f,
        e.nombre AS nombre_entrenador
    FROM clases_inscripciones ci
    JOIN clases_grupales cg ON ci.clase_id = cg.clase_id
    JOIN entrenadores e ON cg.entrenador_id = e.entrenador_id
    WHERE ci.cliente_id = '$id_cliente' 
    AND DATE(cg.fecha_hora_c) < CURDATE()
    ORDER BY cg.fecha_hora_c DESC
";

$resultPast = mysqli_query($conn, $sqlPastClasses);
$pastClasses = mysqli_fetch_all($resultPast, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Clases | EnerGym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .main-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .table-container {
            width: 100%;
            max-width: 900px;
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #0d6efd;
            color: white;
            text-align: center;
        }
        .table td {
            text-align: center;
            vertical-align: middle;
        }
        .btn {
            transition: all 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Header -->
    <?php require '../partials/header1.view.php'; ?>

    <!-- Content Section -->
    <div class="content">
        <div class="table-container">
            <h2 class="text-center mb-4">Clases Grupales</h2>
            <!-- Botón para inscribirse en clases grupales -->
            <div class="d-flex justify-content-center mt-4">
                <a href="clienteClasesGrupalesApuntarse.php" class="btn btn-success">
                    <i class="fas fa-calendar-plus"></i> Apuntarse a Clases Grupales
                </a>
            </div>
            <!-- Current and Future Classes -->
            <h2 class="text-center mt-4">Próximas Clases</h2>
            <?php if (count($upcomingClasses) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Clase</th>
                                <th>Entrenador</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingClasses as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                                    <td><?= htmlspecialchars($clase['nombre_entrenador']) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_c'])) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_f'])) ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="clase_id" value="<?= $clase['clase_id'] ?>">
                                            <input type="hidden" name="action" value="desapuntarse">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Desapuntarse
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No tienes clases programadas.</p>
            <?php endif; ?>

            <!-- Past Classes -->
            <h2 class="text-center mt-5">Clases Pasadas</h2>
            <?php if (count($pastClasses) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Clase</th>
                                <th>Entrenador</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pastClasses as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                                    <td><?= htmlspecialchars($clase['nombre_entrenador']) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_c'])) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_f'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No tienes clases pasadas registradas.</p>
            <?php endif; ?>

            <!-- Back button -->
            <div class="d-flex justify-content-center mt-4">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require '../partials/footer.view.php'; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>
