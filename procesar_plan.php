<?php
require 'partials/header2.view.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_principal = $_REQUEST['plan'];
    $extras = $_REQUEST['extras'];

    echo $plan_principal . "<br>";

    echo !empty($extras) ? implode("<br>", $extras) : "No has seleccionado ningún extra.";

}

?>
