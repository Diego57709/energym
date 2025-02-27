<?php
// update_scan.php

header("Content-Type: application/json; charset=utf-8");

$scanFile = "scan_status.txt";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents($scanFile, "checked");
    echo json_encode(["status" => "checked"]);
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>
