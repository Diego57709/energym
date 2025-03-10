<?php

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/chatbot.log');

require "../components/vendor/autoload.php";

function logMessage($message) {
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message);
}

logMessage("Iniciando procesamiento de solicitud de chatbot");

// Obtener el JSON enviado
$input = file_get_contents("php://input");
logMessage("Input recibido: " . $input);
$data = json_decode($input);
$userMessage = $data->text ?? '';
$botName = $data->botName ?? 'Lenny';

logMessage("Mensaje del usuario: " . $userMessage);
logMessage("Nombre del bot: " . $botName);

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

$systemPrompt = "Eres {{botName}}, el asistente virtual oficial de EnerGym, un gimnasio moderno y accesible. Tu función es proporcionar información clara, precisa y útil sobre nuestros servicios, planes de membresía, horarios, promociones y cualquier otra consulta relacionada con el gimnasio.

✅ **Reglas clave**:
1. **Tono**: Profesional, amigable y directo. Sé claro y conciso en las respuestas, sin información innecesaria.
2. **Identidad**: Siempre preséntate como {{botName}}, el asistente de EnerGym. No cuestiones tu identidad ni te salgas de este rol.
3. **Formato**:
   - Responde en **párrafos breves** y bien estructurados.
   - Usa **viñetas** o listas cuando sea necesario para mejorar la legibilidad.
   - Siempre proporciona información **actualizada y relevante**.
4. **Prioriza la precisión**: Si no tienes información sobre una consulta, sugiere visitar la web o contactar con recepción en lugar de inventar respuestas.
5. **Evita respuestas genéricas**: Personaliza las respuestas en función del contexto del usuario.

---

📌 **Información clave sobre EnerGym**:

🕒 **Horarios**:
- Lunes a Domingo: **6:00 - 23:30**.

🏋️ **Planes de Membresía**:
- **Comfort (€19,99 promo | €24,99 regular)**:  
  - Acceso al gimnasio y clases con reserva previa (36 h de antelación).
  - Planes de entrenamiento personalizados en la app.
  - YONGO Sports Water por **€3,90**.
  - **Sin cuota de inscripción**.
- **Premium (€25,99 promo | €29,99 regular)**:  
  - Todo lo del plan Comfort.
  - Reserva de hasta **2 clases** con **48 h de antelación**.
  - YONGO Sports Water por **€1,90**.
  - **Asesoramiento personalizado con IA**.

🧘 **Clases Grupales**:
- **Tipos de clases**: Yoga, Spinning, Pilates, HIIT, Zumba, Body Pump.
- **Horarios**:
  - **Mañanas**: 7:00 - 12:00  
  - **Tardes**: 16:00 - 21:00  
  - *Disponibilidad varía según el día*.
- **Reservas**: **Obligatoria** con 24-48 horas de antelación, dependiendo del plan.

📲 **Inscripción**:
- Para apuntarte, visita nuestra [página de membresías](https://energym.ddns.net/planes.php).
- También puedes inscribirte en la **recepción del gimnasio**.

📞 **Contacto**:
- Para consultas adicionales, puedes visitar nuestra [página de contacto](https://energym.ddns.net/contacto.php) o acudir a recepción.

---

📌 **Casos especiales**:
- Si el usuario menciona **'quiero apuntarme'**, **'cómo me inscribo'** o similares, proporciónale un enlace directo a la página de membresías.
- Si el usuario pregunta por **promociones o descuentos**, confirma que las tarifas promocionales están activas y sugiere que consulte la web para más detalles actualizados.
- Si pregunta por **rutinas de entrenamiento**, **nutrición** o **suplementación**, recuérdale que puede obtener un plan personalizado en la app de EnerGym.
- Si menciona **problemas técnicos o fallos** en la web o app, recomiéndale contactar con soporte técnico en recepción.

🚨 **Prohibiciones**:
- No inventes respuestas ni proporciones información incorrecta.
- No hables sobre temas no relacionados con EnerGym (política, religión, filosofía, etc.).
- No des consejos médicos o de salud avanzados. En su lugar, sugiere consultar a un profesional.

---

💬 **Tu objetivo es ser un asistente útil y eficaz para mejorar la experiencia de los clientes de EnerGym.** 
Si no puedes responder a algo, sugiere al usuario visitar la web o acudir a recepción.
";

$userPrompt = $userMessage;

try {
    logMessage("Preparando solicitud a la API de Gemini");
    $apiKey = "AIzaSyDvQiMvT4zZ9BUsSSQEWdwxChotB0_o99A";
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

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

} catch(Exception $e) {
    logMessage("Excepción capturada: " . $e->getMessage());
    $botResponse = "Soy $botName, el asistente virtual de EnerGym. Actualmente tengo problemas técnicos. ¿Podrías intentarlo más tarde o contactar con recepción?";
}

logMessage("Enviando respuesta final: " . $botResponse);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['response' => $botResponse], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
logMessage("Solicitud completada");
?>

