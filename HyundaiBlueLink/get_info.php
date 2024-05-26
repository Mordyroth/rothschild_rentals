<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vin = $_POST["vin"];
    $command = escapeshellcmd("node get_info.js " . escapeshellarg($vin));
    $output = shell_exec($command);

    if ($output === null) {
        echo "Error executing command.";
        exit;
    }

    echo "<pre>Raw Output: " . htmlspecialchars($output) . "</pre>"; // Debugging line

    $output = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding JSON: " . json_last_error_msg();
        exit;
    }

    // Extract data from the response
    $location = $output['location'] ?? null;
    $odometer = $output['odometer'] ?? null;
    $fuelLevel = $output['fuelLevel'] ?? "N/A";

    // Display the data
    echo "<h2>Vehicle Information</h2>";
    if ($location) {
        echo "<strong>Latitude:</strong> " . htmlspecialchars($location['latitude']) . "<br/>";
        echo "<strong>Longitude:</strong> " . htmlspecialchars($location['longitude']) . "<br/>";
        echo "<strong>Altitude:</strong> " . htmlspecialchars($location['altitude']) . " meters<br/>";
        echo "<strong>Speed:</strong> " . htmlspecialchars($location['speed']['value']) . " " . ($location['speed']['unit'] == 1 ? 'km/h' : 'mph') . "<br/>";
        echo "<strong>Heading:</strong> " . htmlspecialchars($location['heading']) . "<br/>";
    }
    if ($odometer) {
        echo "<strong>Odometer:</strong> " . htmlspecialchars($odometer['value']) . " " . ($odometer['unit'] == 0 ? 'km' : 'miles') . "<br/>";
    }
    echo "<strong>Fuel Level:</strong> " . htmlspecialchars($fuelLevel) . "<br/>";

    // Calculate the distance to home
    if ($location) {
        $lat1 = $location['latitude'];
        $lon1 = $location['longitude'];
        $lat2 = 40.089745; // Home latitude
        $lon2 = -74.210797; // Home longitude
        $distance = calculateDistance($lat1, $lon1, $lat2, $lon2);

        if ($distance <= 0.1) {
            echo "<strong>Location:</strong> HOME<br/>";
        } else {
            echo "<strong>Location:</strong> AWAY<br/>";
        }
    }
}

function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'mi') {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $dist = $dist * 60 * 1.1515;

    if ($unit == 'km') {
        $dist = $dist * 1.609344;
    } elseif ($unit == 'nmi') {
        $dist = $dist * 0.8684;
    }

    return $dist;
}
?>
