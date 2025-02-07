<?php
declare(strict_types=1);
include '../db.php'; // Conexión a la base de datos

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Token del bot de Telegram
include 'token.php';

// ENCRYPTION KEY
include '../../partials/encrypt.php';
// Cargar el autoload de Composer y las librerías
require_once __DIR__ . '/../../components/vendor/autoload.php';
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Función para desencriptar datos utilizando AES-256-CBC
function decryptData($encryptedData) {
    $cipher = "aes-256-cbc";
    $data = base64_decode($encryptedData);
    $iv_length = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $iv_length);
    $encryptedText = substr($data, $iv_length);
    return openssl_decrypt($encryptedText, $cipher, ENCRYPTION_KEY, 0, $iv);
}

// Obtener la actualización enviada por Telegram
$input = file_get_contents("php://input");
$update = json_decode($input, true);

// Registrar la actualización en un log para depuración
file_put_contents('bot_debug.log', "Received input: " . print_r($update, true), FILE_APPEND);

if (isset($update['message'])) {
    // Obtener el chat_id y el texto del mensaje enviado por el usuario
    $chat_id = $update['message']['chat']['id'];
    $message = trim($update['message']['text'] ?? '');

    // Procesar el comando /start con cliente_id encriptado
    if (strpos($message, '/start') === 0) {
        // Extraer el cliente_id encriptado del mensaje
        $encrypted_cliente_id = trim(str_replace('/start', '', $message));

        // Desencriptar el cliente_id
        $cliente_id = decryptData($encrypted_cliente_id);
        file_put_contents('bot_debug.log', "Decrypted Client ID: " . $cliente_id . "\n", FILE_APPEND);

        // Validar que el ID desencriptado es válido y numérico
        if ($cliente_id && is_numeric($cliente_id)) {
            // Guardar el chat_id en la base de datos asociado al cliente_id
            $sql = "UPDATE clientes SET chat_id = ? WHERE cliente_id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, 'si', $chat_id, $cliente_id);
                if (mysqli_stmt_execute($stmt)) {
                    sendMessage($chat_id, "✅ Has conectado correctamente tu cuenta con Telegram.");
                } else {
                    logAndNotifySupport($chat_id, "Fallo en la ejecución del query: " . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);
            } else {
                logAndNotifySupport($chat_id, "Fallo en el query: " . mysqli_error($conn));
            }
        } else {
            logAndNotifySupport($chat_id, "El ID ha sido modificado.");
        }
    }

    // Mostrar el menú cada vez que se envía un mensaje
    sendMenu($chat_id);
}

// Manejar los clics en los botones (callbacks) de Telegram
if (isset($update['callback_query'])) {
    // Obtener la información del callback
    $callback_id = $update['callback_query']['id'];
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $data = $update['callback_query']['data'];

    if ($data === "generate_qr") {
        // Si se solicita generar el QR, llamar a la función correspondiente
        generateQRCode($chat_id);
    }

    // Confirmar a Telegram la acción para quitar el estado de "cargando"
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/answerCallbackQuery";
    file_get_contents($url . '?' . http_build_query(["callback_query_id" => $callback_id]));

    // Reenviar el menú para mantener visible el botón
    sendMenu($chat_id);
}

// Función para enviar mensajes a los usuarios de Telegram
function sendMessage($chat_id, $text) {
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    $data = ['chat_id' => $chat_id, 'text' => $text];

    file_get_contents($url . '?' . http_build_query($data));
}

// Función para enviar el menú con la opción "Generar QR"
function sendMenu($chat_id) {
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    
    // Configuración del teclado inline de Telegram
    $keyboard = [
        "inline_keyboard" => [
            [
                ["text" => "📲 Generar QR de acceso", "callback_data" => "generate_qr"]
            ]
        ]
    ];

    $data = [
        "chat_id" => $chat_id,
        "text" => "🏋️ *Acceso al gimnasio* \nPresiona el botón para generar tu QR.",
        "parse_mode" => "Markdown",
        "reply_markup" => json_encode($keyboard)
    ];

    file_get_contents($url . '?' . http_build_query($data));
}

