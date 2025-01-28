<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Manager') {
    header('Location: ../index.php?error=permisos_insuficientes');
    exit();
}

require '../partials/timeout.php';
include '../partials/db.php';

$queryPlantillas = "
    SELECT 
        plantilla_id, 
        nombre,
        capacidad,
        duracion
    FROM plantilla_clases
";
$resultP = mysqli_query($conn, $queryPlantillas);

$queryEntrenador = "
    SELECT 
        entrenador_id,
        nombre
    FROM entrenadores
";
$resultE = mysqli_query($conn, $queryEntrenador);

// Filtro para las clases existentes
$startDate = $_GET['start_date'] ?? '';
$endDate   = $_GET['end_date']   ?? '';

$queryClases = "
    SELECT 
        clase_id, 
        nombre_clase, 
        capacidad,
        entrenador_id, 
        fecha_hora_c,
        fecha_hora_f
    FROM clases_grupales
    WHERE 1=1
";

if (!empty($startDate) && !empty($endDate)) {
    $queryClases .= " AND DATE(fecha_hora_c) BETWEEN '$startDate' AND '$endDate'";
}

$queryClases .= " ORDER BY fecha_hora_c DESC";

$resultC = mysqli_query($conn, $queryClases);
if (!$resultC) {
    die("Error in query: " . mysqli_error($conn));
}

$clases = mysqli_fetch_all($resultC, MYSQLI_ASSOC);

// Añadir una nueva clase
$add_startDate = $_GET['add_start_date']  ?? null;
$entrenador_id = $_GET['entrenador_id']   ?? null; 
$clase_id      = $_GET['clase_id']        ?? null; 

if ($add_startDate) {
    $fechaHora = str_replace('T', ' ', $add_startDate) . ':00';
} else {
    $fechaHora = null;
}

if (!empty($fechaHora) && !empty($entrenador_id) && $entrenador_id != 0 && !empty($clase_id) && $clase_id != 0) {
    $queryPlantillaSelec = "SELECT * FROM plantilla_clases WHERE plantilla_id = '$clase_id' LIMIT 1";
    $resPlantilla = mysqli_query($conn, $queryPlantillaSelec);

    if ($resPlantilla && mysqli_num_rows($resPlantilla) > 0) {
        $rowPlantilla = mysqli_fetch_assoc($resPlantilla);

        $nombreClase = $rowPlantilla['nombre'];
        $capacidad   = $rowPlantilla['capacidad'];
        $duracion    = $rowPlantilla['duracion']; // minutes

        $fechaHoraFin = date('Y-m-d H:i:s', strtotime($fechaHora . " + $duracion minutes"));

        $sqlInsert = "
            INSERT INTO clases_grupales (
                nombre_clase, 
                capacidad, 
                entrenador_id, 
                fecha_hora_c,
                fecha_hora_f
            )
            VALUES (
                '$nombreClase', 
                '$capacidad', 
                '$entrenador_id', 
                '$fechaHora', 
                '$fechaHoraFin'
            )
        ";

        $insertResult = mysqli_query($conn, $sqlInsert);
        if (!$insertResult) {
            die("Error inserting class: " . mysqli_error($conn));
        }
    
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clases Grupales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link 
        rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    >
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 40px;
            background-color: #f8f9fa;
            height:100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .active-link {
            background-color: #0d6efd;
            color: white !important;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        footer {
            text-align: center;
            padding: 15px;
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar and content -->
    <div class="d-flex flex-grow-1">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">Panel de Control</div>
            <hr>
            <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="ver_usuarios.php"><i class="fas fa-users"></i> Ver Usuarios </a> 
            <a href="ver_pagos.php"><i class="fas fa-credit-card"></i> Ver pagos </a>
            <a href="ver_asistencias.php"><i class="fas fa-door-open"></i> Ver asistencias</a>
            <a href="mails_enviar.php"><i class="fas fa-envelope"></i> Enviar correos</a>
            <a href="ver_clases.php" class="active-link"><i class="fas fa-bicycle"></i> Ver clases</a>
            <a href="/trabajador/"><i class="fas fa-sign-out-alt"></i> Salir</a>
            <a href="../logoutProcesar.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Main content -->
        <div class="flex-grow-1">
            <div class="main-content">
                <h1 class="mb-4">Clases Grupales</h1>
                <!-- Formulario para Agregar Clase -->
                <form method="GET" class="filter-form row">
                    <div class="col-md-2">
                        <label for="clase_id" class="form-label">Clase:</label>
                        <select name="clase_id" id="clase_id" class="form-select form-select-sm">
                            <option value="0">Elige una clase</option>
                            <?php
                            // Since we've already fetched $resultP once, we either:
                            // 1) run it again, OR
                            // 2) store results in an array from the beginning 
                            // For simplicity, let's run again quickly (or store above).
                            
                            // Reset pointer or re-run the query if needed:
                            mysqli_data_seek($resultP, 0);
                            while ($row = mysqli_fetch_assoc($resultP)): 
                            ?>
                                <option value="<?= htmlspecialchars($row['plantilla_id']) ?>">
                                    <?= htmlspecialchars($row['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="add_start_date" class="form-label">Día y hora:</label>
                        <input 
                            type="datetime-local" 
                            name="add_start_date" 
                            id="add_start_date" 
                            class="form-control form-control-sm" 
                            value=""
                        >
                    </div>
                    <div class="col-md-3">
                        <label for="entrenador_id" class="form-label">Entrenador:</label>
                        <select name="entrenador_id" id="entrenador_id" class="form-select form-select-sm">
                            <option value="0">Elige un entrenador</option>
                            <?php
                            // Reset pointer or re-run for entrenadores too:
                            mysqli_data_seek($resultE, 0);
                            while ($rowE = mysqli_fetch_assoc($resultE)): 
                            ?>
                                <option value="<?= htmlspecialchars($rowE['entrenador_id']) ?>">
                                    <?= htmlspecialchars($rowE['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Guardar Clase
                        </button>
                    </div>
                </form>

                <!-- Filtro de fecha (Listar / Ver) -->
                <form method="GET" class="filter-form row">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Desde:</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            id="start_date" 
                            class="form-control form-control-sm" 
                            value="<?= htmlspecialchars($startDate) ?>"
                        >
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Hasta:</label>
                        <input 
                            type="date" 
                            name="end_date" 
                            id="end_date" 
                            class="form-control form-control-sm" 
                            value="<?= htmlspecialchars($endDate) ?>"
                        >
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                        <a href="ver_clases.php" class="btn btn-secondary btn-sm">Quitar Filtro</a>
                    </div>
                </form>

                <!-- Tabla de clases grupales -->
                <div class="table-responsive mt-4">
                    <?php if (count($clases) > 0): ?>
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Clase ID</th>
                                    <th>Nombre de Clase</th>
                                    <th>Cupo Máximo</th>
                                    <th>Entrenador ID</th>
                                    <th>Fecha y Hora Inicio</th>
                                    <th>Fecha y Hora Fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clases as $clase): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($clase['clase_id']) ?></td>
                                        <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                                        <td><?= htmlspecialchars($clase['capacidad']) ?></td>
                                        <td><?= htmlspecialchars($clase['entrenador_id']) ?></td>
                                        <td><?= htmlspecialchars($clase['fecha_hora_c']) ?></td>
                                        <td><?= htmlspecialchars($clase['fecha_hora_f']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center text-muted">No hay clases para mostrar en este rango de fechas.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 EnerGym Admin Dashboard. Todos los derechos reservados.</p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
