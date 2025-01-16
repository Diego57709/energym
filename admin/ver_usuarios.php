<?php
// Tiene manager?
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Manager') {
    header('Location: ../index.php?error=permisos_insuficientes');
    exit();
}
require '../partials/timeout.php';
include '../partials/db.php';

// Filtro de roles
$roleFilter = $_GET['role'] ?? 'todos';

$queryClientes = "SELECT 'Cliente' AS role, cliente_id AS id, nombre COLLATE utf8mb4_general_ci AS nombre, email COLLATE utf8mb4_general_ci AS email FROM clientes";
$queryTrabajadores = "SELECT 'Trabajador' AS role, trabajador_id AS id, nombre COLLATE utf8mb4_general_ci AS nombre, email COLLATE utf8mb4_general_ci AS email FROM trabajadores";
$queryEntrenadores = "SELECT 'Entrenador' AS role, entrenador_id AS id, nombre COLLATE utf8mb4_general_ci AS nombre, email COLLATE utf8mb4_general_ci AS email FROM entrenadores";

// Combinar las queries con el UNION
$queryAll = "$queryClientes UNION ALL $queryTrabajadores UNION ALL $queryEntrenadores ORDER BY role, nombre";

if ($roleFilter !== 'todos') {
    $queryAll = "SELECT * FROM ($queryAll) AS users WHERE role = '$roleFilter'";
}

$result = mysqli_query($conn, $queryAll);
if (!$result) {
    die("Error in query: " . mysqli_error($conn));
}
$usuarios = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Contar cada tipo
$countClientesQuery = "SELECT COUNT(*) AS count FROM clientes";
$countTrabajadoresQuery = "SELECT COUNT(*) AS count FROM trabajadores";
$countEntrenadoresQuery = "SELECT COUNT(*) AS count FROM entrenadores";

$countClientesResult = mysqli_query($conn, $countClientesQuery);
$countTrabajadoresResult = mysqli_query($conn, $countTrabajadoresQuery);
$countEntrenadoresResult = mysqli_query($conn, $countEntrenadoresQuery);

$countClientes = mysqli_fetch_assoc($countClientesResult)['count'];
$countTrabajadores = mysqli_fetch_assoc($countTrabajadoresResult)['count'];
$countEntrenadores = mysqli_fetch_assoc($countEntrenadoresResult)['count'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Usuarios</title>
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
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
            <a href="ver_usuarios.php" class="active-link"><i class="fas fa-users"></i> Ver Usuarios </a> 
            <a href="ver_pagos.php"><i class="fas fa-credit-card"></i> Ver pagos </a>
            <a href="ver_asistencias.php"><i class="fas fa-door-open"></i> Ver asistencias</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Main content -->
        <div class="flex-grow-1">
            <div class="main-content">
            <div class="row mb-4">
            <!-- Clientes Box -->
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Clientes</h5>
                        <p class="card-text fs-4"><?= $countClientes ?></p>
                    </div>
                </div>
            </div>

            <!-- Trabajadores Box -->
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Trabajadores</h5>
                        <p class="card-text fs-4"><?= $countTrabajadores ?></p>
                    </div>
                </div>
            </div>

            <!-- Entrenadores Box -->
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Entrenadores</h5>
                        <p class="card-text fs-4"><?= $countEntrenadores ?></p>
                    </div>
                </div>
            </div>
            </div>
                <h1 class="mb-4">Usuarios Registrados</h1>

                <!-- Filtro de tipo de usuario -->
                <form method="GET" class="filter-form d-flex gap-2">
                    <select name="role" class="form-select" style="width: auto;">
                        <option value="todos" <?= $roleFilter === 'todos' ? 'selected' : '' ?>>Todos</option>
                        <option value="Cliente" <?= $roleFilter === 'Cliente' ? 'selected' : '' ?>>Clientes</option>
                        <option value="Trabajador" <?= $roleFilter === 'Trabajador' ? 'selected' : '' ?>>Trabajadores</option>
                        <option value="Entrenador" <?= $roleFilter === 'Entrenador' ? 'selected' : '' ?>>Entrenadores</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="ver_usuarios.php" class="btn btn-secondary">Quitar Filtro</a>
                </form>

                <!-- Tabla de usuarios -->
                <div class="table-responsive">
                    <?php if (count($usuarios) > 0): ?>
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th> <!-- Actions column -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $index => $usuario): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                                        <td><?= htmlspecialchars($usuario['role']) ?></td>
                                        <td>
                                            <!-- Modificar Button -->
                                            <a href="ver_usuarios_modificar.php?id=<?= urlencode($usuario['id']) ?>&role=<?= urlencode($usuario['role']) ?>"
                                            class="btn btn-warning btn-sm">
                                                Cambiar contraseña
                                            </a>
                                            <!-- Eliminar Button -->
                                            <form action="ver_usuarios_modificar_eliminar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">
                                                <input type="hidden" name="role" value="<?= htmlspecialchars($usuario['role']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center text-muted">No hay usuarios registrados para la categoría seleccionada.</p>
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
