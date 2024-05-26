<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vin = $_POST["vin"];
    $command = escapeshellcmd("node status.js $vin");
    $output = shell_exec($command);
    echo $output;
}
