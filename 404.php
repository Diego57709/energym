<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - Página no encontrada</title>
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
        .error-container {
            width: 400px;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            margin: 5rem auto;
            text-align: center;
        }
        .error-title {
            font-size: 2rem;
            font-weight: bold;
            color: #dc3545;
            text-align: center;
            margin-bottom: 1rem;
        }
        .error-description {
            text-align: center;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }
        .btn-back {
            display: inline-block;
            background-color: #0f8b8d;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none; /* Para que se vea como botón */
        }

        .btn-back:hover {
            background-color: #0a5e5f;
        }
        </style>
</head>

<body>

    <?php require 'partials/header1.view.php'; ?>

    <div class="main">
        <div class="error-container">
            <h1 class="error-title">Error 404</h1>
            <p class="error-description">Lo sentimos, la página que buscas no fue encontrada. Te devolveremos al inicio...</p>
            <a href="index.php" class="btn-back text-center">Volver al Inicio</a>
            <script>
                setTimeout(function(){
                    window.location.href = 'index.php';
                }, 3000); // Redirect after 3 seconds
            </script>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <?php require 'partials/footer.view.php'; ?>

</body>
</html>
