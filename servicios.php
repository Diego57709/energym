<?php
    require 'partials/header1.view.php';
?>
<!DOCTYPE html>
<html lang="en">
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
            background-image: url('/img/servicios/entrenador.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            filter: brightness(0.7);
        }
        .main-img .text-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
        }
        .main-img .text-overlay h1 {
            font-size: 3rem;
            font-weight: bold;
            margin: 0;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }
        .main-img .text-overlay h2 {
            font-size: 1.5rem;
            font-weight: normal;
            margin: 10px 0 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        /* Us Styles */
        .us {
            text-align: center;
            padding: 2rem;
            background-color: #f7f7f7;
        }
        .us h2 {
            margin-bottom: 2rem;
        }
        .us-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .us-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            width: 22%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .us-item img {
            width: 80%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
        }
        .us-item h3 {
            margin-top: 1rem;
            color: #333;
        }
        .us-item p {
            margin: 0.5rem 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            .us-item {
                width: 45%;
            }
        }
        @media (max-width: 480px) {
            .us-item {
                width: 100%;
            }
        }
        /* History Styles */
        .history {
            text-align: center;
            padding: 2rem;
            background-color: rgb(41, 48, 53);
            border-top: 5px solid rgb(15, 139, 141);
            border-bottom: 5px solid rgb(15, 139, 141);
        }
        .history h2 {
            margin-bottom: 2rem;
            color: white;
        }
        .history-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .history-text {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            width: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: left; 
        }

        .history-img {
            width: 30%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .history-img img {
            width: 100%;
            height: auto; 
            object-fit: cover;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .history-text, .history-img {
                width: 90%;
            }
        }

        @media (max-width: 480px) {
            .history-text, .history-img {
                width: 100%;
            }
        }
        /* Trainers Styles */
        .trainers {
            text-align: center;
            padding: 2rem;
        }
        .trainers h2 {
            margin-bottom: 2rem;
        }
        .trainers-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .trainers-item {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            width: 15%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .trainers-item img {
            width: 90%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        .trainers-item h3 {
            margin-top: 1rem;
            color: #333;
        }
        .trainers-item p {
            margin: 0.5rem 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            .trainers-item {
                width: 50%;
            }
        }
        @media (max-width: 480px) {
            .trainers-item {
                width: 100%;
            }
        }

    </style>
</head>
<body>
    <div class="main-img">
        <div class="text-overlay">
            <h1>EnerGym</h1>
            <h2>¿Quienes somos?</h2>
        </div>
    </div>
    <div class="us">
        <h2>Fundadores</h2>
        <div class="us-container">
            <div class="us-item">
                <img src="/img/nosotros/berro.jpg" alt="founder">
                <h3>Pablo Berrocal</h3>
                <p>Disfruta de equipos de última generación y áreas dedicadas.</p>
            </div>
            <div class="us-item">
                <img src="/img/nosotros/diego.webp" alt="founder">
                <h3>Diego Chuhan Li</h3>
                <p>Participa en clases dinámicas como yoga, spinning y más.</p>
            </div>
        </div>
    </div>
    <div class="history">
        <h2>¿Por qué un gimnasio?</h2>
        <div class="history-container">
            <div class="history-text">
                <h3>Nuestra historia</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sed eros vitae turpis efficitur interdum et sollicitudin augue. Etiam at sollicitudin enim. Integer vehicula odio vel consequat blandit. Quisque vel pharetra libero, a ornare mi.
                <br><br>
                In porta interdum egestas. In hac habitasse platea dictumst. Proin malesuada purus quis risus elementum, nec vulputate eros euismod. Integer congue accumsan diam, in fermentum magna commodo ut. Ut vehicula lobortis accumsan. Praesent et lobortis lorem. Praesent vel sagittis purus. Donec sagittis nulla eu orci porta tristique. Nullam mauris orci, malesuada id sapien eu, tempus lacinia lorem. Sed augue sem, blandit ut rhoncus ac, tristique eget justo. Aenean eu orci auctor, varius ex in, tempor dui.
                <br><br>
                Sed at tellus vel ante elementum vulputate vehicula vel nibh. Praesent lacinia sodales ante. Vestibulum massa metus, tincidunt molestie eros non, interdum efficitur urna. Phasellus at felis nec ex congue sollicitudin. Cras id placerat erat. Quisque blandit nunc id metus varius posuere. Ut varius nunc id arcu finibus, non accumsan sem euismod. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam risus ipsum, tincidunt vel libero a, molestie congue libero. Mauris vestibulum nibh eget aliquam suscipit.
                </p>
            </div>
            <div class="history-img">
                <img src="/img/nosotros/lake.webp" alt="img-history">
            </div>
        </div>
    </div>
    <div class="trainers">
        <h2>Nuestra plantilla</h2>
        <div class="trainers-container">
            <div class="trainers-item">
                <img src="/img/nosotros/diego2.webp" alt="trainer">
                <h3>Doraemon</h3>
                <p>Disfruta de equipos de última generación y áreas dedicadas.</p>
            </div>
            <div class="trainers-item">
                <img src="/img/nosotros/jari.webp" alt="trainer">
                <h3>Jareño</h3>
                <p>Participa en clases dinámicas como yoga, spinning y más.</p>
            </div>
            <div class="trainers-item">
                <img src="/img/nosotros/jose.webp" alt="trainer">
                <h3>Jose</h3>
                <p>Participa en clases dinámicas como yoga, spinning y más.</p>
            </div>
            <div class="trainers-item">
                <img src="/img/nosotros/cristian.webp" alt="trainer">
                <h3>Cristian</h3>
                <p>Participa en clases dinámicas como yoga, spinning y más.</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
    require 'partials/footer.view.php';
?>
