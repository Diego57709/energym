<?php
require '../partials/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../components/phpmailer/src/Exception.php';
require '../components/phpmailer/src/PHPMailer.php';
require '../components/phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $mensaje = $_POST['mensaje'] ?? '';
    $destinatariosGrupos = $_POST['destinatarios_grupo'] ?? [];
    $destinatariosIndividuales = $_POST['destinatarios_individuales'] ?? '';

    // Verifica que se haya ingresado título, mensaje y al menos un destinatario (por grupo o individual)
    if (empty($titulo) || empty($mensaje) || (empty($destinatariosGrupos) && empty(trim($destinatariosIndividuales)))) {
        header('Location: mails_enviar.php?status=error');
        exit();
    }

    $emails = [];

    // Procesa destinatarios por grupo
    foreach ($destinatariosGrupos as $grupo) {
        if ($grupo === 'clientes') {
            $result = mysqli_query($conn, "SELECT email FROM clientes");
        } elseif ($grupo === 'trabajadores') {
            $result = mysqli_query($conn, "SELECT email FROM trabajadores");
        } elseif ($grupo === 'entrenadores') {
            $result = mysqli_query($conn, "SELECT email FROM entrenadores");
        } elseif ($grupo === 'newsletter') {
            $result = mysqli_query($conn, "SELECT correo AS email FROM newsletter");
        }
        if (isset($result) && $result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $emails[] = $row['email'];
            }
        }
    }

    // Procesa destinatarios individuales
    if (!empty(trim($destinatariosIndividuales))) {
        // Separa los emails por comas, elimina espacios en blanco y descarta valores vacíos
        $individualEmails = array_filter(array_map('trim', explode(',', $destinatariosIndividuales)));
        // Valida el formato de cada email antes de agregarlos
        foreach ($individualEmails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }
    }

    // Elimina correos duplicados
    $emails = array_unique($emails);

    // Verifica que se tenga al menos un email para enviar
    if (empty($emails)) {
        header('Location: mails_enviar.php?status=error');
        exit();
    }

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
        $mail->isHTML(true);
        $mail->Subject = $titulo;

        // Agrega la firma al mensaje
        $firma = '<br><br>Atentamente,<br><strong>El equipo de EnerGym</strong>';
        $mail->Body = $mensaje . $firma;

        // Envía el correo a cada destinatario individualmente
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
