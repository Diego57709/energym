<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        footer {
            background-color: #1f2429;
            color: white;
            padding-top: 20px;
            border-top: 5px solid rgb(15, 139, 141);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .footer-section h3 {
            color: rgb(15, 139, 141);
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-section a {
            text-decoration: none;
            color: #b0b0b0;
            font-size: 16px;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .footer-section a:hover {
            color: #FF5722;
            transform: translateX(5px);
        }

        .footer-bottom {
            text-align: center;
            font-size: 14px;
            background-color: #141a1f;
            padding: 20px;
            border-top: 1px solid rgb(15, 139, 141);
        }

        @media (max-width: 768px) {
            .footer-section {
                text-align: center;
            }
        }
    </style>
</head>

<body>

<footer>
    <div class="container py-5">
        <div class="row">
            <!-- Links Útiles -->
            <div class="col-md-3 col-sm-6 footer-section">
                <h3>Links Útiles</h3>
                <ul class="list-unstyled">
                    <li><a href="/index.php">Home</a></li>
                    <li><a href="/nosotros.php">Sobre Nosotros</a></li>
                    <li><a href="/servicios.php">Servicios</a></li>
                    <li><a href="/contactanos.php">Contactanos</a></li>
                    <li><a href="/faq.php">FAQ</a></li>
                </ul>
            </div>

            <!-- Links -->
            <div class="col-md-3 col-sm-6 footer-section">
                <h3>Links</h3>
                <ul class="list-unstyled">
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Política de privacidad</a></li>
                    <li><a href="#">Términos de servicio</a></li>
                    <li><a href="#">Política de reembolsos</a></li>
                </ul>
            </div>

            <!-- Contactanos -->
            <div class="col-md-3 col-sm-6 footer-section">
                <h3>Redes Sociales</h3>
                <ul class="list-unstyled">
                    <li><a href="https://www.facebook.com">Facebook</a></li>
                    <li><a href="https://www.instagram.com">Instagram</a></li>
                    <li><a href="https://www.x.com">Twitter</a></li>
                    <li><a href="https://www.linkedin.com">LinkedIn</a></li>
                </ul>
            </div>

            <!-- Dirección -->
            <div class="col-md-3 col-sm-6 footer-section">
                <h3>Donde estamos</h3>
                <p>C/ José María Roquero 4</p>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <p>&copy; 2024 EnerGym. All Rights Reserved.</p>
    </div>
</footer>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