// Función para generar y enviar un código QR
function generateQRCode($chat_id) {
    global $conn;

    // Registrar en el log la solicitud de generación de QR
    file_put_contents('bot_debug.log', "[QR] Received request from chat_id: {$chat_id}\n", FILE_APPEND);

    // Obtener el ID del usuario desde la base de datos usando el chat_id
    $sql = "SELECT cliente_id FROM clientes WHERE chat_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $chat_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id_usuario);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    if (!$id_usuario) {
        // Si no se encuentra el usuario, registrar el error y notificarlo al usuario
        file_put_contents('bot_debug.log', "[QR ERROR] No user found for chat_id: {$chat_id}\n", FILE_APPEND);
        sendMessage($chat_id, "⚠️ No se encontró tu cuenta en el sistema. Contacta a soporte: energym.asir@gmail.com.");
        return;
    }

    file_put_contents('bot_debug.log', "[QR] User ID found: {$id_usuario}\n", FILE_APPEND);

    // Generar un nuevo token para el QR
    $qrToken = bin2hex(random_bytes(16));
    $updateTokenSql = "UPDATE clientes SET qr_token = '$qrToken' WHERE cliente_id = '$id_usuario'";

    if (!mysqli_query($conn, $updateTokenSql)) {
        file_put_contents('bot_debug.log', "[QR ERROR] Failed to update qr_token in database: " . mysqli_error($conn) . "\n", FILE_APPEND);
        sendMessage($chat_id, "⚠️ Hubo un error al generar tu QR. Contacta a soporte.");
        return;
    }

    file_put_contents('bot_debug.log', "[QR] Token updated successfully for user ID: {$id_usuario}\n", FILE_APPEND);

    // Generar el código QR utilizando la librería chillerlan/php-qrcode
    try {
        $options = new QROptions([
            'version' => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
        ]);

        $qrcode = new QRCode($options);
        // URL que se codifica en el QR, incluyendo el token y el cliente_id
        $url = "https://energym.ddns.net/qr_verificacion.php?token={$qrToken}&cliente_id={$id_usuario}";

        // Ruta donde se guardará el código QR generado
        $qr_path = __DIR__ . "/qr_codes/{$chat_id}.png";
        file_put_contents('bot_debug.log', "[QR] Saving QR to: {$qr_path}\n", FILE_APPEND);

        // Guardar el código QR como imagen PNG
        $qrcode->render($url, $qr_path);

        // Verificar que el archivo se haya guardado correctamente
        if (!file_exists($qr_path) || filesize($qr_path) == 0) {
            file_put_contents('bot_debug.log', "[QR ERROR] QR file was not saved at {$qr_path}\n", FILE_APPEND);
            sendMessage($chat_id, "⚠️ Error: el QR no se guardó correctamente.");
            return;
        }

        file_put_contents('bot_debug.log', "[QR] QR Code correctly saved at {$qr_path}\n", FILE_APPEND);

    } catch (Exception $e) {
        // Registrar y notificar cualquier excepción al generar el QR
        file_put_contents('bot_debug.log', "[QR ERROR] Failed to save PNG: " . $e->getMessage() . "\n", FILE_APPEND);
        sendMessage($chat_id, "⚠️ Error al generar el código QR.");
        return;
    }

    // Enviar el código QR generado al usuario
    sendPhoto($chat_id, "Aquí está tu código QR para acceder al gimnasio:", $qr_path);
}

// Función para enviar una imagen (código QR) a través de Telegram
function sendPhoto($chat_id, $caption, $photo_path) {
    file_put_contents('bot_debug.log', "[SEND PHOTO] Preparing to send photo from: {$photo_path}\n", FILE_APPEND);

    // Verificar que el archivo de imagen existe
    if (!file_exists($photo_path)) {
        file_put_contents('bot_debug.log', "[SEND PHOTO ERROR] File does not exist: {$photo_path}\n", FILE_APPEND);
        sendMessage($chat_id, "⚠️ Error: el archivo de QR no fue encontrado.");
        return;
    }

    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendPhoto";

    // Configurar los campos para la solicitud a la API de Telegram
    $post_fields = [
        'chat_id' => $chat_id,
        'caption' => $caption,
        'photo' => new CURLFile(realpath($photo_path))
    ];

    // Inicializar cURL y enviar la solicitud
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // Registrar la respuesta de Telegram para depuración
    file_put_contents('bot_debug.log', "[SEND PHOTO] Telegram response code: {$http_code}\n", FILE_APPEND);
    file_put_contents('bot_debug.log', "[SEND PHOTO] Telegram response: {$response}\n", FILE_APPEND);

    if ($error) {
        file_put_contents('bot_debug.log', "[SEND PHOTO ERROR] cURL Error: {$error}\n", FILE_APPEND);
        sendMessage($chat_id, "⚠️ Hubo un error al enviar el QR.");
    } else {
        file_put_contents('bot_debug.log', "[SEND PHOTO] QR sent successfully to chat_id: {$chat_id}\n", FILE_APPEND);
    }
}

// Función para registrar errores y notificar al soporte al usuario
function logAndNotifySupport($chat_id, $errorMessage) {
    file_put_contents('bot_debug.log', "Error: " . $errorMessage . "\n", FILE_APPEND);
    sendMessage($chat_id, "⚠️ Hubo un error generando tu QR. Contacta a soporte: energym.asir@gmail.com.");
}
?>
