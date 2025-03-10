<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'components/phpmailer/src/Exception.php';
require 'components/phpmailer/src/PHPMailer.php';
require 'components/phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: 404.php");
    exit();
}

if (isset($_REQUEST['send'])) {
    // Validar reCAPTCHA
    $recaptchaSecret = '6LdXppgqAAAAAM_ZlbHbFC2GMPXY7gN4MGVtVDko';
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
    $responseKeys = json_decode($response, true);
    
    if (!$responseKeys['success']) {
        header('Location: contactanos.php?status=error_captcha');
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'energym.asir@gmail.com';
        $mail->Password = 'wvaz qdrj yqfm bnub';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('energym.asir@gmail.com', 'EnerGym');

        $userEmail = $_REQUEST['email'];
        $userName = htmlspecialchars($_REQUEST['name']);
        $userSubject = htmlspecialchars($_REQUEST['subject']);
        $userBody = htmlspecialchars($_REQUEST['body']);

        // 🔹 1. Enviar correo de confirmación al usuario
        $mail->addAddress($userEmail);
        $mail->isHTML(true);
        $mail->Subject = "Hemos recibido tu mensaje - EnerGym";
        $mail->Body = "
            <h3>Hola, $userName</h3>
            <p>Hemos recibido tu mensaje con el asunto: <strong>\"$userSubject\"</strong>.</p>
            <p>Mensaje enviado:</p>
            <blockquote>$userBody</blockquote>
            <p>Nos pondremos en contacto contigo lo antes posible.</p>
            <p><strong>Atentamente,</strong><br>El equipo de EnerGym</p>";

        $mail->send();  // Enviar correo al usuario

        // 🔹 2. Enviar correo con detalles al administrador
        $mail->clearAddresses();
        $mail->addAddress('energym.asir@gmail.com');

        $mail->Subject = "Nuevo mensaje recibido de $userName";
        $mail->Body = "
            <h3>Nuevo mensaje de contacto recibido:</h3>
            <p><strong>Nombre:</strong> $userName</p>
            <p><strong>Email:</strong> $userEmail</p>
            <p><strong>Asunto:</strong> $userSubject</p>
            <p><strong>Mensaje:</strong></p>
            <blockquote>$userBody</blockquote>";

        $mail->send();  // Enviar correo al administrador

        // Redirigir con éxito
        header('Location: contactanos.php?status=success');
        exit();
    } catch (Exception $e) {
        header('Location: contactanos.php?status=error');
        exit();
    }
}

?>
