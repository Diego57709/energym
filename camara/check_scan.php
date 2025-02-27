<?php
// check_scan.php

header("Content-Type: application/json; charset=utf-8");

$scanFile = "scan_status.txt";

if (file_exists($scanFile)) {
    $status = trim(file_get_contents($scanFile));
    echo json_encode(["status" => $status]);
} else {
    echo json_encode(["status" => "none"]);
}
?>
