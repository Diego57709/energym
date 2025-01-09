<?php
    require 'partials/header1.view.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Por qué elegir EnerGym?</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            
        }
        .main-img {
            position: relative;
            width: 100%;
            height: 50vh;
            background-image: url('https://static.vecteezy.com/system/resources/thumbnails/026/781/389/small_2x/gym-interior-background-of-dumbbells-on-rack-in-fitness-and-workout-room-photo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .main-img::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }
        .main-img .text-overlay {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
            z-index: 1;
        }
        .main-img .text-overlay h1 {
            font-size: 3rem;
            font-weight: bold;
            margin: 15px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }
        .main-img .text-overlay h2 {
            font-size: 1.5rem;
            font-weight: normal;
            margin: 10px 0 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        .overlay-inscribete {
            font-size: 1.5rem;
            color: white;
            text-transform: uppercase;
            background-color: rgb(15, 139, 141);
            padding: 0.5rem 1.25rem;
            border-radius: 5px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .overlay-inscribete:hover {
            background-color: rgb(10, 94, 95);
        }
        /* Responsividad */
        @media (max-width: 768px) {
            .main-img .text-overlay h1 {
                font-size: 2.5rem;
            }

            .main-img .text-overlay h2 {
                font-size: 1.2rem;
            }

            .overlay-inscribete {
                font-size: 1.2rem;
                padding: 0.6rem 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .main-img .text-overlay h1 {
                font-size: 2rem;
            }

            .main-img .text-overlay h2 {
                font-size: 1rem;
            }

            .overlay-inscribete {
                font-size: 1rem;
                padding: 0.5rem 1rem;
            }
        }
        /* Features Styles */
        .features {
            text-align: center;
            padding: 2rem;
            background-color: #f7f7f7;
        }
        .features h2 {
            margin-bottom: 2rem;
        }
        .features-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .feature-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            width: 22%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .feature-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        .feature-item h3 {
            margin-top: 1rem;
            color: #333;
        }
        .feature-item p {
            margin: 0.5rem 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            .feature-item {
                width: 45%;
            }
        }
        @media (max-width: 480px) {
            .feature-item {
                width: 100%;
            }
        }
        /* Newsletter Styles */
        .newsletter {
            border-top: 5px solid rgb(15, 139, 141);
            border-bottom: 5px solid rgb(15, 139, 141);
            background-color: rgb(41, 48, 53);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .newsletter h2 {
            margin-bottom: 1rem;
        }
        .newsletter form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        .newsletter input[type="text"],
        .newsletter input[type="email"] {
            width: 90%;
            max-width: 400px;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
        }
        .newsletter input[type="checkbox"] {
            margin-right: 10px;
        }
        .newsletter button {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            background-color: rgb(15, 139, 141);
            color: white;
            font-size: 1rem;
            cursor: pointer;
        }
        .newsletter button:hover {
            background-color: #feb47b;
        }
        .newsletter small {
            display: block;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #bbb;
        }
        /* Blog Section */
        .events-section {
            text-align: center;
            padding: 3rem 1.5rem;
            background-color: #f7f7f7;
        }
        .events-section h2 {
            margin-bottom: 2rem;
        }
        .events-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .events-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            width: 22%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .events-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        .events-item h3 {
            margin-top: 1rem;
            color: #333;
        }
        .events-item p {
            margin: 0.5rem 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            .events-item {
                width: 45%;
            }
        }
        @media (max-width: 480px) {
            .events-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-img">
        <div class="text-overlay">
            <h1>EnerGym</h1>
            <a href="planes.php"><span class="overlay-inscribete">¡Inscribete Ya!</span></a>
        </div>
    </div>
    <div class="features">
        <h2>¿Por qué elegir EnerGym?</h2>
        <div class="features-container">
            <div class="feature-item">
                <img src="https://elcierredigital.com/filesedc/uploads/image/post/metrpolitan-slider-espacios-03_1200_800.webp" alt="Instalaciones">
                <h3>Instalaciones Modernas</h3>
                <p>Disfruta de equipos de última generación y áreas dedicadas.</p>
            </div>
            <div class="feature-item">
                <img src="https://i.blogs.es/60be3c/24793137175_30cc0f8b76_o/1366_2000.jpg" alt="Actividades">
                <h3>Clases Dirigidas</h3>
                <p>Participa en clases dinámicas como yoga, spinning y más.</p>
            </div>
            <div class="feature-item">
                <img src="https://site.educa.madrid.org/ies.elisasorianofischer.getafe//wp-content/uploads/ies.elisasorianofischer.getafe/2022/03/relij-horarios.jpeg" alt="Horario">
                <h3>Horarios Amplios</h3>
                <p>Aprovecha nuestra apertura de 24/7 para entrenar cuando quieras.</p>
            </div>
        </div>
    </div>
    <div class="newsletter">
        <h2>Suscríbete a nuestra Newsletter</h2>
        <form action="procesar_newsletter.php" method="POST">
            <input type="text" name="nombre" placeholder="Tu nombre" required>
            <input type="email" name="correo" placeholder="Tu correo electrónico" required>
            <label>
                <input type="checkbox" name="condiciones" required>
                Acepto los <a href="https://www.iubenda.com/es/help/40472-puedo-usar-una-plantilla-de-terminos-y-condiciones" onclick="window.open(this.href, 'smallWindow', 'width=600,height=400'); return false;" style="color: rgb(15, 139, 141);">términos y condiciones</a>
            </label>
            <button type="submit">Suscribirme</button>
        </form>
        <small>Recibe las últimas noticias, ofertas y consejos de entrenamiento directamente en tu correo.</small>
    </div>
    <div class="events-section">
        <h2>Eventos próximos</h2>
        <div class="events-container">
            <div class="events-item">
                <img src="https://86982a2a87.clvaw-cdnwnd.com/46c34b2886f94a9f866428b26bb1dba1/200000561-d97a0d97a2/entrenador-fitness.png?ph=86982a2a87" alt="events 1">
                <h3>Entrenamiento para principiantes</h3>
                <p>Consejos para empezar tu rutina de entrenamiento y obtener los mejores resultados.</p>
            </div>
            <div class="events-item">
                <img src="https://www.adfisioterapiavalencia.com/wp-content/uploads/2020/07/nutricion-deportiva.jpg" alt="events 2">
                <h3>Nutrición para deportistas</h3>
                <p>Descubre cómo una dieta balanceada puede mejorar tu rendimiento físico.</p>
            </div>
            <div class="events-item">
                <img src="https://hips.hearstapps.com/hmg-prod/images/yoga-1638533114.jpg?crop=0.6666666666666666xw:1xh;center,top&resize=1200:*" alt="events 3">
                <h3>Los beneficios del yoga</h3>
                <p>Aprende cómo el yoga puede ser una excelente adición a tu rutina de entrenamiento.</p>
            </div>
        </div>
    </div>
</body>
<?php
    require 'partials/footer.view.php';
?>
</html>

