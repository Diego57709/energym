<?php include 'partials/header1.view.php'; 
session_start();?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | EnerGym</title>

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
        .form-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .form-container input,
        .form-container textarea {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            background-color: rgb(15, 139, 141);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            display: hidden;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        .form-container button:hover {
            background-color: rgb(10, 94, 95);
        }
        .contact-info {
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .contact-info h4 {
            margin-bottom: 20px;
        }
        .status-message {
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
        }
        .status-message.success {
            color: green;
        }
        .status-message.error {
            color: red;
        }
        /* reCAPTCHA widget */
        .g-recaptcha {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .text-center {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
    </style>
</head>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<body>

<!-- Image Section -->
<div class="main-img">
    <div class="text-overlay">
        <h1>¿Tienes alguna duda?</h1>
        <h4>Contactanos</h4>
    </div>
</div>

<!-- Info y Formulario -->
<div class="container py-5">
    <div class="row justify-content-center">
        <!-- Información de contacto -->
        <div class="col-md-4">
            <div class="contact-info">
                <h4>Contáctanos</h4>
                <p><strong>Teléfono:</strong> +34 123 456 789</p>
                <p><strong>Email:</strong> energym.asir@gmail.com</p>
                <p><strong>Ubicación:</strong> Calle José María Roquero 4, Madrid, España</p>
                <p><strong>Horario:</strong> Lunes a Viernes: 9:00 - 20:00</p>
            </div>
        </div>
        <!-- Formulario -->
        <div class="col-md-8">
            <div class="form-container">
            <h2 class="text-center mb-4">¡Ponte en contacto con nosotros!</h2>
                <form action="procesar_email.php" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Asunto</label>
                        <input type="text" id="subject" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="body" class="form-label">Mensaje</label>
                        <textarea id="body" name="body" class="form-control" rows="5" required></textarea>
                    </div>
                    <!-- reCAPTCHA v2 Widget -->
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="6LdXppgqAAAAABjdITCe1V1ghsaScK3ZzoStcxQw" data-callback="enableSubmitButton"></div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-lg" name="send" id="submit-btn">Enviar Mensaje</button>
                    </div>
                </form>
                <!-- Verificar si hay un mensaje de éxito o error -->
                <?php if (isset($_GET['status'])): ?>
                    <div class="alert <?= ($_GET['status'] == 'success') ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= ($_GET['status'] == 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                        <?= ($_GET['status'] == 'success') 
                            ? "El correo fue enviado correctamente." 
                            : "Hubo un error al enviar el correo. Por favor, intenta nuevamente."; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require 'partials/chatbot.php'; ?>

<script>
    // Submit despues del CATPCHA
    function enableSubmitButton() {
        var submitButton = document.getElementById('submit-btn');
        var recaptcha = document.querySelector('.g-recaptcha');

        // Animacion del boton
        submitButton.style.display = 'block';
        setTimeout(function() {
            submitButton.style.opacity = 1;
        }, 100);
        recaptcha.style.display = 'none';
    }
</script>

<?php require 'partials/footer.view.php'; ?> 
</body>
</html>
