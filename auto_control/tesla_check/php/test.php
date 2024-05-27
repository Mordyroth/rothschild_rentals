<?php
$location = array(40.089126, -74.210598);
$coord1 = array(40.0896, -74.21078);
$coord2 = array(40.08966, -74.21073);
$coord3 = array(40.08961, -74.2106);
$coord4 = array(40.08956, -74.21063);
$isInside = isLocationInsidePolygon($location, $coord1, $coord2, $coord3, $coord4);
if ($isInside) {
    echo "The location is inside the polygon.";
} else {
    echo "The location is not inside the polygon.";
}


function isLocationInsidePolygon($location, $coord1, $coord2, $coord3, $coord4) {
    // Create an array of the four coordinates
    $polygon = array($coord1, $coord2, $coord3, $coord4);

    // Check if the "location" is inside the polygon
    $inside = false;
    $x = $location[0];
    $y = $location[1];
    $j = count($polygon) - 1;
    for ($i = 0; $i < count($polygon); $i++) {
        if (($polygon[$i][1] < $y && $polygon[$j][1] >= $y || $polygon[$j][1] < $y && $polygon[$i][1] >= $y) &&
            ($polygon[$i][0] + ($y - $polygon[$i][1]) / ($polygon[$j][1] - $polygon[$i][1]) * ($polygon[$j][0] - $polygon[$i][0]) < $x)) {
            $inside = !$inside;
        }
        $j = $i;
    }
    return $inside;
}


?>