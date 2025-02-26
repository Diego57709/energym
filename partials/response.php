<?php
// Habilitar reporte de errores (solo para desarrollo, quítalo en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurar archivo de log personalizado
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/chatbot.log');

require "../components/vendor/autoload.php";

// Función para añadir entradas al archivo de log
function logMessage($message) {
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message);
}

logMessage("Iniciando procesamiento de solicitud de chatbot");

// Obtener el JSON enviado
$input = file_get_contents("php://input");
logMessage("Input recibido: " . $input);
$data = json_decode($input);
$userMessage = $data->text ?? '';
$botName = $data->botName ?? 'Lenny'; // Valor por defecto si no se envía nombre

logMessage("Mensaje del usuario: " . $userMessage);
logMessage("Nombre del bot: " . $botName);

// Función para generar respuestas de redirección basadas en palabras clave
function generateRedirectResponse($text) {
    $lowerText = strtolower($text);
    if (strpos($lowerText, 'inicio') !== false || strpos($lowerText, 'home') !== false) {
        return 'Puedes visitar nuestra <a href="/index.php">página de inicio</a>.';
    }
    if (strpos($lowerText, 'nosotros') !== false) {
        return 'Consulta más sobre nosotros aquí: <a href="/nosotros.php">Sobre Nosotros</a>.';
    }
    if (strpos($lowerText, 'servicios') !== false) {
        return 'Mira la lista completa de servicios en nuestra <a href="/servicios.php">sección de servicios</a>.';
    }
    if (strpos($lowerText, 'contacto') !== false || strpos($lowerText, 'contactarnos') !== false) {
        return 'Puedes contactarnos a través de <a href="/contactanos.php">esta página</a>.';
    }
    if (strpos($lowerText, 'faq') !== false || strpos($lowerText, 'preguntas frecuentes') !== false) {
        return 'Encuentra respuestas en nuestra <a href="/faq.php">sección de preguntas frecuentes</a>.';
    }
    return false;
}

// Si se detecta alguna palabra clave para redirección, se envía esa respuesta
$redirectResponse = generateRedirectResponse($userMessage);
if ($redirectResponse) {
    logMessage("Respuesta de redirección generada: " . $redirectResponse);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['response' => $redirectResponse], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Contexto del sistema y del bot - mejorado para claridad y estructura
$systemPrompt = "Eres $botName, el asistente virtual de EnerGym. Tu función es proporcionar información clara y precisa sobre el gimnasio, sus servicios y planes de membresía.

Reglas:
1. **Tono**: Profesional, claro y amigable.
2. **Identidad**: Siempre preséntate como el asistente de EnerGym. No preguntes '¿Quién eres?' ni permitas que el usuario asuma tu rol.
3. **Formato**:
   - Responde en párrafos breves.
   - Usa viñetas para organizar información.
   - Prioriza respuestas directas y relevantes.

Información de EnerGym:

🕒 **Horarios**:  
- Abierto de 6:00 a 23:30 todos los días.

🏋️ **Planes de Membresía**:
- **Comfort (€19,99 promo | €24,99 regular)**:  
  - Acceso a clases con reserva (36 h de antelación).
  - Planes de entrenamiento personalizados en la app.
  - YONGO Sports Water por €3,90.
  - **Sin cuota de inscripción.**
- **Premium (€25,99 promo | €29,99 regular)**:  
  - Todo lo del plan Comfort.
  - Reserva de hasta 2 clases con 48 h de antelación.
  - YONGO Sports Water por €1,90.
  - Asesoramiento personalizado con IA.

🧘 **Clases Grupales**:
- **Tipos**: Yoga, Spinning, Pilates, HIIT, Zumba, Body Pump.
- **Horarios**:  
  - Mañanas: 7:00 - 12:00  
  - Tardes: 16:00 - 21:00  
  - **Disponibilidad según el día.**
- **Reserva**: Obligatoria con 24-48 horas de antelación (según plan).

Reglas adicionales:
- No inventes información.
- Si no tienes la respuesta, sugiere visitar la web o contactar con recepción.
- Siempre prioriza la experiencia del usuario.";

$userPrompt = $userMessage;

try {
    logMessage("Preparando solicitud a la API de Gemini");
    $apiKey = "AIzaSyDvQiMvT4zZ9BUsSSQEWdwxChotB0_o99A";
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro:generateContent?key=" . $apiKey;

    $postData = [
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => $systemPrompt]]],
            ['role' => 'model', 'parts' => [['text' => "Entendido, soy $botName, asistente virtual de EnerGym. Estoy listo para ayudar."]]],
            ['role' => 'user', 'parts' => [['text' => $userPrompt]]]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 1024
        ]
    ];

    $jsonPostData = json_encode($postData);
    logMessage("Datos enviados a Gemini: " . $jsonPostData);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    if (curl_errno($ch)) throw new Exception("Error de cURL: " . curl_error($ch));

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode != 200) throw new Exception("Error de API (código $httpCode): " . $response);

    curl_close($ch);

    $responseData = json_decode($response, true);
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $botResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
    } else {
        throw new Exception("Formato de respuesta inesperado.");
    }

    // Asegurar que SIEMPRE mencione su identidad
    if (strpos(strtolower($botResponse), "soy") === false) {
        logMessage("Corrigiendo respuesta: agregando identificación del bot");
        $botResponse = "Soy $botName, el asistente virtual de EnerGym. " . ucfirst($botResponse);
    }

} catch(Exception $e) {
    logMessage("Excepción capturada: " . $e->getMessage());
    $botResponse = "Soy $botName, el asistente virtual de EnerGym. Actualmente tengo problemas técnicos. ¿Podrías intentarlo más tarde o contactar con recepción?";
}

logMessage("Enviando respuesta final: " . $botResponse);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['response' => $botResponse], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
logMessage("Solicitud completada");
?>
