<?php
require "components/vendor/autoload.php";

use GeminiAPI\Client;

// Reemplaza "YOUR_API_KEY" con tu clave de API real
$client = new Client("AIzaSyDvQiMvT4zZ9BUsSSQEWdwxChotB0_o99A");

try {
    // Suponiendo que el SDK tenga un método listModels() que devuelva un array de modelos disponibles.
    $models = $client->listModels();
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($models, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $e->getMessage()]);
}
