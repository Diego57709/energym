<?php require 'partials/header1.view.php'; ?> 
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
            width: 100%;
            height: 200px;
            margin-bottom: 15px;
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

<!-- Fundadores Section -->
<div class="container py-5">
    <h2 class="text-center mb-4">Fundadores</h2>
    <div class="row justify-content-center">
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="/img/nosotros/berro.jpg" alt="Instalaciones" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Pablo Berrocal</h3>
                    <p class="card-text">Disfruta de equipos de última generación y áreas dedicadas.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="/img/nosotros/diego.webp" alt="Actividades" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Diego Chuhan Li</h3>
                    <p class="card-text">Participa en clases dinámicas como yoga, spinning y más.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Personal Section -->
<div class="bg-dark text-white py-5 custom-border p-4" style="border-top: 5px solid rgb(15, 139, 141); border-bottom: 5px solid rgb(15, 139, 141)">
    <div class="container py-5">
        <h2 class="text-center mb-4">¿Por qué un gimnasio?</h2>
        <div class="row align-items-center">
            <!-- Texto explicativo encapsulado -->
            <div class="col-md-6">
                <div class="bg-white text-dark p-4 rounded shadow">
                    <p class="text-justify">
                        EnerGym es más que un gimnasio: es tu compañero en el camino hacia un estilo de vida saludable. 
                        Ofrecemos instalaciones modernas, clases grupales dinámicas y asesoramiento personalizado para 
                        que alcances tus metas. Nuestro compromiso es ayudarte a sentirte bien contigo mismo, mientras 
                        disfrutas de un ambiente inclusivo y motivador.
                    </p>
                    <p class="text-justify">
                        Únete a EnerGym y experimenta la diferencia de entrenar con un equipo apasionado que está aquí 
                        para apoyarte en cada paso del camino.
                    </p>
                </div>
            </div>
            <!-- Imagen -->
            <div class="col-md-6">
                <div class="img-container">
                    <img src="https://static.vecteezy.com/system/resources/previews/026/781/389/large_2x/gym-interior-background-of-dumbbells-on-rack-in-fitness-and-workout-room-photo.jpg" 
                        alt="Imagen del gimnasio" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Plantilla Section -->
<div class="container py-5">
    <h2 class="text-center mb-4">Nuestra plantilla</h2>
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="img/nosotros/billetes.webp" alt="Billetes" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Billetes</h3>
                    <p class="card-text">º sesiones personalizadas para alcanzar tus objetivos.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="/img/nosotros/jari.webp" alt="Actividades" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Jareño</h3>
                    <p class="card-text">Recibe asesoría para complementar tu entrenamiento con una dieta saludable.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="/img/nosotros/jose.webp" alt="Horario" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Jose</h3>
                    <p class="card-text">Aprovecha nuestra apertura 24/7 para entrenar cuando quieras.</p>
                </div>
            </div>
        </div>
        <!-- New columns added below -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card shadow-lg">
                <img src="/img/nosotros/cristian.webp" alt="Card 4" class="card-img-top">
                <div class="card-body">
                    <h3 class="card-title">Cristian</h3>
                    <p class="card-text">Mejora tu resistencia y salud cardiovascular con nuestra oferta de clases.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require 'partials/chatbot.php'; ?>
<?php require 'partials/footer.view.php'; ?> 
</body>
</html>
