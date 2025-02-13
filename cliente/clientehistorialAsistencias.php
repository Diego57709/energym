<?php
declare(strict_types=1);

include '../partials/db.php';
session_start();

// Redirigir al login si no hay sesión iniciada
if (!isset($_SESSION['usuario']) && !isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$id_cliente = $_SESSION['id'];
$nombreUsuario = $_SESSION['nombre'];
$tipo = $_SESSION['usuario'];

// Filtros de mes, año y tipo
$mesSeleccionado = $_GET['mes'] ?? date('m'); // Mes actual por defecto
$anioSeleccionado = $_GET['anio'] ?? date('Y'); // Año actual por defecto
$tipoSeleccionado = $_GET['tipo'] ?? 'todos'; // 'todos' por defecto (sin filtro)

// Consulta SQL para obtener asistencias del cliente
$sqlHistorial = "SELECT fecha_hora, tipo FROM asistencias 
                 WHERE usuario_id = '$id_cliente' 
                 AND tipo_usuario = 'cliente' 
                 AND YEAR(fecha_hora) = '$anioSeleccionado'
                 AND MONTH(fecha_hora) = '$mesSeleccionado'";

// Filtrar por tipo de asistencia si se selecciona "entrada" o "salida"
if ($tipoSeleccionado !== 'todos') {
    $sqlHistorial .= " AND tipo = '$tipoSeleccionado'";
}

$sqlHistorial .= " ORDER BY fecha_hora DESC";

$resultHistorial = mysqli_query($conn, $sqlHistorial);
$asistencias = mysqli_fetch_all($resultHistorial, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Asistencias | EnerGym</title>
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
                <h1 class="text-center mb-4">Historial de Asistencias</h1>

                <!-- Filtros de mes, año y tipo -->
                <form method="GET" class="mb-4 d-flex justify-content-center gap-3 flex-wrap">
                    <!-- Filtro de mes -->
                    <select name="mes" class="form-select" style="width: auto;">
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
                        <?php for ($anio = date('Y'); $anio >= 2020; $anio--): ?>
                            <option value="<?= $anio ?>" <?= $anioSeleccionado == $anio ? 'selected' : '' ?>>
                                <?= $anio ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <!-- Filtro de tipo -->
                    <select name="tipo" class="form-select" style="width: auto;">
                        <option value="todos" <?= $tipoSeleccionado == 'todos' ? 'selected' : '' ?>>Todos</option>
                        <option value="entrada" <?= $tipoSeleccionado == 'entrada' ? 'selected' : '' ?>>Entradas</option>
                        <option value="salida" <?= $tipoSeleccionado == 'salida' ? 'selected' : '' ?>>Salidas</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="clientehistorialAsistencias.php" class="btn btn-secondary">Quitar Filtro</a>
                </form>

                <!-- Tabla de asistencias -->
                <?php if (count($asistencias) > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Fecha y Hora</th>
                                <th>Tipo de Asistencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asistencias as $index => $asistencia): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= date('d F Y, h:i A', strtotime($asistencia['fecha_hora'])) ?></td>
                                    <td>
                                        <?php 
                                        if (strtolower($asistencia['tipo']) === 'entrada') {
                                            echo '<span class="text-success">Entrada</span>';
                                        } elseif (strtolower($asistencia['tipo']) === 'salida') {
                                            echo '<span class="text-danger">Salida</span>';
                                        } else {
                                            echo 'Desconocido';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center text-muted">No hay asistencias registradas para el periodo seleccionado.</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    ></script>
</body>
</html>
