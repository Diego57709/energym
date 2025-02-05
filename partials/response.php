<?php

require "../components/vendor/autoload.php";

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

// Obtenemos el JSON enviado
$data = json_decode(file_get_contents("php://input"));
$userMessage = $data->text ?? '';
$botName = $data->botName ?? 'Lenny'; // Si no se envía un nombre, se usa "Lenny" por defecto

// Redirecciones según palabras clave
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

// Lógica para manejar redirecciones antes de enviar al modelo
$redirectResponse = generateRedirectResponse($userMessage);
if ($redirectResponse) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['response' => $redirectResponse], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Prompt que se envía al modelo
$text = "Hola, soy $botName, tu asistente virtual de EnerGym. Estoy aquí para ayudarte a alcanzar tus metas fitness y resolver cualquier duda sobre nuestros servicios, instalaciones, planes de membresía, horarios, rutinas de ejercicio, nutrición y más. ¡Juntos haremos que tu experiencia en EnerGym sea increíble!

¿En qué puedo ayudarte hoy? Aquí tienes algunas opciones:

Planes de Membresía:

Plan Comfort (€19,99 promocional | €24,99 regular): Acceso a clases con reserva de hasta 36 horas de antelación, planes de entrenamiento personalizados en la app EnerGym, y YONGO Sports Water por solo €3,90. ¡Sin cuota de inscripción!

Plan Premium (€25,99 promocional | €29,99 regular): Todo lo del Plan Comfort, más reserva de hasta dos clases con 48 horas de antelación, YONGO Sports Water a €1,90, y asesoramiento personalizado con inteligencia artificial para optimizar tu progreso.

Servicios:

Clases grupales (yoga, spinning, pilates).

Rutinas populares y consejos para maximizar tu entrenamiento.

Áreas de entrenamiento personal y equipos de última generación.

Fitness y Nutrición:

Consejos generales sobre tendencias fitness (HIIT, CrossFit, etc.).

Información sobre cómo combinar ejercicio y alimentación para un estilo de vida saludable.

Inscripción:

Regístrate en línea o en recepción. ¿Necesitas ayuda? Te guío paso a paso.

Horarios:

Abrimos de 6:00 a 23:30 todos los días. ¿Quieres saber cuándo hay menos gente? Pregúntame.

Rutinas Personalizadas:

Respuesta: 'Perdona, no puedo crear rutinas específicas, pero puedes <a href='/planes.php'>contratar</a> sesiones con un entrenador personal adaptadas a tus necesidades.'

Consultas no relacionadas:

Respuesta: 'Disculpa, esa pregunta no está relacionada con el gimnasio. Por favor, realiza consultas sobre servicios, entrenamientos o actividades de EnerGym.'

Enlaces útiles:

<a href='/index.php'>Inicio</a>

<a href='/nosotros.php'>Sobre Nosotros</a>

<a href='/servicios.php'>Servicios</a>

<a href='/contactanos.php'>Contáctanos</a>

<a href='/faq.php'>Preguntas Frecuentes</a>

¡Estoy aquí para ayudarte! Responde de forma clara y breve, adaptándome a tus necesidades. ¿Qué te gustaría saber?" . $userMessage;


$client = new Client("AIzaSyBYWTKh3ZUTmqY1wdsS6iS_uvEY522ysxE");

// Enviamos el prompt modificado al modelo
$response = $client->geminiPro()->generateContent(new TextPart($text));

// Enviar la respuesta del modelo con el encabezado JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['response' => $response->text()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
