<?php
// Conexión a la base de datos (ajusta según tu configuración)
require 'partials/db.php';

// Incluir PHPMailer
require 'components/phpmailer/src/Exception.php';
require 'components/phpmailer/src/PHPMailer.php';
require 'components/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Función para enviar correo usando PHPMailer
 */
function sendEmail($to, $name, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'energym.asir@gmail.com';
        $mail->Password   = 'wvaz qdrj yqfm bnub';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('energym.asir@gmail.com', 'EnerGym');
        $mail->addAddress($to, $name);

        $mail->isHTML(true);
        $mail->Subject = $subject;

        // Agrega una firma al mensaje
        $firma = '<br><br>Atentamente,<br><strong>El equipo de EnerGym</strong>';
        $mail->Body = $body . $firma;

        $mail->send();
        error_log("Correo enviado a: $to");
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo a $to: " . $mail->ErrorInfo);
        return false;
    }
}

/* ========================
   1. Recordatorio de Renovación
   ======================== */

// Consulta: clientes cuya suscripción finaliza en los próximos 7 días
$queryRecordatorio = "
    SELECT cliente_id, nombre, email, end_sub 
    FROM clientes 
    WHERE end_sub BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
";
$resultRecordatorio = mysqli_query($conn, $queryRecordatorio);
if ($resultRecordatorio) {
    while ($cliente = mysqli_fetch_assoc($resultRecordatorio)) {
        $email    = $cliente['email'];
        $nombre   = $cliente['nombre'];
        $endSub   = $cliente['end_sub'];

        // Calcula los días restantes
        $fechaFin   = new DateTime($endSub);
        $fechaActual = new DateTime();
        $diasRestantes = (int)$fechaActual->diff($fechaFin)->format("%r%a");

        if ($diasRestantes > 0 && $diasRestantes <= 7) {
            $subject = "Recordatorio: Renovación de Suscripción en EnerGym";
            $body  = "<p>Hola " . htmlspecialchars($nombre) . ",</p>";
            $body .= "<p>Tu suscripción en EnerGym finaliza en <strong>" . $diasRestantes . " día(s)</strong> (el " . date("d/m/Y", strtotime($endSub)) . ").</p>";
            $body .= "<p>Te recomendamos renovarla para seguir disfrutando de nuestros servicios.</p>";
            sendEmail($email, $nombre, $subject, $body);
        }
    }
} else {
    error_log("Error en consulta de recordatorios: " . mysqli_error($conn));
}

/* ========================
   2. Felicitación de Cumpleaños
   ======================== */

// Consulta: clientes cuyo cumpleaños es hoy (se comparan mes y día)
$queryCumple = "
    SELECT cliente_id, nombre, email, fecha_nacimiento
    FROM clientes
    WHERE DATE_FORMAT(fecha_nacimiento, '%m-%d') = DATE_FORMAT(NOW(), '%m-%d')
";
$resultCumple = mysqli_query($conn, $queryCumple);
if ($resultCumple) {
    while ($cliente = mysqli_fetch_assoc($resultCumple)) {
        $email = $cliente['email'];
        $nombre = $cliente['nombre'];
        $subject = "¡Feliz Cumpleaños de parte de EnerGym!";
        $body  = "<p>Hola " . htmlspecialchars($nombre) . ",</p>";
        $body .= "<p>En EnerGym queremos desearte un muy feliz cumpleaños. Esperamos que disfrutes de este día tan especial y que sigas cumpliendo tus metas.</p>";
        $body .= "<p>No olvides que estamos aquí para apoyarte en cada paso de tu entrenamiento.</p>";
        sendEmail($email, $nombre, $subject, $body);
    }
} else {
    error_log("Error en consulta de cumpleaños: " . mysqli_error($conn));
}
?>
