<?php
require 'partials/header1.view.php';
include 'partials/db.php';
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: 404.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if the password and email fields are set
    if (isset($_REQUEST['password']) && isset($_REQUEST['email'])) {
        $email = mysqli_real_escape_string($conn, $_REQUEST['email']);
        $password = password_hash($_REQUEST['password'], PASSWORD_DEFAULT);

        // Update the password in the database
        $sql = "UPDATE clientes SET password = '$password' WHERE email = '$email'";
        ?>
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
                }
            </style>
        </head>
        <body>

        <div class="main">
            <div class="login-container mx-auto text-center">
                <h2>Inicio de Sesión</h2>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <?php
                    if (mysqli_query($conn, $sql)) {
                        echo "<p class='inner-text text-success mt-3'>Contraseña agregada correctamente.</p>";
                        echo "<p class='inner-text mt-3'>Redirigiendo al inicio de sesión...</p>";
                        ?>
                        <script>
                            setTimeout(function(){
                                window.location.href = 'login.php';
                            }, 3000); // Redirect after 3 seconds
                        </script>
                        <?php
                    } else {
                        echo "<p class='inner-text text-danger mt-3'>Error al agregar la contraseña: " . mysqli_error($conn) . "</p>";
                    }
                    ?>
                </form>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

        </body>
        </html>

        <?php
    } else if (isset($_REQUEST['email'])) {
        $email = $_REQUEST['email'];
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Agregar Contraseña</title>

            <!-- Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .container {
            min-height: 50dvh;
        }
                .container {
                        display: flex;
                        flex-direction: column;
                        min-height: 50vh !important;
                    }
                .login-container {
                    max-width: 400px;
                    padding: 20px;
                    background-color: #f8f9fa;
                    border-radius: 10px;
                    box-shadow: 0 0 15px rgba(0,0,0,0.3);
                    margin: 5rem auto;
                }
            </style>
        </head>
        <body>

        <div class="container">
            <div class="login-container mx-auto text-center">
                <h2>Agregar Contraseña</h2>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <label for="password" class="form-label mt-3">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Escribe tu contraseña" required>

                    <button type="submit" class="btn btn-primary mt-4 w-100">Guardar Contraseña</button>
                </form>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

        </body>
        </html>

        <?php
    } else {
        header("Location: 404.php");
    }
}

require 'partials/footer.view.php';
?>
