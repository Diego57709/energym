<?php
    require 'partials/header1.view.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <style>
        body {background-color: #f4f4f4;}
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 50px auto;
        }
        .login-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        .login-container label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }
        .login-container input {
            width: 92.5%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #218838;
        }
        .login-container .plan-link {
            margin-top: 15px;
            text-align: center;
        }
        .login-container .plan-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-container .plan-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Inicio de Sesión</h2>
        <form action="cliente.php" method="POST">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required placeholder="Usuario123">

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required placeholder="********">

            <button type="submit">Iniciar Sesión</button>
        </form>
        <div class="plan-link">
            <p>¿No estas registrado? <a href="planes.php">Elige tu plan</a>.</p>
        </div>
    </div>
</body>
</html>

<?php
    require 'partials/footer.view.php';
?>
