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
        // Si falla reCAPTCHA, vuelve con error
        header('Location: contactanos.php?status=error_captcha');
        exit;
    }

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'energym.asir@gmail.com';
    $mail->Password = 'wvaz qdrj yqfm bnub';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('energym.asir@gmail.com');

    // Email, nombre y cuerpo
    $userEmail = $_REQUEST['email'];
    $userName = htmlspecialchars($_REQUEST['name']);
    $userBody = htmlspecialchars($_REQUEST['body']);

    $mail->addAddress($userEmail);
    $mail->isHTML(true);
    $mail->Subject = $_REQUEST['subject'];
    $mail->Body = "
        Hola $userName, hemos recibido tu email sobre: \"$userBody\".<br>
        Responderemos cuanto antes. ¡Gracias por contactarnos!
        <br><br>
        <p>Atentamente,<br>El equipo de EnerGym</p>";
        

    try {
        $mail->send();

        $mail->clearAddresses();
        $mail->addAddress('energym.asir@gmail.com');

        $mail->Subject = "Nuevo correo recibido de $userName";
        $mail->Body = "
            <h3>Has recibido un nuevo mensaje de contacto:</h3>
            <p><strong>Nombre:</strong> $userName</p>
            <p><strong>Email:</strong> $userEmail</p>
            <p><strong>Asunto:</strong> " . htmlspecialchars($_REQUEST['subject']) . "</p>
            <p><strong>Mensaje:</strong><br>$userBody</p>";

        $mail->send();

        // Redirigir con éxito
        header('Location: contactanos.php?status=success');
        exit;
    } catch (Exception $e) {
        // Si no fufa, a contactanos con error
        header('Location: contactanos.php?status=error');
        exit;
    }
}

?>
