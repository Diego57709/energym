<?php 
declare(strict_types=1);
include '../partials/db.php';
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || ($_SESSION['usuario'] !== 'entrenador')) {
    header("Location: /login.php");
    exit();
}
if (time() > $_SESSION['timeout']) {
  session_unset(); 
  session_destroy();
  header('Location: /login.html');
  exit();
}

$usuario = $_SESSION['usuario'];
$id_entrenador = $_SESSION['id'];

$sqlClasesFuturas = "
    SELECT 
        cg.clase_id,
        cg.nombre_clase,
        cg.capacidad,
        cg.fecha_hora_c,
        cg.fecha_hora_f,
        (SELECT COUNT(*) FROM clases_inscripciones ci WHERE ci.clase_id = cg.clase_id) AS inscritos
    FROM clases_grupales cg
    WHERE cg.entrenador_id = '$id_entrenador'
    AND DATE(cg.fecha_hora_c) >= CURDATE()
    ORDER BY cg.fecha_hora_c ASC
";
$resultadoClasesFuturas = mysqli_query($conn, $sqlClasesFuturas);
$clasesFuturas = mysqli_fetch_all($resultadoClasesFuturas, MYSQLI_ASSOC);

$sqlClasesPasadas = "
    SELECT 
        cg.clase_id,
        cg.nombre_clase,
        cg.capacidad,
        cg.fecha_hora_c,
        cg.fecha_hora_f,
        (SELECT COUNT(*) FROM clases_inscripciones ci WHERE ci.clase_id = cg.clase_id) AS inscritos
    FROM clases_grupales cg
    WHERE cg.entrenador_id = '$id_entrenador'
    AND DATE(cg.fecha_hora_c) < CURDATE()
    ORDER BY cg.fecha_hora_c DESC
";
$resultadoClasesPasadas = mysqli_query($conn, $sqlClasesPasadas);
$clasesPasadas = mysqli_fetch_all($resultadoClasesPasadas, MYSQLI_ASSOC);
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
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Encabezado -->
    <?php require '../partials/header1.view.php'; ?>

    <!-- Sección de contenido -->
    <div class="content">
        <div class="table-container">
            <h2 class="text-center mb-4">Mis Clases Grupales</h2>
            
            <!-- Clases futuras del entrenador -->
            <h2 class="text-center mt-4">Próximas Clases</h2>
            <?php if (count($clasesFuturas) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Clase</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Capacidad</th>
                                <th>Inscritos</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clasesFuturas as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_c'])) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_f'])) ?></td>
                                    <td><?= $clase['capacidad'] ?></td>
                                    <td><?= $clase['inscritos'] ?></td>
                                    <td>
                                        <a href="ver_inscritos.php?clase_id=<?= $clase['clase_id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Ver Inscritos
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No tienes clases programadas.</p>
            <?php endif; ?>

            <!-- Clases pasadas del entrenador -->
            <h2 class="text-center mt-5">Clases Pasadas</h2>
            <?php if (count($clasesPasadas) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Clase</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Capacidad</th>
                                <th>Inscritos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clasesPasadas as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_c'])) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_f'])) ?></td>
                                    <td><?= $clase['capacidad'] ?></td>
                                    <td><?= $clase['inscritos'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No tienes clases pasadas registradas.</p>
            <?php endif; ?>

            <!-- Botón para volver -->
            <div class="d-flex justify-content-center mt-4">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Pie de página -->
    <?php require '../partials/footer.view.php'; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>
