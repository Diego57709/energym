<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style type="text/css">
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        footer {
            background-color: #1f2429;
            color: white;
            padding-top: 20px;
            text-align: left;
            border-top: 5px solid rgb(15, 139, 141);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-section {
            flex: 1;
            min-width: 250px;
            max-width: 300px;
            margin-bottom: 20px;
        }

        .footer-section h3 {
            color: rgb(15, 139, 141);
            font-size: 22px;
            margin-bottom: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
            margin-left: 5px;
        }

        .footer-section ul li {
            margin: 12px 0;
        }

        .footer-section ul li a {
            text-decoration: none;
            color: #b0b0b0;
            font-size: 16px;
            transition: color 0.3s ease, transform 0.2s ease;
            display: inline-block;
        }

        .footer-section ul li a:hover {
            color: #FF5722;
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            font-size: 14px;
            color: #b0b0b0;
            background-color: #141a1f;
            margin-top: 20px;
            padding: 20px;
            border-top: 1px solid rgb(15, 139, 141);
            letter-spacing: 0.5px;
            clear: both;
            width: 100%;
        }

        .footer-bottom p {
            margin: 0;
        }

        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .footer-section {
                margin-bottom: 20px;
                min-width: 200px;
                max-width: 100%;
            }

            .footer-bottom {
                padding: 25px 0;
            }
        }
    </style>
</head>
<body>
<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3>Links utiles</h3>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Sobre Nosotros</a></li>
                <li><a href="#">Servicios</a></li>
                <li><a href="#">Contactanos</a></li>
                <li><a href="#">FAQ</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Links</h3>
            <ul>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Política de privacidad</a></li>
                <li><a href="#">Terminos de servicio</a></li>
                <li><a href="#">Política de reembolsos</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contactanos</h3>
            <ul>
                <li><a href="#">Facebook</a></li>
                <li><a href="#">Instagram</a></li>
                <li><a href="#">Twitter</a></li>
                <li><a href="#">LinkedIn</a></li>
                <li><a href="#">Email</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Donde estámos</h3>
            <p>C/ José María Roquero 4</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 EnerGym. All Rights Reserved.</p>
    </div>
</footer>
</body>
</html>
