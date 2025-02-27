<?php
session_start();
include 'partials/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Recuperar datos del formulario
$email    = $_POST['email']    ?? '';
$password = $_POST['password'] ?? '';

// -----------------------------------
// 1) BUSCAR EN CLIENTES
// -----------------------------------
$stmtClientes = mysqli_prepare($conn, "SELECT * FROM clientes WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmtClientes, "s", $email);
mysqli_stmt_execute($stmtClientes);
$resClientes = mysqli_stmt_get_result($stmtClientes);

if ($resClientes && mysqli_num_rows($resClientes) === 1) {
    $cliente = mysqli_fetch_assoc($resClientes);

    // Verificar si tiene contraseña asignada
    if (empty($cliente['password'])) {
        // No tiene contraseña asignada
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Contraseña no asignada</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                html, body {
                    height: 100%;
                    margin: 0;
                    display: flex;
                    flex-direction: column;
                }
                .main {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .text-container {
                    max-width: 400px;
                    padding: 20px;
                    background-color: #f8f9fa;
                    border-radius: 10px;
                    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
                    text-align: center;
                }
            </style>
        </head>
        <body>
        <div class="main">
            <div class="text-container">
                <h3>Contraseña no asignada</h3>
                <p>Parece que aún no has creado tu contraseña.</p>
                <p>Por favor, revisa tu correo electrónico para completarlo.</p>
                <p>Redirigiendo al inicio de sesión en 3 segundos...</p>
                <a href="login.php" class="btn btn-primary mt-3 w-100">Volver al inicio de sesión</a>
            </div>
        </div>
        <script>
            setTimeout(function () {
                window.location.href = 'login.php';
            }, 3000);
        </script>
        <?php include 'partials/footer.view.php'; ?>
        </body>
        </html>
        <?php
        exit();
    }

    // Verificar contraseña
    if (password_verify($password, $cliente['password'])) {
        // Contraseña correcta
        $_SESSION['nombre']    = $cliente['nombre'];
        $_SESSION['id']        = $cliente['cliente_id'];
        $_SESSION['email']     = $cliente['email'];
        $_SESSION['usuario']   = "cliente";
        $_SESSION['timeout']   = time() + 1800;
        header('Location: cliente/');
        exit();
    } else {
        // Contraseña incorrecta
        header('Location: /login.php?error=contraseña_incorrecta');
        exit();
    }
}

// -----------------------------------
// 2) NO ESTÁ EN CLIENTES -> BUSCAR EN TRABAJADORES
// -----------------------------------
// -----------------------------------
// 2) NO ESTÁ EN CLIENTES -> BUSCAR EN TRABAJADORES
// -----------------------------------
$stmtTrab = mysqli_prepare($conn, "SELECT * FROM trabajadores WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmtTrab, "s", $email);
mysqli_stmt_execute($stmtTrab);
$resTrab = mysqli_stmt_get_result($stmtTrab);

if ($resTrab && mysqli_num_rows($resTrab) === 1) {
    $trabajador = mysqli_fetch_assoc($resTrab);

    if (password_verify($password, $trabajador['password'])) {
        // Determinar tiempo de sesión según el rol
        $tiempoSesion = ($trabajador['rol'] === 'camara') ? 43200 : 1800;

        // Iniciar sesión
        $_SESSION['nombre']    = $trabajador['nombre'];
        $_SESSION['id']        = $trabajador['trabajador_id'];
        $_SESSION['email']     = $trabajador['email'];
        $_SESSION['usuario']   = "trabajador";
        $_SESSION['rol']       = $trabajador['rol'];
        $_SESSION['timeout']   = time() + $tiempoSesion;

        header('Location: trabajador/');
        exit();
    } else {
        header('Location: /login.php?error=contraseña_incorrecta');
        exit();
    }
}


// -----------------------------------
// 3) NO ESTÁ EN TRABAJADORES -> BUSCAR EN ENTRENADORES
// -----------------------------------
$stmtEntr = mysqli_prepare($conn, "SELECT * FROM entrenadores WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmtEntr, "s", $email);
mysqli_stmt_execute($stmtEntr);
$resEntr = mysqli_stmt_get_result($stmtEntr);

if ($resEntr && mysqli_num_rows($resEntr) === 1) {
    $entrenador = mysqli_fetch_assoc($resEntr);

    if (password_verify($password, $entrenador['password'])) {
        // Contraseña correcta
        $_SESSION['nombre']    = $entrenador['nombre'];
        $_SESSION['id']        = $entrenador['entrenador_id'];
        $_SESSION['email']     = $entrenador['email'];
        $_SESSION['usuario']   = "entrenador";
        $_SESSION['timeout']   = time() + 1800;
        header('Location: entrenador/'); // Ajusta tu ruta si es necesario
        exit();
    } else {
        header('Location: login.php?error=contraseña_incorrecta');
        exit();
    }
}

// -----------------------------------
// 4) NO SE ENCONTRÓ EN NINGUNA TABLA
// -----------------------------------
include 'partials/header1.view.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario no encontrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .text-container {
            max-width: 400px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
    </style>
</head>
<body>
<div class="main">
    <div class="text-container">
        <h3>Usuario no encontrado</h3>
        <p>El correo ingresado no está registrado.</p>
        <p>Redirigiendo al inicio de sesión en 3 segundos...</p>
        <a href="login.php" class="btn btn-primary mt-3 w-100">Volver al inicio de sesión</a>
    </div>
</div>
<script>
    setTimeout(function () {
        window.location.href = 'login.php';
    }, 3000);
</script>
<?php include 'partials/footer.view.php'; ?>
</body>
</html>
