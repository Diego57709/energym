<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Manager') {
    header('Location: ../index.php?error=permisos_insuficientes');
    exit();
}

require '../partials/timeout.php';
require '../partials/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_destino = trim($_POST['email_destino'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    
    if (empty($email_destino) || empty($asunto) || empty($mensaje)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'energym.asir@gmail.com';
            $mail->Password = 'wvaz qdrj yqfm bnub';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
            $mail->addAddress($email_destino);

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = nl2br($mensaje);

            $mail->send();
            $success = "Correo enviado con éxito a $email_destino.";
        } catch (Exception $e) {
            $error = "Error al enviar el correo: " . $mail->ErrorInfo;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar correos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        .main {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background-color: #f8f9fa;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>
<body>
<div class="main">
    <div class="form-container">
        <h2 class="text-center mb-4">Enviar correos</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="mb-3">
                <label for="email_destino" class="form-label">Correo Destino</label>
                <input type="email" name="email_destino" id="email_destino" class="form-control" placeholder="example@domain.com" required>
            </div>
            <div class="mb-3">
                <label for="asunto" class="form-label">Asunto</label>
                <input type="text" name="asunto" id="asunto" class="form-control" placeholder="Asunto del correo" required>
            </div>
            <div class="mb-3">
                <label for="mensaje" class="form-label">Mensaje</label>
                <textarea name="mensaje" id="mensaje" class="form-control" rows="6" placeholder="Escribe tu mensaje aquí..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar</button>
        </form>
    </div>
</div>
</body>
</html>
