<?php
// Start session and check if user is a Manager
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Manager') {
    header('Location: ../index.php?error=permisos_insuficientes');
    exit();
}

// Include timeout and database connection
require '../partials/timeout.php';
include '../partials/db.php';

// Filters
$tipoFilter = $_GET['tipo'] ?? 'todos';
$tipoUsuarioFilter = $_GET['tipo_usuario'] ?? 'todos';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

// Base query with strict JOINs to ensure correct table based on tipo_usuario
$queryAsistencias = "
    SELECT 
        a.asistencia_id, 
        a.usuario_id, 
        a.fecha_hora, 
        a.tipo, 
        a.tipo_usuario, 
        CASE 
            WHEN a.tipo_usuario = 'cliente' AND c.cliente_id IS NOT NULL THEN c.nombre
            WHEN a.tipo_usuario = 'trabajador' AND t.trabajador_id IS NOT NULL THEN t.nombre
            WHEN a.tipo_usuario = 'entrenador' AND e.entrenador_id IS NOT NULL THEN e.nombre
            ELSE 'Desconocido'
        END AS usuario_nombre
    FROM asistencias a
    LEFT JOIN clientes c ON a.tipo_usuario = 'cliente' AND a.usuario_id = c.cliente_id
    LEFT JOIN trabajadores t ON a.tipo_usuario = 'trabajador' AND a.usuario_id = t.trabajador_id
    LEFT JOIN entrenadores e ON a.tipo_usuario = 'entrenador' AND a.usuario_id = e.entrenador_id
    WHERE 1=1
";

// Apply filters
if ($tipoFilter !== 'todos') {
    $queryAsistencias .= " AND a.tipo = '" . mysqli_real_escape_string($conn, $tipoFilter) . "'";
}

if ($tipoUsuarioFilter !== 'todos') {
    $queryAsistencias .= " AND a.tipo_usuario = '" . mysqli_real_escape_string($conn, $tipoUsuarioFilter) . "'";
}

if (!empty($startDate) && !empty($endDate)) {
    $queryAsistencias .= " AND DATE(a.fecha_hora) BETWEEN '" . mysqli_real_escape_string($conn, $startDate) . "' AND '" . mysqli_real_escape_string($conn, $endDate) . "'";
}

$queryAsistencias .= " ORDER BY a.fecha_hora DESC";

$result = mysqli_query($conn, $queryAsistencias);

if (!$result) {
    die("Error in query: " . mysqli_error($conn));
}

$asistencias = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Asistencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            height: 100%;
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
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="ver_usuarios.php"><i class="fas fa-users"></i> Ver Usuarios</a>
            <a href="ver_pagos.php"><i class="fas fa-credit-card"></i> Ver pagos</a>
            <a href="ver_asistencias.php" class="active-link"><i class="fas fa-door-open"></i> Ver asistencias</a>
            <a href="mails_enviar.php"><i class="fas fa-envelope"></i> Enviar correos</a>
            <a href="ver_clases.php"><i class="fas fa-bicycle"></i> Ver clases</a>
            <a href="/trabajador/"><i class="fas fa-sign-out-alt"></i> Salir</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Main content -->
        <div class="flex-grow-1">
            <div class="main-content">
                <h1 class="mb-4">Asistencias Registradas</h1>

                <!-- Filtro de tipo, fecha y tipo_usuario -->
                <form method="GET" class="filter-form row">
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">Tipo:</label>
                        <select name="tipo" id="tipo" class="form-select form-select-sm">
                            <option value="todos" <?= $tipoFilter === 'todos' ? 'selected' : '' ?>>Todos</option>
                            <option value="ENTRADA" <?= $tipoFilter === 'ENTRADA' ? 'selected' : '' ?>>ENTRADA</option>
                            <option value="SALIDA" <?= $tipoFilter === 'SALIDA' ? 'selected' : '' ?>>SALIDA</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_usuario" class="form-label">Tipo de Usuario:</label>
                        <select name="tipo_usuario" id="tipo_usuario" class="form-select form-select-sm">
                            <option value="todos" <?= $tipoUsuarioFilter === 'todos' ? 'selected' : '' ?>>Todos</option>
                            <option value="cliente" <?= $tipoUsuarioFilter === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                            <option value="trabajador" <?= $tipoUsuarioFilter === 'trabajador' ? 'selected' : '' ?>>Trabajador</option>
                            <option value="entrenador" <?= $tipoUsuarioFilter === 'entrenador' ? 'selected' : '' ?>>Entrenador</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Desde:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="<?= htmlspecialchars($startDate) ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Hasta:</label>
                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="<?= htmlspecialchars($endDate) ?>">
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                        <a href="ver_asistencias.php" class="btn btn-secondary btn-sm">Quitar Filtro</a>
                    </div>
                </form>

                <!-- Tabla de asistencias -->
                <div class="table-responsive">
                    <?php if (count($asistencias) > 0): ?>
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Usuario ID</th>
                                    <th>Fecha y Hora</th>
                                    <th>Tipo</th>
                                    <th>Tipo de Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asistencias as $asistencia): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($asistencia['asistencia_id']) ?></td>
                                        <td><?= htmlspecialchars($asistencia['usuario_nombre']) ?></td>
                                        <td><?= htmlspecialchars($asistencia['usuario_id']) ?></td>
                                        <td><?= htmlspecialchars($asistencia['fecha_hora']) ?></td>
                                        <td><?= htmlspecialchars($asistencia['tipo']) ?></td>
                                        <td><?= htmlspecialchars($asistencia['tipo_usuario']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center text-muted">No hay asistencias que coincidan con los filtros seleccionados.</p>
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
