<?php
// get_log.php

$logFile = 'log_asistencias.txt';

header("Content-Type: application/json; charset=utf-8");

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!empty($lines)) {
        $lastLine = array_pop($lines);
        echo json_encode(["log" => $lastLine]);
    } else {
        echo json_encode(["error" => "No hay registros disponibles."]);
    }
} else {
    echo json_encode(["error" => "No hay registros disponibles."]);
}
?>
