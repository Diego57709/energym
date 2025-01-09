<?php
require 'partials/header1.view.php';
include 'partials/db.php';
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: 404.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];
    $sql = "SELECT * FROM clientes WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $clientes = mysqli_fetch_assoc($result);

        if (!empty($clientes['password'])) {
            if (password_verify($password, $clientes['password'])) {
                // Contraseña correcta
                header('Location: cliente.php');
                session_start();
                $_SESSION['usuario'] = $clientes['nombre'];
                $_SESSION['id_cliente'] = $clientes['cliente_id'];
                exit();
            } else {
                // Si no coincide la contraseña -> login.php con código de error
                header('Location: login.php?error=contraseña_incorrecta');
                exit();
            }
        } else {
            // Si no hay contraseña -> procesar_login2.php
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Redirigiendo...</title>
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
                    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
                    margin: 5rem auto;
                    flex-grow: 0;
                }
                </style>
            </head>
            <body>
                <div class="main">
                    <div class="login-container mx-auto text-center">
                        <h2>Redirigiendo...</h2>
                        <p>Contraseña no encontrada<br>
                        <?php echo htmlspecialchars($email); ?>
                        </p>
                        <form id="redirectForm" action="procesar_login2.php" method="POST">
                            <input type="hidden" name="email" value="<?php echo $email; ?>"> <!-- Display email here -->
                            <button type="submit" class="btn btn-primary mt-3 w-100" style="background-color:#28a745; color:white;">Haga clic aquí si no es redirigido automáticamente</button>
                        </form>
                    </div>

                </div>
                <!-- Bootstrap JS -->
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
                <script>
                 // Redirect after 3 seconds
                 setTimeout(function(){
        document.getElementById('redirectForm').submit();
    }, 3000);
            </script>
            </body>
            </html>
            <?php // Prevent further PHP execution
        }
    } else {
        // Si no encuentra el usuario -> login.php
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Usuario no encontrado</title>
            <!-- Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .main {
            min-height: 50dvh;
        }
                .main {
                    display: flex;
                    flex-direction: column;
                }
                .login-container {
                    max-width: 400px;
                    padding: 20px;
                    background-color: #f8f9fa;
                    border-radius: 10px;
                    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
                    margin: 5rem auto;
                    flex-grow: 1;
                }
            </style>
        </head>
        <body>
            <div class="main">
                <div class="login-container mx-auto text-center">
                    <h3>Usuario no encontrado, volviendo...</h3>
                    <form action="login.php" method="POST">
                        <input type="hidden" name="email" value="<?php echo $email; ?>"> <!-- Display email here -->
                        <button type="submit" class="btn btn-warning mt-3 w-100" style="background-color:#28a745; color:white;">Haga clic aquí si no es redirigido automáticamente</button>
                    </form>
                </div>
            </div>
            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
            <script>
                setTimeout(function(){
                    window.location.href = 'login.php';
                }, 3000); // Redirect after 3 seconds
            </script>
        </body>
        </html>
        <?php // Prevent further PHP execution
    }
}
        require 'partials/footer.view.php';
