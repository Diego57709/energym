<?php 
require 'partials/header1.view.php'; 
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Por qué elegir EnerGym?</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .main-img {
            position: relative;
            width: 100%;
            height: 50vh;
            background: url('https://static.vecteezy.com/system/resources/thumbnails/026/781/389/small_2x/gym-interior-background-of-dumbbells-on-rack-in-fitness-and-workout-room-photo.jpg') center/cover no-repeat;
        }
        .main-img::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .main-img .text-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            text-align: center;
        }

        .overlay-inscribete {
            background-color: rgb(15, 139, 141) !important;
            padding: 0.65rem 1.4rem !important;
            color: white !important;;
            font-weight: bold !important;;
            font-size: 20px !important;
        }
        .overlay-inscribete:hover {
            background-color: rgb(10, 94, 95);
        }
        .card-img-top {
            object-fit: cover;
            height: 200px; /* Ensure a consistent height */
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>

<!-- Main Image Section -->
<div class="main-img">
    <div class="text-overlay">
        <h1>EnerGym</h1>
        <a href="planes.php" class="btn overlay-inscribete mt-3 d-inline-block">¡Inscríbete Ya!</a>
    </div>
</div>

<!-- Features Section -->
<div class="container py-5">
    <h2 class="text-center mb-4">¿Por qué elegir EnerGym?</h2>
    <div class="row">
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="https://elcierredigital.com/filesedc/uploads/image/post/metrpolitan-slider-espacios-03_1200_800.webp" alt="Instalaciones" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Instalaciones Modernas</h3>
                    <p class="card-text">Disfruta de equipos de última generación y áreas dedicadas.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="https://i.blogs.es/60be3c/24793137175_30cc0f8b76_o/1366_2000.jpg" alt="Actividades" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Clases Dirigidas</h3>
                    <p class="card-text">Participa en clases dinámicas como yoga, spinning y más.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="https://site.educa.madrid.org/ies.elisasorianofischer.getafe//wp-content/uploads/ies.elisasorianofischer.getafe/2022/03/relij-horarios.jpeg" alt="Horario" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Horarios Amplios</h3>
                    <p class="card-text">Aprovecha nuestra apertura 24/7 para entrenar cuando quieras.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Newsletter Section -->
<div id="newsletter" class="bg-dark text-white py-5 custom-border p-4" style="border-top: 5px solid rgb(15, 139, 141); border-bottom: 5px solid rgb(15, 139, 141)">
    <div class="container">
        <h2>Suscríbete a nuestra Newsletter</h2>
        <?php if (isset($_GET['status'])): ?>
            <div class="alert <?php echo $_GET['status'] === 'success' ? 'alert-success' : 'alert-danger'; ?> mt-3">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
        <form action="procesar_newsletter.php#newsletter" method="POST" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="nombre" placeholder="Tu nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
                <input type="email" name="correo" placeholder="Tu correo electrónico" class="form-control" required>
            </div>
            <div class="col-12">
                <label>
                    <input type="checkbox" name="condiciones" required>
                    Acepto los <a href="https://www.iubenda.com/es/help/40472-puedo-usar-una-plantilla-de-terminos-y-condiciones" target="_blank" style="color: rgb(15, 139, 141);">términos y condiciones</a>
                </label>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary mt-3">Suscribirme</button>
            </div>
            <p class="fs-8">Recibe las últimas noticias, ofertas y consejos de entrenamiento directamente en tu correo.</p>
        </form>
    </div>
</div>


</div>

<!-- Events Section -->
<div class="container py-5">
    <h2 class="text-center mb-4">Eventos Próximos</h2>
    <div class="row">
        <!-- Evento: Competencia de Levantamiento de Pesas -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="https://i.ytimg.com/vi/YuK9FUj_3NE/maxresdefault.jpg" 
                     alt="Competencia de Levantamiento" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Competencia de Levantamiento</h3>
                    <p class="card-text">Demuestra tu fuerza en nuestra competencia de levantamiento de pesas. Inscripciones abiertas.</p>
                </div>
            </div>
        </div>

        <!-- Evento: Clase Especial de HIIT -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="https://kingsbox.com/blog/wp-content/uploads/2024/03/HYROX-KINGSBOX.png" 
                     alt="Clase Especial de HIIT" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Clase Especial de HIIT</h3>
                    <p class="card-text">Únete a nuestra sesión de alta intensidad con entrenadores certificados. ¡Quema calorías al máximo!</p>
                </div>
            </div>
        </div>

        <!-- Evento: Seminario de Nutrición Deportiva -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="https://www.efadeporte.com/blog/wp-content/uploads/2013/05/nutricion_620x368.jpg" 
                     alt="Seminario de Nutrición Deportiva" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Seminario de Nutrición Deportiva</h3>
                    <p class="card-text">Aprende a optimizar tu dieta para mejorar tu rendimiento y alcanzar tus metas fitness.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'partials/chatbot.php'; ?>
<!-- Footer -->
<?php require 'partials/footer.view.php'; ?>

</body>
</html>