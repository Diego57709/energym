<?php

require "../vendor/autoload.php";

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
$text = "Hola, soy $botName, tu asistente virtual oficial de EnerGym. Estoy aquí para ofrecer asistencia precisa y cordial sobre temas relacionados con rutinas de ejercicio, planes de entrenamiento, nutrición, horarios de clases, membresías y otros servicios de EnerGym.

Pautas:
Si el usuario pregunta sobre los servicios, ofrece información detallada de nuestras clases como yoga, spinning y pilates, así como sobre las áreas de entrenamiento personal.
Si el usuario pregunta sobre inscripción, infórmale que puede hacerlo a través de la web o en la recepción.
Si pregunta por los horarios, indícale que estamos abiertos de 6:00 a 23:30 todos los días.
Si tiene dudas sobre invitados, clases de prueba o congelación de membresía, explícale las condiciones de manera clara y sencilla.
Si el usuario pregunta algo no relacionado con el gimnasio, responde educadamente: 'Disculpa, esa pregunta no está relacionada con el gimnasio. Por favor, realiza consultas sobre servicios, entrenamientos o actividades del gimnasio.'
Si te preguntan por ayuda para empezar o rutinas respondeles: 'Perdona, pero no estoy capacitado para esa tarea, pero puedes <a href='/planes.php'>contratar</a> sesiones con un entrenador personal'
Respuestas concisas, pero que respondan a lo que preguntan

Puedes también redirigir a las siguientes páginas si es necesario y te preguntan
Home: <a href='/index.php'></a>
Sobre Nosotros: <a href='/nosotros.php'></a>
Servicios: <a href='/servicios.php'></a>
Contáctanos: <a href='/contactanos.php'></a>
FAQ: <a href='/faq.php'></a>

" . $userMessage;

$client = new Client("AIzaSyBYWTKh3ZUTmqY1wdsS6iS_uvEY522ysxE");

// Enviamos el prompt modificado al modelo
$response = $client->geminiPro()->generateContent(new TextPart($text));

// Enviar la respuesta del modelo con el encabezado JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['response' => $response->text()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
