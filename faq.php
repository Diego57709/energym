<?php
    require 'partials/header1.view.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Por qué elegir EnerGym?</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .main-img {
            position: relative;
            width: 100%;
            height: 50vh;
            background-image: url('/img/faq/faq.webp');
            background-size: cover;
            background-position: 15%;
            background-attachment: fixed;
        }

        .main-img::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); 
            z-index: 1;
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
        .main-img .text-overlay h2 {
            font-size: 1.5rem;
            font-weight: normal;
            margin: 10px 0 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        /* Estilos para FAQ */
        .faq {
            text-align: center;
            padding: 2rem;
            background-color: #f7f7f7;
        }
        .faq h2 {
            margin-bottom: 2rem;
        }
        .faq-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .faq-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            width: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            height: auto;
        }
        .faq-item h3 {
            color: #333;
            margin-top: 5px;
        }
        .faq-item p {
            margin: 0.5rem 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            .faq-item {
                width: 80%;
            }
        }
        @media (max-width: 480px) {
            .faq-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Imagen principal con atributo aria-label -->
    <div class="main-img" role="img" aria-label="Imagen de fondo de EnerGym para FAQ">
        <div class="text-overlay">
            <h1>EnerGym</h1>
            <h2>¿Quiénes somos?</h2>
        </div>
    </div>

    <!-- Contenido principal -->
    <main id="contenido-principal">
        <!-- Sección de FAQ con aria-labelledby -->
        <section class="faq" role="region" aria-labelledby="header-faq">
            <h2 id="header-faq">Preguntas Frecuentes</h2>
            <div class="faq-container">
                <?php
                    $faq = [
                        [
                            "pregunta" => "¿Qué servicios ofrece EnerGym?",
                            "respuesta" => "Ofrecemos equipos de última generación y clases dinámicas como yoga, spinning, pilates y más."
                        ],
                        [
                            "pregunta" => "¿Tienen un gimnasio para entrenamiento personal?",
                            "respuesta" => "Sí, contamos con áreas dedicadas para entrenadores personales y seguimiento individual."
                        ],
                        [
                            "pregunta" => "¿Cómo puedo inscribirme?",
                            "respuesta" => "Puedes inscribirte fácilmente a través de nuestra página web o directamente en el gimnasio."
                        ],
                        [
                            "pregunta" => "¿Cuáles son los horarios del gimnasio?",
                            "respuesta" => "Nuestro gimnasio está abierto todos los días de 6:00 - 23:30."
                        ],
                        [
                            "pregunta" => "¿Puedo llevar un invitado?",
                            "respuesta" => "Sí, puedes llevar un invitado con el plan premium de viernes a domingo."
                        ],
                        [
                            "pregunta" => "¿Ofrecen clases gratuitas para los nuevos miembros?",
                            "respuesta" => "Lastimosamente no ofrecemos clases gratuitas, pero puedes adquirir una por 9,90€ en recepción."
                        ],
                        [
                            "pregunta" => "¿Puedo congelar mi membresía?",
                            "respuesta" => "Sí, puedes congelar tu membresía por un máximo de 3 meses si no puedes asistir debido a razones personales o de salud."
                        ],
                        [
                            "pregunta" => "¿El gimnasio cuenta con estacionamiento?",
                            "respuesta" => "Sí, ofrecemos estacionamiento gratuito para nuestros miembros."
                        ]
                    ];
                    
                    foreach ($faq as $item) {
                        echo '<div class="faq-item">';
                        echo '<h3>' . $item['pregunta'] . '</h3>';
                        echo '<p>' . $item['respuesta'] . '</p>';
                        echo '</div>';
                    }
                ?>
            </div>
        </section>
    </main>
    
    <?php require 'partials/chatbot.php'; ?>
</body>
<!-- Bootstrap JS -->
</html>
<?php
    require 'partials/footer.view.php';
?>
