<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vin = $_POST["vin"];
    $command = escapeshellcmd("node lock_car.js $vin");
    $output = shell_exec($command);
    echo $output;
    $jsonString = preg_replace('/(\w+):/', '"$1":', $output);

// Decode the JSON string into a PHP object
$data = json_decode($jsonString);

// Access the properties
$latitude = $data->latitude;
$longitude = $data->longitude;
$altitude = $data->altitude;
$speedUnit = $data->speed->unit;
$speedValue = $data->speed->value;
$heading = $data->heading;

// Print the values
echo "Latitude: $latitude\n";
echo "Longitude: $longitude\n";
echo "Altitude: $altitude\n";
echo "Speed Unit: $speedUnit\n";
echo "Speed Value: $speedValue\n";
echo "Heading: $heading\n";
}
?>
