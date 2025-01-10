<?php
require 'partials/header1.view.php';
if (isset($_REQUEST['email'])) {
$email = $_REQUEST['email'];

echo var_dump($_REQUEST);
?>
if 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>

    <!-- Bootstrap CSS -->
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
            max-width: 400px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            margin: 5rem auto;
        }
        .inner-text {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>

<body>

<div class="main">
    <div class="login-container mx-auto text-center">
        <h2>Inicio de Sesión</h2>
        <h5 class="inner-text mt-3 mb-4">Usuario encontrado, agregue una contraseña</h5>

        <form action="procesar_login3.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Escribe tu contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3" style="background-color:#28a745; color:white;">Añadir contraseña</button>
        </form>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>

<?php
}
require 'partials/footer.view.php';

?>
