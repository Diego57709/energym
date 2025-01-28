<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Manager') {
    header('Location: ../index.php?error=permisos_insuficientes');
    exit();
}

require '../partials/db.php';

// Filtros de fecha
$fechaFilter1 = $_GET['fecha1'] ?? '';
$fechaFilter2 = $_GET['fecha2'] ?? '';

// Validar las fechas
if (!empty($fechaFilter1) && !empty($fechaFilter2)) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFilter1) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFilter2)) {
        die("Fechas inválidas. Verifique el formato.");
    }
}

$queryPagos = "
    SELECT hp.metodo_pago, hp.fecha_pago, hp.total, hp.recurrente, c.nombre
    FROM historial_pagos hp
    JOIN clientes c ON c.cliente_id = hp.cliente_id
    WHERE 1 = 1
";

if (!empty($fechaFilter1) && !empty($fechaFilter2)) {
    $queryPagos .= " AND hp.fecha_pago BETWEEN '$fechaFilter1' AND '$fechaFilter2'";
}

$queryPagos .= " ORDER BY fecha_pago DESC";

$result = mysqli_query($conn, $queryPagos);
if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}

$pagos = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
    <div class="d-flex flex-grow-1">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">Panel de Control</div>
            <hr>
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="ver_usuarios.php"><i class="fas fa-users"></i> Ver Usuarios </a> 
            <a href="ver_pagos.php" class="active-link"><i class="fas fa-credit-card"></i> Ver pagos </a>
            <a href="ver_asistencias.php"><i class="fas fa-door-open"></i> Ver asistencias</a>
            <a href="mails_enviar.php"><i class="fas fa-envelope"></i> Enviar correos</a>
            <a href="ver_clases.php"><i class="fas fa-bicycle"></i> Ver clases</a>
            <a href="/trabajador/"><i class="fas fa-sign-out-alt"></i> Salir</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Main content -->
        <div class="flex-grow-1">
            <div class="main-content">
                <h1 class="mb-4">Historial de Pagos</h1>

                <!-- Filtro -->
                <form method="GET" class="d-flex gap-2 mb-4">
                    <input type="date" name="fecha1" class="form-control" value="<?= htmlspecialchars($fechaFilter1) ?>" style="width: auto;">
                    <input type="date" name="fecha2" class="form-control" value="<?= htmlspecialchars($fechaFilter2) ?>" style="width: auto;">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="ver_pagos.php" class="btn btn-secondary">Quitar Filtros</a>
                </form>

                <!-- Tabla de pagos -->
                <div class="table-responsive">
                    <?php if (count($pagos) > 0): ?>
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Nombre</th>
                                    <th>Total</th>
                                    <th>Método</th>
                                    <th>Recurrente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pagos as $index => $pago): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                                        <td><?= htmlspecialchars($pago['nombre']) ?></td>
                                        <td><?= htmlspecialchars($pago['total']) ?></td>
                                        <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                        <td><?= htmlspecialchars($pago['recurrente']) ? "Sí" : "No" ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center text-muted">No se encontraron pagos para los filtros seleccionados.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 EnerGym Admin Dashboard. All rights reserved.</p>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
