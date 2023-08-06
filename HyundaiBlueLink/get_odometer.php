<?php
// Get the VIN from the query string
$vin = $_GET['vin'];

// Define the command to run the status.js script with Node.js, passing the VIN
$command = "node HyundaiBlueLink/status.js " . escapeshellarg($vin);

// Execute the command and capture the output
exec($command, $output);

// Return the odometer reading (assuming it's the first line of the output)
echo $output[0];
?>
