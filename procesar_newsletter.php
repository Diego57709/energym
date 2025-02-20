<?php
include 'partials/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'components/phpmailer/src/Exception.php';
require 'components/phpmailer/src/PHPMailer.php';
require 'components/phpmailer/src/SMTP.php';

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

// Insertar datos en la base de datos utilizando una consulta preparada
$stmt = mysqli_prepare($conn, "INSERT INTO newsletter (nombre, correo, fecha_suscripcion) VALUES (?, ?, NOW())");
if (!$stmt) {
    $errorMessage = urlencode("Error al preparar la consulta: " . mysqli_error($conn));
    header("Location: index.php?status=error&message=$errorMessage.#newsletter");
    exit();
}
mysqli_stmt_bind_param($stmt, "ss", $nombre, $correo);
if (!mysqli_stmt_execute($stmt)) {
    $errorMessage = urlencode('Error al guardar los datos en la base de datos.');
    header("Location: index.php?status=error&message=$errorMessage.#newsletter");
    exit();
}
mysqli_stmt_close($stmt);

// Configurar PHPMailer sin usar bloques try
// Se desactivan las excepciones pasando "false" al constructor
$mail = new PHPMailer(false);
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

// Enviar el correo y manejar error si ocurre
if (!$mail->send()) {
    $errorMessage = urlencode('Suscripción completada, pero no se pudo enviar el correo.');
    header("Location: index.php?status=error&message=$errorMessage.#newsletter");
    exit();
}

// Redirigir con éxito
header("Location: index.php?status=success&message=Suscripción completada. Revisa tu correo para más detalles.#newsletter");
exit();
?>
