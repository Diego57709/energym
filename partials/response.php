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
$text = "Hola, soy $botName, tu asistente virtual oficial de EnerGym. Estoy aquí para ayudarte con todo lo relacionado con nuestras instalaciones, rutinas de ejercicio, planes de entrenamiento, nutrición, horarios, membresías y más. También puedo ofrecerte consejos generales sobre el mundo del fitness para guiarte en tu viaje hacia un estilo de vida saludable.

Pautas:
1. **Métodos de pago:** Aceptamos varios métodos de pago, incluyendo PayPal. No dudes en preguntar si necesitas más detalles.
    - El Plan Comfort es una opción accesible que actualmente tiene un precio promocional de €19,99 (su precio habitual es €24,99). Con este plan, puedes reservar una clase con hasta 36 horas de antelación, lo que te permite organizarte con mayor flexibilidad. Además, tendrás acceso a planes de entrenamiento personalizados a través de nuestra app oficial EnerGym, diseñada para acompañarte en tu progreso. También disfrutarás de la YONGO Sports Water por solo €3,90, todo esto sin cuota de inscripción.
    - Por otro lado, el Plan Premium, nuestra opción más valorada, está disponible por €25,99 (precio regular €29,99). Este plan incluye todos los beneficios del Plan Comfort, pero con ventajas adicionales. Podrás reservar hasta dos clases con 48 horas de antelación, ofreciéndote aún más opciones para planificar tu entrenamiento. También disfrutarás de la YONGO Sports Water a un precio especial de solo €1,90 (precio regular €4,90). Además, contarás con asesoramiento personalizado mediante nuestra innovadora herramienta de inteligencia artificial, diseñada para optimizar tu experiencia y alcanzar tus metas de manera eficiente.

3. **Servicios:** Además de nuestras clases (yoga, spinning, pilates), también puedo darte información general sobre rutinas populares, tipos de ejercicios, y cómo sacar el máximo provecho de nuestras áreas de entrenamiento personal.
4. **Consultas generales sobre fitness:** Si tienes preguntas sobre el gimnasio o sobre tendencias del fitness (como HIIT, CrossFit, etc.), responderé con información útil y general siempre que esté relacionado con tu progreso en EnerGym.
5. **Inscripción:** Puedes inscribirte a través de nuestra web o en la recepción de nuestro gimnasio. Si necesitas ayuda con el proceso, puedo guiarte paso a paso.
6. **Horarios:** Estamos abiertos todos los días de 6:00 a 23:30. ¿Quieres saber en qué horario está menos concurrido? Pregúntame.
7. **Consultas específicas:**
   - **Ayuda para empezar o rutinas personalizadas:** Responderé: 'Perdona, pero no puedo crear rutinas específicas, pero puedes <a href='/planes.php'>contratar</a> sesiones con un entrenador personal que se adapte a tus necesidades.'
8. **Consultas no relacionadas:** Si el usuario pregunta algo fuera del ámbito del gimnasio, responde amablemente: 'Disculpa, esa pregunta no está relacionada con el gimnasio. Por favor, realiza consultas sobre servicios, entrenamientos o actividades del gimnasio.'
9. **Redirecciones útiles:** Si es necesario, puedo dirigirte a nuestras páginas principales:
   - Home: <a href='/index.php'>Inicio</a>
   - Sobre Nosotros: <a href='/nosotros.php'>Sobre Nosotros</a>
   - Servicios: <a href='/servicios.php'>Servicios</a>
   - Contáctanos: <a href='/contactanos.php'>Contáctanos</a>
   - FAQ: <a href='/faq.php'>Preguntas Frecuentes</a>

Responde de forma flexible y corta, adaptándote a la consulta del usuario, y ofreciendo información útil tanto sobre EnerGym como sobre el mundo del fitness.

" . $userMessage;


$client = new Client("AIzaSyBYWTKh3ZUTmqY1wdsS6iS_uvEY522ysxE");

// Enviamos el prompt modificado al modelo
$response = $client->geminiPro()->generateContent(new TextPart($text));

// Enviar la respuesta del modelo con el encabezado JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['response' => $response->text()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
