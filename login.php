<?php
session_start();
if (isset($_SESSION['id_cliente'])) {
    header("Location: cliente.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            margin: 5rem auto;
        }
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <?php require 'partials/header1.view.php'; ?>

    <div class="main">
        <div class="login-container mx-auto">
            <h2 class="text-center mb-4">Inicio de Sesión</h2>

            <!-- Formulario de inicio de sesión -->
            <form action="procesar_login.php" method="POST">
                
                <!-- Campo Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="example@domain.ext" required>
                </div>

                <!-- Campo Contraseña -->
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>

                <!-- Mensaje de error -->
                <?php if (isset($_GET['error'])): ?>
                    <?php $error_message = htmlspecialchars($_GET['error']); ?>
                    <div class="error-message"><?php echo 'Contraseña incorrecta'; ?></div>
                <?php endif; ?>

                <!-- Botón de envío -->
                <button type="submit" class="btn w-100 mt-3" style="background-color:#28a745; color:white;">Iniciar Sesión</button>

            </form>

            <!-- Enlace para registrarse -->
            <div class="mt-3 text-center">
                <p>¿No estás registrado? <a href="planes.php" class="text-primary text-decoration-none">Elige tu plan</a>.</p>
            </div>

        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>

<?php require 'partials/footer.view.php'; ?>

</html>
