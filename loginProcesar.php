<?php
require 'partials/header1.view.php';
include 'partials/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: 404.php');
    exit();
}

session_start();

// Recuperar datos del formulario
$email    = $_POST['email']    ?? '';
$password = $_POST['password'] ?? '';

// -----------------------------------
// 1) BUSCAR EN CLIENTES
// -----------------------------------
$sqlClientes = "SELECT * FROM clientes WHERE email = '$email' LIMIT 1";
$resClientes = mysqli_query($conn, $sqlClientes);

if ($resClientes && mysqli_num_rows($resClientes) === 1) {
    $cliente = mysqli_fetch_assoc($resClientes);

    // Verificar contraseña
    if (password_verify($password, $cliente['password'])) {
        // Contraseña correcta
        $_SESSION['nombre']    = $cliente['nombre'];
        $_SESSION['id'] = $cliente['cliente_id'];
        $_SESSION['email'] = $cliente['email'];
        $_SESSION['usuario'] = "cliente";
        $_SESSION['timeout'] = time() + 1800;
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
$sqlTrab = "SELECT * FROM trabajadores WHERE email = '$email' LIMIT 1";
$resTrab = mysqli_query($conn, $sqlTrab);

if ($resTrab && mysqli_num_rows($resTrab) === 1) {
    $trabajador = mysqli_fetch_assoc($resTrab);

    if (password_verify($password, $trabajador['password'])) {
        // Contraseña correcta
        $_SESSION['nombre']       = $trabajador['nombre']; 
        $_SESSION['id'] = $trabajador['trabajador_id'];
        $_SESSION['email'] = $trabajador['email'];
        $_SESSION['usuario'] = "trabajador";
        $_SESSION['timeout'] = time() + 1800;
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
$sqlEntr = "SELECT * FROM entrenadores WHERE email = '$email' LIMIT 1";
$resEntr = mysqli_query($conn, $sqlEntr);

if ($resEntr && mysqli_num_rows($resEntr) === 1) {
    $entrenador = mysqli_fetch_assoc($resEntr);

    if (password_verify($password, $entrenador['password'])) {
        // Contraseña correcta
        $_SESSION['nombre'] = $entrenador['nombre'];
        $_SESSION['id'] = $entrenador['entrenador_id'];
        $_SESSION['email'] = $entrenador['email'];
        $_SESSION['usuario'] = "entrenador";
        $_SESSION['timeout'] = time() + 1800;
        header('Location: entrenador.php'); // Ajusta tu ruta
        exit();
    } else {
        header('Location: login.php?error=contraseña_incorrecta');
        exit();
    }
}

// -----------------------------------
// 4) NO SE ENCONTRÓ EN NINGUNA TABLA
// -----------------------------------
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario no encontrado</title>
    <!-- Bootstrap CSS -->
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
        .login-container {
            max-width: 400px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            margin: 5rem auto;
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="login-container text-center">
            <h3>Usuario no encontrado</h3>
            <p>Volviendo a la página de inicio de sesión...</p>
            <form action="login.php" method="POST">
                <!-- Por si quieres recordar el email introducido -->
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <button type="submit" class="btn btn-warning mt-3 w-100" style="background-color:#28a745; color:white;">
                    Haga clic aquí si no es redirigido automáticamente
                </button>
            </form>
        </div>
    </div>
    <?php include '../partials/footer.php'?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        setTimeout(function(){
            window.location.href = 'login.php';
        }, 3000);
    </script>
</body>
</html>
<?php
exit();
