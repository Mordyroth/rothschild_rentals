<?php
$vin = $_GET['vin'];
$command = "node HyundaiBlueLink/status.js " . escapeshellarg($vin);
exec($command, $output);
echo $output[0];
?>
