<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
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

    // A quien enviamos
    $mail->addAddress($userEmail);

    // El CC
    $mail->addAddress('energym.asir@gmail.com'); 

    $mail->isHTML(true);

    // Asunto del mail
    $mail->Subject = $_REQUEST['subject'];

    // El cuerpo
    $mail->Body = "
    Hola $userName, hemos recibido tu email sobre: \"$userBody\".<br>
    Responderemos cuanto antes. ¡Gracias por contactarnos!";

    // Intentamos enviar
    try {
        $mail->send();
        // Si fufa, a contactanos con exito
        header('Location: contactanos.php?status=success');
        exit;
    } catch (Exception $e) {
        // Si no fufa, a contactanos con error
        header('Location: contactanos.php?status=error');
        exit;
    }
}
?>
