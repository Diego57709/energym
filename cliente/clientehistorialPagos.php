<?php
declare(strict_types=1);
include '../partials/db.php';
session_start();

// Redirigir al login si no hay sesión iniciada
if (!isset($_SESSION['usuario']) && !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$nombreUsuario = $_SESSION['usuario'];
$id_cliente = $_SESSION['id'];

// Filtros de mes y año
$mesSeleccionado = $_GET['mes'] ?? null;
$anioSeleccionado = $_GET['anio'] ?? null;

// Base de la consulta
$sqlHistorialPagos = "SELECT cliente_id, metodo_pago, fecha_pago, total, recurrente FROM historial_pagos WHERE cliente_id = '$id_cliente' ";

// Agregar filtros si se seleccionaron mes y año
if ($mesSeleccionado && $anioSeleccionado) {
    $sqlHistorialPagos .= " WHERE YEAR(fecha_pago) = '$anioSeleccionado' AND MONTH(fecha_pago) = '$mesSeleccionado'";
}

$sqlHistorialPagos .= " ORDER BY fecha_pago DESC";

$resultHistorialPagos = mysqli_query($conn, $sqlHistorialPagos);
$pagos = mysqli_fetch_all($resultHistorialPagos, MYSQLI_ASSOC);
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
                <h1 class="text-center mb-4">Todos los Pagos</h1>

                <!-- Filtros de mes y año -->
                <form method="GET" class="mb-4 d-flex justify-content-center gap-3 flex-wrap">
                    <!-- Filtro de mes -->
                    <select name="mes" class="form-select" style="width: auto;">
                        <option value="">Todos los meses</option>
                        <option value="01" <?= $mesSeleccionado == '01' ? 'selected' : '' ?>>Enero</option>
                        <option value="02" <?= $mesSeleccionado == '02' ? 'selected' : '' ?>>Febrero</option>
                        <option value="03" <?= $mesSeleccionado == '03' ? 'selected' : '' ?>>Marzo</option>
                        <option value="04" <?= $mesSeleccionado == '04' ? 'selected' : '' ?>>Abril</option>
                        <option value="05" <?= $mesSeleccionado == '05' ? 'selected' : '' ?>>Mayo</option>
                        <option value="06" <?= $mesSeleccionado == '06' ? 'selected' : '' ?>>Junio</option>
                        <option value="07" <?= $mesSeleccionado == '07' ? 'selected' : '' ?>>Julio</option>
                        <option value="08" <?= $mesSeleccionado == '08' ? 'selected' : '' ?>>Agosto</option>
                        <option value="09" <?= $mesSeleccionado == '09' ? 'selected' : '' ?>>Septiembre</option>
                        <option value="10" <?= $mesSeleccionado == '10' ? 'selected' : '' ?>>Octubre</option>
                        <option value="11" <?= $mesSeleccionado == '11' ? 'selected' : '' ?>>Noviembre</option>
                        <option value="12" <?= $mesSeleccionado == '12' ? 'selected' : '' ?>>Diciembre</option>
                    </select>

                    <!-- Filtro de año -->
                    <select name="anio" class="form-select" style="width: auto;">
                        <option value="">Todos los años</option>
                        <?php for ($anio = date('Y'); $anio >= 2020; $anio--): ?>
                            <option value="<?= $anio ?>" <?= $anioSeleccionado == $anio ? 'selected' : '' ?>>
                                <?= $anio ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="clientehistorialPagos.php" class="btn btn-secondary">Quitar Filtro</a>
                </form>

                <!-- Tabla de pagos -->
                <?php if (count($pagos) > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha de Pago</th>
                                <th>Método de Pago</th>
                                <th>Total</th>
                                <th>Recurrente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagos as $index => $pago): ?>
                                <tr>
                                    <td><?= date('d F Y, h:i A', strtotime($pago['fecha_pago'])) ?></td>
                                    <td><?= htmlspecialchars($pago['metodo_pago']) ?></td>
                                    <td><?= number_format((float)$pago['total'], 2) ?></td>
                                    <td><?= $pago['recurrente'] ? 'Sí' : 'No' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-muted">No hay pagos registrados para el periodo seleccionado.</p>
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
