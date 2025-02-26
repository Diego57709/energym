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
            color: white;
            text-align: center;
            z-index: 2;
            background: rgba(0, 0, 0, 0.4); /* fondo semitransparente */
            padding: 1rem;
            border-radius: 8px;
        }
        .main-img .text-overlay h1 {
            font-size: 3rem;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
            color: #fff;
        }
        .overlay-inscribete {
            background-color: rgb(15, 139, 141) !important;
            padding: 0.65rem 1.4rem !important;
            color: white !important;
            font-weight: bold !important;
            font-size: 20px !important;
        }
        .overlay-inscribete:hover {
            background-color: rgb(10, 94, 95);
        }
        .card-img-top {
            object-fit: cover;
            height: 200px; /* Asegura una altura consistente */
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        /* Estilos para el enlace de salto */
        .skip-link:focus {
            position: absolute;
            top: 0;
            left: 0;
            background: #fff;
            color: #000;
            padding: 8px;
            z-index: 1000;
        }
        /* Mejoras de contraste para cumplir WCAG 2.1 */
        .btn-primary {
            background-color: #0056b3 !important;
            border-color: #0056b3 !important;
            color: white !important;
        }
        a {
            color: #0056b3;
            text-decoration: underline;
        }
        .card-title {
            color: #333;
        }
        .text-overlay h1 {
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }
        /* Foco visible para elementos interactivos */
        a:focus, button:focus, input:focus {
            outline: 3px solid #0078d7;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <!-- Enlace para saltar al contenido principal -->
    <a href="#contenido-principal" class="visually-hidden-focusable skip-link">Saltar al contenido principal</a>

    <main id="contenido-principal">
        <!-- Sección de Imagen Principal -->
        <div class="main-img" role="img" aria-label="Imagen de fondo con ambiente de gimnasio">
            <div class="text-overlay">
                <h1>EnerGym</h1>
                <a href="planes.php" class="btn overlay-inscribete mt-3 d-inline-block" aria-label="Inscríbete a EnerGym ahora">¡Inscríbete Ya!</a>
            </div>
        </div>

        <!-- Sección de Características -->
        <section aria-labelledby="header-caracteristicas">
            <div class="container py-5">
                <h2 id="header-caracteristicas" class="text-center mb-4">¿Por qué elegir EnerGym?</h2>
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-lg h-100" tabindex="0">
                            <img src="https://elcierredigital.com/filesedc/uploads/image/post/metrpolitan-slider-espacios-03_1200_800.webp" alt="Modernas instalaciones de EnerGym con equipos de última generación" class="card-img-top">
                            <div class="card-body">
                                <h3 class="card-title">Instalaciones Modernas</h3>
                                <p class="card-text">Disfruta de equipos de última generación y áreas dedicadas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-lg h-100" tabindex="0">
                            <img src="https://i.blogs.es/60be3c/24793137175_30cc0f8b76_o/1366_2000.jpg" alt="Clase de spinning dirigida por un instructor en EnerGym" class="card-img-top">
                            <div class="card-body">
                                <h3 class="card-title">Clases Dirigidas</h3>
                                <p class="card-text">Participa en clases dinámicas como yoga, spinning y más.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-lg h-100" tabindex="0">
                            <img src="https://site.educa.madrid.org/ies.elisasorianofischer.getafe//wp-content/uploads/ies.elisasorianofischer.getafe/2022/03/relij-horarios.jpeg" alt="Reloj mostrando horarios de apertura extendidos 24/7" class="card-img-top">
                            <div class="card-body">
                                <h3 class="card-title">Horarios Amplios</h3>
                                <p class="card-text">Aprovecha nuestra apertura 24/7 para entrenar cuando quieras.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Newsletter -->
        <section aria-labelledby="header-newsletter" id="newsletter" class="bg-dark text-white py-5 custom-border p-4" style="border-top: 5px solid rgb(15, 139, 141); border-bottom: 5px solid rgb(15, 139, 141)">
            <div class="container">
                <h2 id="header-newsletter">Suscríbete a nuestra Newsletter</h2>
                <?php if (isset($_GET['status'])): ?>
                    <div class="alert <?php echo $_GET['status'] === 'success' ? 'alert-success' : 'alert-danger'; ?> mt-3" role="alert">
                        <?php echo htmlspecialchars($_GET['message']); ?>
                    </div>
                <?php endif; ?>
                <form action="procesar_newsletter.php#newsletter" method="POST" class="row g-3" aria-labelledby="header-newsletter">
                    <div class="col-md-6">
                        <label for="nombre">Tu nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required aria-required="true">
                    </div>
                    <div class="col-md-6">
                        <label for="correo">Tu correo electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control" required aria-required="true">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" id="condiciones" name="condiciones" class="form-check-input" required aria-required="true">
                            <label for="condiciones" class="form-check-label">
                                Acepto los <a href="https://www.iubenda.com/es/help/40472-puedo-usar-una-plantilla-de-terminos-y-condiciones" target="_blank" rel="noopener noreferrer" style="color: #5cb3ff;">términos y condiciones</a>
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary mt-3">Suscribirme</button>
                    </div>
                    <p class="fs-8">Recibe las últimas noticias, ofertas y consejos de entrenamiento directamente en tu correo.</p>
                </form>
            </div>
        </section>

        <!-- Sección de Eventos -->
        <section aria-labelledby="header-eventos">
            <div class="container py-5">
                <h2 id="header-eventos" class="text-center mb-4">Eventos Próximos</h2>
                <div class="row">
                    <!-- Evento: Competencia de Levantamiento de Pesas -->
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-lg h-100" tabindex="0">
                            <img src="https://i.ytimg.com/vi/YuK9FUj_3NE/maxresdefault.jpg" 
                                 alt="Competencia de levantamiento de pesas con participantes en acción" class="card-img-top">
                            <div class="card-body">
                                <h3 class="card-title">Competencia de Levantamiento</h3>
                                <p class="card-text">Demuestra tu fuerza en nuestra competencia de levantamiento de pesas. Inscripciones abiertas.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Evento: Clase Especial de HIIT -->
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-lg h-100" tabindex="0">
                            <img src="https://kingsbox.com/blog/wp-content/uploads/2024/03/HYROX-KINGSBOX.png" 
                                 alt="Clase de entrenamiento HIIT con grupo de personas ejercitándose" class="card-img-top">
                            <div class="card-body">
                                <h3 class="card-title">Clase Especial de HIIT</h3>
                                <p class="card-text">Únete a nuestra sesión de alta intensidad con entrenadores certificados. ¡Quema calorías al máximo!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Evento: Seminario de Nutrición Deportiva -->
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-lg h-100" tabindex="0">
                            <img src="https://www.efadeporte.com/blog/wp-content/uploads/2013/05/nutricion_620x368.jpg" 
                                 alt="Seminario de nutrición deportiva con alimentos saludables y guía nutricional" class="card-img-top">
                            <div class="card-body">
                                <h3 class="card-title">Seminario de Nutrición Deportiva</h3>
                                <p class="card-text">Aprende a optimizar tu dieta para mejorar tu rendimiento y alcanzar tus metas fitness.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require 'partials/chatbot.php'; ?>
    <!-- Footer -->
    <?php require 'partials/footer.view.php'; ?>

</body>
</html>
