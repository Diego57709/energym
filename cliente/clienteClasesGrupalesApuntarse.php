<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../partials/db.php';
session_start();

// Redirigir al login si el usuario no está autenticado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id']) || ($_SESSION['usuario'] !== 'cliente')) {
    header("Location: login.php");
    exit;
}

$nombreUsuario = $_SESSION['usuario'];
$id_cliente = $_SESSION['id'];

// Si se detecta un parámetro 'clase_id' en la URL, procesamos la inscripción
if (isset($_GET['clase_id']) && !empty($_GET['clase_id'])) {
    $clase_id = intval($_GET['clase_id']);
    
    // Comprobar si el usuario ya está inscrito en esta clase (consulta sin prepared statement)
    $sqlCheck = "SELECT * FROM clases_inscripciones WHERE cliente_id = $id_cliente AND clase_id = $clase_id";
    $resultCheck = mysqli_query($conn, $sqlCheck);
    if (!$resultCheck) {
        $_SESSION['mensaje'] = "Error al verificar inscripción: " . mysqli_error($conn);
        header("Location: clienteClasesGrupalesApuntarse.php");
        exit;
    }
    if (mysqli_num_rows($resultCheck) > 0) {
        $_SESSION['mensaje'] = "Ya estás inscrito en esta clase.";
        header("Location: clienteClasesGrupalesApuntarse.php");
        exit;
    }
    
    // Insertar la inscripción usando un prepared statement
    $sqlInsert = "INSERT INTO clases_inscripciones (cliente_id, clase_id, fecha_inscripcion) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sqlInsert);
    if (!$stmt) {
        $_SESSION['mensaje'] = "Error al preparar la inserción: " . $conn->error;
        header("Location: clienteClasesGrupalesApuntarse.php");
        exit;
    }
    $stmt->bind_param("ii", $id_cliente, $clase_id);
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Inscripción exitosa.";
    } else {
        $_SESSION['mensaje'] = "Error al inscribirse, inténtalo de nuevo.";
    }
    $stmt->close();
    
    // Redirigimos para eliminar el parámetro de la URL y evitar reinserciones en refrescos
    header("Location: clienteClasesGrupalesApuntarse.php");
    exit;
}

// Obtener las clases de hoy
$sqlClasesHoy = "
    SELECT 
        cg.clase_id,
        cg.nombre_clase,
        cg.capacidad,
        cg.fecha_hora_c,
        cg.fecha_hora_f,
        e.nombre AS nombre_entrenador
    FROM clases_grupales cg
    JOIN entrenadores e ON cg.entrenador_id = e.entrenador_id
    WHERE DATE(cg.fecha_hora_c) = CURDATE()
    ORDER BY cg.fecha_hora_c ASC
";
$resultClasesHoy = mysqli_query($conn, $sqlClasesHoy);
$clasesHoy = mysqli_fetch_all($resultClasesHoy, MYSQLI_ASSOC);

// Obtener las clases próximas (con fecha mayor a hoy)
$sqlClasesProximas = "
    SELECT 
        cg.clase_id,
        cg.nombre_clase,
        cg.capacidad,
        cg.fecha_hora_c,
        cg.fecha_hora_f,
        e.nombre AS nombre_entrenador
    FROM clases_grupales cg
    JOIN entrenadores e ON cg.entrenador_id = e.entrenador_id
    WHERE DATE(cg.fecha_hora_c) > CURDATE()
    ORDER BY cg.fecha_hora_c ASC
";
$resultClasesProximas = mysqli_query($conn, $sqlClasesProximas);
$clasesProximas = mysqli_fetch_all($resultClasesProximas, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clases Grupales | EnerGym</title>
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
    </style>
</head>
<body>

<div class="main-container">
    <!-- Header -->
    <?php require '../partials/header1.view.php'; ?>

    <!-- Sección de Contenido -->
    <div class="content">
        <div class="table-container">
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-info">
                    <?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Clases de Hoy -->
            <h2 class="text-center mt-4">Clases de Hoy</h2>
            <?php if (count($clasesHoy) > 0): ?>
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
                            <?php foreach ($clasesHoy as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                                    <td><?= htmlspecialchars($clase['nombre_entrenador']) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_c'])) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_f'])) ?></td>
                                    <td>
                                        <a href="clienteClasesGrupalesApuntarse.php?clase_id=<?= $clase['clase_id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Apuntarse
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No hay clases programadas para hoy.</p>
            <?php endif; ?>

            <!-- Clases Próximas -->
            <h2 class="text-center mt-5">Próximas Clases</h2>
            <?php if (count($clasesProximas) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
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
                            <?php foreach ($clasesProximas as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                                    <td><?= htmlspecialchars($clase['nombre_entrenador']) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_c'])) ?></td>
                                    <td><?= date('d/m/Y, h:i A', strtotime($clase['fecha_hora_f'])) ?></td>
                                    <td>
                                        <a href="clienteClasesGrupalesApuntarse.php?clase_id=<?= $clase['clase_id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Apuntarse
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No hay clases próximas disponibles.</p>
            <?php endif; ?>

            <!-- Botón de regreso -->
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
<!-- FontAwesome para íconos -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>
