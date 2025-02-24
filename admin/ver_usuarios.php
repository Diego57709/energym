<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Manager') {
    header('Location: ../index.php?error=permisos_insuficientes');
    exit();
}

require '../partials/timeout.php';
include '../partials/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../components/phpmailer/src/Exception.php';
require '../components/phpmailer/src/PHPMailer.php';
require '../components/phpmailer/src/SMTP.php';

$mensaje_exito = '';
$mensaje_error = '';


// 1) CREACIÓN DE USUARIOS (sólo Entrenador / Trabajador) con envío de email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_user'])) {
    $rol               = trim($_POST['rol'] ?? '');
    $dni               = trim($_POST['dni'] ?? '');
    $nombre            = trim($_POST['nombre'] ?? '');
    $apellidos         = trim($_POST['apellidos'] ?? '');
    $fecha_nacimiento  = trim($_POST['fecha_nacimiento'] ?? '');
    $direccion         = trim($_POST['direccion'] ?? '');
    $codigo_postal     = trim($_POST['codigo_postal'] ?? '');
    $telefono          = trim($_POST['telefono'] ?? '');
    $email             = trim($_POST['email'] ?? '');

    if (empty($rol)) {
        $mensaje_error = "No se especificó un rol.";
    } elseif (!in_array($rol, ['Entrenador', 'Trabajador'])) {
        $mensaje_error = "Solo se permite crear Entrenador o Trabajador, no '$rol'.";
    }
    if (empty($dni) || empty($nombre) || empty($apellidos) || empty($email)) {
        $mensaje_error = "Los campos DNI, Nombre, Apellidos y Email son obligatorios.";
    }

    // Si no hay error, insertamos según el rol
    if (empty($mensaje_error)) {
        if ($rol === 'Entrenador') {
            $sqlInsert = "INSERT INTO entrenadores (
                dni, nombre, apellidos,
                fecha_nacimiento, direccion,
                codigo_postal, telefono, email, fecha_contratacion
            ) VALUES (
                '$dni', '$nombre', '$apellidos',
                '$fecha_nacimiento', '$direccion',
                '$codigo_postal', '$telefono', '$email', NOW()
            )";
        } else {
            $sqlInsert = "INSERT INTO trabajadores (
                dni, nombre, apellidos,
                fecha_nacimiento, direccion,
                codigo_postal, telefono, email, fecha_contratacion
            ) VALUES (
                '$dni', '$nombre', '$apellidos',
                '$fecha_nacimiento', '$direccion',
                '$codigo_postal', '$telefono', '$email', NOW()
            )";
        }

        if (mysqli_query($conn, $sqlInsert)) {
            $mensaje_exito = "$rol agregado correctamente: $nombre $apellidos";
            $nuevoId = mysqli_insert_id($conn);

            $token = bin2hex(random_bytes(16));
            if ($rol === 'Entrenador') {
                $tabla     = 'entrenadores';
                $campoId   = 'entrenador_id';
                $urlCambio = "http://energym.ddns.net/entrenador/crear_password.php?token="; 
            } else {
                $tabla     = 'trabajadores';
                $campoId   = 'trabajador_id';
                $urlCambio = "http://energym.ddns.net/trabajador/crear_password.php?token="; 
            }

            $updateTokenSQL = "UPDATE $tabla SET reset_token = '$token' WHERE $campoId = $nuevoId";
            mysqli_query($conn, $updateTokenSQL);

            $linkCrearPassword = $urlCambio . urlencode($token);
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'energym.asir@gmail.com';
                $mail->Password   = 'wvaz qdrj yqfm bnub'; 
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;

                $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
                $mail->addAddress($email, $nombre);

                $mail->isHTML(true);
                $mail->Subject = "Bienvenido a EnerGym - Crea tu contraseña ($rol)";
                $mail->Body = "
                    <h2>¡Hola, $nombre $apellidos!</h2>
                    <p>Te damos la bienvenida como <strong>$rol</strong> en EnerGym.</p>
                    <p>Para establecer tu contraseña, haz clic en el siguiente enlace:</p>
                    <p><a href='$linkCrearPassword' target='_blank'>Crear contraseña</a></p>
                    <p>Si el enlace no funciona, copia y pega esta URL en tu navegador:</p>
                    <p>$linkCrearPassword</p>
                    <br>
                    <p>¡Gracias por unirte a EnerGym!</p>
                ";

                $mail->send();
                // Opcionalmente, podrías agregar un mensaje adicional: "Se ha enviado un correo..."
            } catch (Exception $e) {
                $mensaje_error = "El usuario se creó correctamente, pero hubo un error al enviar el correo: {$mail->ErrorInfo}";
            }

        } else {
            $mensaje_error = "Error al insertar: " . mysqli_error($conn);
        }
    }
}

// 2) MOSTRAR TODOS LOS USUARIOS (Cliente, Trabajador, Entrenador)
$roleFilter = $_GET['role'] ?? 'todos';

$sqlClientes = "
    SELECT 
      cliente_id AS id,
      dni,
      nombre,
      apellidos,
      fecha_nacimiento,
      direccion,
      codigo_postal,
      telefono,
      email,
      'Cliente' AS role
    FROM clientes
";
$sqlTrabajadores = "
    SELECT 
      trabajador_id AS id,
      dni,
      nombre,
      apellidos,
      fecha_nacimiento,
      direccion,
      codigo_postal,
      telefono,
      email,
      'Trabajador' AS role
    FROM trabajadores
