<?php
include 'partials/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// Obtener los datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
// Validar datos
if (empty($nombre) || empty($correo)) {
    header("Location: index.php?status=error&message=Todos los campos son obligatorios.#newsletter");
    exit();
}

try {
    // Insertar datos en la base de datos
    $queryInsert = "INSERT INTO newsletter (nombre, correo, fecha_suscripcion) VALUES ('$nombre', '$correo', NOW())";
    if (!mysqli_query($conn, $queryInsert)) {
        throw new Exception('Error al guardar los datos en la base de datos.');
    }

    // Configurar PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'energym.asir@gmail.com';
        $mail->Password = 'wvaz qdrj yqfm bnub';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
        $mail->addAddress($correo, $nombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido a nuestra Newsletter de EnerGym!';
        $mail->Body = "
            <h2>Hola, $nombre</h2>
            <p>Gracias por suscribirte a nuestra newsletter. Ahora recibirás las últimas noticias, ofertas exclusivas y consejos de entrenamiento directamente en tu correo.</p>
            <p>¡Estamos encantados de tenerte con nosotros!</p>
            <br>
            <p>Atentamente,<br>El equipo de EnerGym</p>
        ";

        $mail->send();

        // Redirigir con éxito
        header("Location: index.php?status=success&message=Suscripción completada. Revisa tu correo para más detalles.#newsletter");
        exit();
    } catch (Exception $e) {
        // Si hay un error al enviar el correo
        throw new Exception('Suscripción completada, pero no se pudo enviar el correo.');
    }
} catch (Exception $e) {
    // Manejar errores y redirigir con mensaje de error
    $errorMessage = urlencode($e->getMessage());
    header("Location: index.php?status=error&message=$errorMessage.#newsletter");
    exit();
}
