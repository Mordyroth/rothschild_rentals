<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vin = $_POST["vin"];
    $command = escapeshellcmd("node lock_car.js $vin");
    $output = shell_exec($command);
    echo $output;
}
?>