";
$sqlEntrenadores = "
    SELECT 
      entrenador_id AS id,
      dni,
      nombre,
      apellidos,
      fecha_nacimiento,
      direccion,
      codigo_postal,
      telefono,
      email,
      'Entrenador' AS role
    FROM entrenadores
";

$sqlUnion = "(
    $sqlClientes
) UNION ALL (
    $sqlTrabajadores
) UNION ALL (
    $sqlEntrenadores
)
ORDER BY id, role ASC
";

if ($roleFilter !== 'todos') {
    $sqlUnion = "SELECT * FROM ($sqlUnion) AS users WHERE role = '$roleFilter'";
}

$result = mysqli_query($conn, $sqlUnion);
if (!$result) {
    die("Error al obtener usuarios: " . mysqli_error($conn));
}
$usuarios = mysqli_fetch_all($result, MYSQLI_ASSOC);

// 3) CONTADORES: CLIENTES, TRABAJADORES, ENTRENADORES
function countTable($conn, $table) {
    $sqlCount = "SELECT COUNT(*) AS cnt FROM $table";
    $resCount = mysqli_query($conn, $sqlCount);
    if ($resCount && $row = mysqli_fetch_assoc($resCount)) {
        return (int)$row['cnt'];
    }
    return 0;
}

$countClientes     = countTable($conn, 'clientes');
$countTrabajadores = countTable($conn, 'trabajadores');
$countEntrenadores = countTable($conn, 'entrenadores');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Usuarios (Clientes / Trabajadores / Entrenadores)</title>
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
    <!-- Sidebar y contenido -->
    <div class="d-flex flex-grow-1">
        <!-- Barra lateral -->
        <div class="sidebar">
            <div class="sidebar-header">Panel de Control</div>
            <hr>
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="ver_usuarios.php" class="active-link"><i class="fas fa-users"></i> Ver Usuarios</a>
            <a href="ver_pagos.php"><i class="fas fa-credit-card"></i> Ver pagos</a>
            <a href="ver_asistencias.php"><i class="fas fa-door-open"></i> Ver asistencias</a>
            <a href="ver_clases.php"><i class="fas fa-bicycle"></i> Ver clases</a>
            <a href="/trabajador/"><i class="fas fa-sign-out-alt"></i> Salir</a>
            <a href="../logoutProcesar.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Contenido principal -->
        <div class="flex-grow-1">
            <div class="main-content">
                <!-- Tarjetas de conteo -->
                <div class="row mb-4">
                    <!-- Clientes -->
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Clientes</h5>
                                <p class="card-text fs-4"><?= $countClientes ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Trabajadores -->
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Trabajadores</h5>
                                <p class="card-text fs-4"><?= $countTrabajadores ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Entrenadores -->
                    <div class="col-md-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Entrenadores</h5>
                                <p class="card-text fs-4"><?= $countEntrenadores ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <h1 class="mb-4">Usuarios (Clientes, Trabajadores, Entrenadores)</h1>

                <!-- Botón para abrir el modal -->
                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-user-plus"></i> Añadir Usuario
                </button>

                <!-- Mensajes de éxito o error -->
                <?php if ($mensaje_exito): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensaje_exito) ?></div>
                <?php endif; ?>
                <?php if ($mensaje_error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($mensaje_error) ?></div>
                <?php endif; ?>

                <!-- Filtro de roles: todos / cliente / trabajador / entrenador -->
                <form method="GET" class="filter-form">
                    <select name="role" class="form-select w-auto">
                        <option value="todos" <?= ($roleFilter === 'todos') ? 'selected' : '' ?>>Todos</option>
                        <option value="Cliente" <?= ($roleFilter === 'Cliente') ? 'selected' : '' ?>>Clientes</option>
                        <option value="Trabajador" <?= ($roleFilter === 'Trabajador') ? 'selected' : '' ?>>Trabajadores</option>
                        <option value="Entrenador" <?= ($roleFilter === 'Entrenador') ? 'selected' : '' ?>>Entrenadores</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="ver_usuarios.php" class="btn btn-secondary">Quitar Filtro</a>
                </form>

                <!-- Tabla unificada con todos los roles -->
                <div class="table-responsive">
                    <?php if (!empty($usuarios)): ?>
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>DNI</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['id']) ?></td>
                                    <td><?= htmlspecialchars($u['dni']) ?></td>
                                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                                    <td><?= htmlspecialchars($u['apellidos']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['telefono']) ?></td>
                                    <td><?= htmlspecialchars($u['role']) ?></td>
                                    <td>
                                        <!-- Eliminar -->
                                        <form action="ver_usuarios_modificar_eliminar.php" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($u['id']) ?>">
                                            <input type="hidden" name="role" value="<?= htmlspecialchars($u['role']) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                        <!-- Podrías añadir un botón de Editar si lo deseas -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center text-muted">No hay usuarios registrados para este filtro.</p>
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

<!-- Modal para crear Entrenador o Trabajador -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="ver_usuarios.php">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Añadir Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="crear_user" value="1" />

          <div class="mb-3">
            <label for="rol" class="form-label">Tipo de Usuario</label>
            <select class="form-select" id="rol" name="rol" required>
              <option value="">-- Selecciona --</option>
              <option value="Entrenador">Entrenador</option>
              <option value="Trabajador">Trabajador</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input type="text" class="form-control" id="dni" name="dni" required>
          </div>

          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>

          <div class="mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
          </div>

          <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
          </div>

          <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion">
          </div>

          <div class="mb-3">
            <label for="codigo_postal" class="form-label">Código Postal</label>
            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal">
          </div>

          <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono">
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>

          <!-- Agrega más campos si son necesarios (password para trabajadores, especialidad, etc.) -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
