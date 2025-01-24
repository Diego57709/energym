<?php
require '../partials/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $mensaje = $_POST['mensaje'] ?? ''; // Captura el contenido generado por Summernote como HTML
    $destinatarios = $_POST['destinatarios'] ?? '';

    if (empty($titulo) || empty($mensaje) || empty($destinatarios)) {
        header('Location: mails_enviar.php?status=error');
        exit();
    }

    $emails = [];

    // Obtener correos según el grupo seleccionado
    if ($destinatarios === 'clientes') {
        $result = mysqli_query($conn, "SELECT email FROM clientes");
    } elseif ($destinatarios === 'trabajadores') {
        $result = mysqli_query($conn, "SELECT email FROM trabajadores");
    } elseif ($destinatarios === 'entrenadores') {
        $result = mysqli_query($conn, "SELECT email FROM entrenadores");
    } elseif ($destinatarios === 'newsletter') {
        $result = mysqli_query($conn, "SELECT correo AS email FROM newsletter");
    } elseif ($destinatarios === 'todos') {
        $result = mysqli_query($conn, "
            (SELECT email FROM clientes)
            UNION
            (SELECT email FROM trabajadores)
            UNION
            (SELECT email FROM entrenadores)
            UNION
            (SELECT correo AS email FROM newsletter)
        ");
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $emails[] = $row['email'];
        }
    }

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
        $mail->isHTML(true);
        $mail->Subject = $titulo;

        $firma = '<br><br>Atentamente,<br><strong>El equipo de EnerGym</strong>';
        $mail->Body = $mensaje . $firma;

        foreach ($emails as $email) {
            $mail->addAddress($email);
            $mail->send();
            $mail->clearAddresses();
        }

        header('Location: mails_enviar.php?status=success');
    } catch (Exception $e) {
        error_log('Error al enviar correo: ' . $e->getMessage());
        header('Location: mails_enviar.php?status=error');
    }
}
?>
