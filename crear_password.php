<?php
require 'partials/header2.view.php';
include 'partials/db.php';

// 1) Verificar si llega el token
if (!isset($_GET['token'])) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Token no válido</title>
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
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
                margin: 5rem auto;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="main">
            <div class="login-container">
                <h2 class="mb-4">Token no válido</h2>
                <p>El enlace ha expirado o ya fue utilizado. Solicita una nueva recuperación de contraseña.</p>
                <a href="login.php" class="btn mt-3 w-100" style="background-color: #0f8b8d; color:white;">Volver al inicio de sesión</a>
            </div>
        </div>
        <?php require 'partials/footer.view.php'; ?>
    </body>
    </html>
    <?php
    exit();
}

$token = $_GET['token'];

// 2) Verificar el token en la BD
$sql = "SELECT * FROM clientes WHERE reset_token = '$token' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Token inválido</title>
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
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
                margin: 5rem auto;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="main">
            <div class="login-container">
                <h2 class="mb-4">Token inválido</h2>
                <p>El enlace ya no es válido. Por favor, solicita un nuevo enlace de recuperación.</p>
                <a href="login.php" class="btn mt-3 w-100" style="background-color: #0f8b8d; color:white;">Volver al inicio de sesión</a>
            </div>
        </div>
        <?php require 'partials/footer.view.php'; ?>
    </body>
    </html>
    <?php
    exit();
}

// Aquí el token es válido
$cliente = mysqli_fetch_assoc($result);
$cliente_id = $cliente['cliente_id'];

// 3) Procesar el POST del formulario
$error = ""; // Para almacenar el mensaje de error si lo hay

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Por favor, rellena ambos campos de contraseña.";
    } elseif ($new_password !== $confirm_password) {
        // Mostrar error en rojo
        $error = "Las contraseñas no coinciden. Inténtalo de nuevo.";
    } else {
        // Todo bien, actualizamos la contraseña
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $updateSql = "UPDATE clientes 
                      SET password = '$hashed_password',
                          reset_token = NULL
                      WHERE cliente_id = $cliente_id";
        $updateResult = mysqli_query($conn, $updateSql);

        if ($updateResult) {
            // Contraseña actualizada con éxito
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Contraseña creada con éxito</title>
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
                        background-color: #f8f9fa;
                    }
                    .main {
                        flex: 1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .success-container {
                        width: 400px;
                        padding: 20px;
                        background-color: white;
                        border-radius: 10px;
                        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
                        text-align: center;
                        margin: 5rem auto;
                    }
                </style>
            </head>
            <body>
                <div class="main">
                    <div class="success-container">
                        <h4 class="alert-heading">¡Contraseña creada con éxito!</h4>
                        <p class="mb-3">Ya puedes iniciar sesión con tu nueva contraseña.</p>
                        <a href="login.php" class="btn btn-success w-100">Ir al inicio de sesión</a>
                    </div>
                </div>
                <?php require 'partials/footer.view.php'; ?>
            </body>
            </html>
            <?php
            exit();
        } else {
            $error = "Error al actualizar la contraseña. Por favor, inténtalo más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Contraseña</title>
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
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="main">
    <div class="login-container mx-auto">
        <h2 class="text-center mb-4">Crear tu nueva contraseña</h2>

        <!-- Mensaje de error si existiese -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para nueva contraseña -->
        <form method="POST">
            <div class="mb-3">
                <label for="new_password" class="form-label">Nueva contraseña</label>
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Introduce tu nueva contraseña" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar contraseña</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirma tu nueva contraseña" required>
            </div>
            <button type="submit" class="btn btn-success w-100 mt-3">Guardar Contraseña</button>
        </form>

        <div class="mt-3 text-center">
            <p>¿Ya tienes una cuenta? 
               <a href="login.php" class="text-primary text-decoration-none">Iniciar sesión</a>.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<?php require 'partials/footer.view.php'; ?>

</body>
</html>
