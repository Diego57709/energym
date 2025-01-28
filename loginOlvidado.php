<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
        .login-container {
            width: 400px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            margin: 5rem auto;
        }
        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <?php require 'partials/header1.view.php'; ?>

    <div class="main">
        <div class="login-container mx-auto">
            <h2 class="text-center mb-4">Recuperar Contraseña</h2>

            <!-- Formulario de recuperación -->
            <form action="loginRecuperacion.php" method="POST">
                <!-- Campo Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Introduce tu correo electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="example@domain.ext" required>
                </div>

                <!-- Mensaje de éxito/error -->
                <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
                    <div class="alert alert-success mt-3" role="alert">
                        Se ha enviado un correo para recuperar tu contraseña. Revisa tu bandeja de entrada.
                    </div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        No se encontró una cuenta con ese correo electrónico.
                    </div>
                <?php endif; ?>
                    <p class="text-muted mt-3">Si no lo ves, revisa tu carpeta de spam o intenta enviar la solicitud nuevamente.</p>
                <!-- Botón de envío -->
                <button type="submit" class="btn w-100 mt-3" style="background-color:#28a745; color:white;">Recuperar Contraseña</button>
            </form>

            <!-- Enlace para volver al inicio de sesión -->
            <div class="mt-3 text-center">
                <a href="login.php" class="text-decoration-none text-primary">Volver al inicio de sesión</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>

<?php require 'partials/footer.view.php'; ?>

</html>
