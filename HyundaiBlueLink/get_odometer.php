<?php
$vin = $_GET['vin'];
$output = null;
exec("node status.js $vin", $output);
echo $output[0]; // Assuming the odometer reading is the first line of output
?>
