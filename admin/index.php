<?php
// Simple login validation (basic example, do not use this directly in production)
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Manager') {
    header('Location: ../index.php?error=permisos_insuficientes');
    exit();
}
require '../partials/timeout.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1; /* Allows the main content to grow and fill available space */
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
        .sidebar-header {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
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
            <a href="admin.php" class="active-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="ver_usuarios.php"><i class="fas fa-users"></i> Ver Usuarios </a> 
            <a href="ver_pagos.php"><i class="fas fa-credit-card"></i> Ver pagos </a>
            <a href="ver_asistencias.php"><i class="fas fa-door-open"></i> Ver asistencias</a>
            <a href="../logoutProcesar.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Main content -->
        <div class="flex-grow-1">
            <div class="main-content">
                <h1 class="mb-4">Bienvenido al panel de Admin</h1>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-center p-4">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h4>Modificar Usuarios</h4>
                            <p>Ver y modificar los usuarios.</p>
                            <a href="ver_usuarios.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center p-4">
                            <i class="fas fa-credit-card fa-3x text-warning mb-3"></i>
                            <h4>Ver pagos</h4>
                            <p>Ver pagos.</p>
                            <a href="ver_pagos.php" class="btn btn-warning">Go</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center p-4">
                            <i class="fas fa-door-open fa-3x text-success mb-3"></i>
                            <h4>Ver asistencias</h4>
                            <p>Ver asistencias.</p>
                            <a href="#" class="btn btn-success">Go</a>
                        </div>
                    </div>
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
