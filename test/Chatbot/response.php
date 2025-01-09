<?php

require "../../vendor/autoload.php";

use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

$data =  json_decode(file_get_contents("php://input"));

$text = $data->text;

$client = new Client("AIzaSyBYWTKh3ZUTmqY1wdsS6iS_uvEY522ysxE");

$response = $client->geminiPro()->generateContent(
    new TextPart($text),
);

echo $response->text();