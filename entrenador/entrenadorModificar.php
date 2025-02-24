<?php
declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../components/vendor/autoload.php';
include '../partials/db.php';

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['id']) || ($_SESSION['usuario'] !== 'entrenador')) {
    header("Location: ../login.php");
    exit;
}

$id_usuario = $_SESSION['id'];

$sql = "SELECT * FROM entrenadores WHERE entrenador_id = '$id_usuario'";
$result = mysqli_query($conn, $sql);
$entrenador = mysqli_fetch_assoc($result);

if (!$entrenador) {
    die("Error al obtener los datos del entrenador.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $codigo_postal = trim($_POST['codigo_postal']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    $sqlUpdate = "UPDATE entrenadores SET 
    nombre = '$nombre', 
    apellidos = '$apellidos',
    email = '$email', 
    telefono = '$telefono', 
    direccion = '$direccion', 
    codigo_postal = '$codigo_postal', 
    fecha_nacimiento = '$fecha_nacimiento'
    WHERE entrenador_id = '$id_usuario'";

    if (mysqli_query($conn, $sqlUpdate)) {
        $mensaje = "Datos actualizados con éxito.";
        header("Location: trabajadorModificar.php?mensaje=$mensaje");
        exit;
    } else {
        $error = "Error al actualizar los datos: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Datos | EnerGym</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
        .summary-box {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .btn-success {
            background-color: #17a2b8;
        }
        .btn-success:hover {
            background-color: #138f9f;
        }
        .undo-container {
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: none;
            margin-top: 15px;
            animation: slideUp 0.5s ease-out forwards;
        }
        .undo-btn {
            color: white;
            background-color: #0f8b8d;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <?php require '../partials/header2.view.php'; ?>

    <div class="main py-4">
        <div class="container bg-white rounded shadow-sm p-4">
            <div class="row">
                <div class="col-lg-8">
                    <h2 class="step-header mb-3">Modificar Datos Personales</h2>

                    <!-- Mensajes de éxito o error -->
                    <?php if (isset($_GET['mensaje'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['mensaje']) ?></div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <!-- Formulario de modificación -->
                    <form method="POST" action="trabajadorModificar.php" class="form-section" id="datos-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-bold">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($entrenador['nombre']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellidos" class="form-label fw-bold">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= htmlspecialchars($entrenador['apellidos'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($entrenador['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($entrenador['telefono']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <label for="direccion" class="form-label fw-bold">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($entrenador['direccion']) ?>" required>
                            </div>
                        </div>


                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label for="codigo_postal" class="form-label fw-bold">Código Postal</label>
                                <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" value="<?= htmlspecialchars($entrenador['codigo_postal']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($entrenador['fecha_nacimiento']) ?>" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-4">Actualizar Datos</button>

                        <!-- Cuadro de confirmación de deshacer -->
                        <div class="undo-container text-center" id="undo-container">
                            <p class="mb-2 fw-bold">¿Quieres deshacer los cambios realizados?</p>
                            <button type="button" class="undo-btn" id="undo-button">Deshacer</button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <div class="summary-box">
                        <h4 class="fw-bold mb-3">Instrucciones</h4>
                        <p class="text-muted">Asegúrate de completar todos los campos con datos válidos antes de guardar los cambios.</p>
                        <p class="text-muted">Si deseas regresar, utiliza el botón "Deshacer".</p>
                        <p class="text-muted">Si deseas deshacer los cambios, utiliza el botón "Volver".</p>
                        <a href="index.php" class="btn btn-secondary w-100 mt-3">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../partials/footer.view.php'; ?>

    <script>
        // Almacenar valores originales
        const originalData = {
            nombre: '<?= htmlspecialchars($entrenador['nombre']) ?>',
            apellidos: '<?= htmlspecialchars($entrenador['apellidos'] ?? '') ?>',
            email: '<?= htmlspecialchars($entrenador['email']) ?>',
            telefono: '<?= htmlspecialchars($entrenador['telefono']) ?>',
            direccion: '<?= htmlspecialchars($entrenador['direccion']) ?>',
            codigo_postal: '<?= htmlspecialchars($entrenador['codigo_postal']) ?>',
            fecha_nacimiento: '<?= htmlspecialchars($entrenador['fecha_nacimiento']) ?>',
        };

        const form = document.getElementById('datos-form');
        const undoContainer = document.getElementById('undo-container');
        const undoButton = document.getElementById('undo-button');

        form.addEventListener('input', () => {
            undoContainer.style.display = 'block';
        });

        undoButton.addEventListener('click', () => {
            document.getElementById('nombre').value = originalData.nombre;
            document.getElementById('apellidos').value = originalData.apellidos;
            document.getElementById('email').value = originalData.email;
            document.getElementById('telefono').value = originalData.telefono;
            document.getElementById('direccion').value = originalData.direccion;
            document.getElementById('codigo_postal').value = originalData.codigo_postal;
            document.getElementById('fecha_nacimiento').value = originalData.fecha_nacimiento;
            undoContainer.style.display = 'none';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
