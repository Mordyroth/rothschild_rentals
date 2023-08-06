<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.tessie.com/vehicles?only_active=false",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    "accept: application/json",
    "authorization: Bearer KGFg3LgJLXIHwHLDdyxb3oJk8e4woOCX"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  //echo $response;
	$results = json_decode($response, true)['results'];
	$i = 0;
	foreach ($results as $result) {
		$lat1 =  $result['last_state']['drive_state']['latitude'];
		$lon1 = $result['last_state']['drive_state']['longitude'];
		$lat2 = "40.089745";
		$lon2 = "-74.210797";
		$distance = distance($lat1, $lon1, $lat2, $lon2);
		

		//echo $result['last_state']['display_name'] . "</br>";
		$name = $result['last_state']['display_name'];
		$response1[$i]['name'] = $name;
		if ($distance <= 0.1) {
			$response1[$i]["location"] = "home";
		    //echo "HOME <br/>";
		} else {
			$response1[$i]["location"] = "away";
		    //echo "AWAY <br/>";
		}
		$response1[$i]['id'] = $result['vin'];
		$response1[$i]['battery'] = $result['last_state']['charge_state']['battery_level'];
		$response1[$i]['charging_state'] = $result['last_state']['charge_state']['charging_state'];
		$response1[$i]['time_to_full'] = $result['last_state']['charge_state']['time_to_full_charge'];
		$response1[$i]['amps'] = $result['last_state']['charge_state']['charge_amps'];
		$response1[$i]['kw'] = $result['last_state']['charge_state']['charger_power'];
		$response1[$i]['locked'] = $result['last_state']['vehicle_state']['locked'];

		//echo $result['last_state']['drive_state']['latitude'] . ", " . $result['last_state']['drive_state']['longitude'];
		//echo "Distance: " . $distance . "<br/>";
		//echo "Battery: " . $result['last_state']['charge_state']['battery_level'] . "</br>";
		//echo "Charging State: " . $result['last_state']['charge_state']['charging_state'] . "</br>";
		//echo "amps: " . $result['last_state']['charge_state']['charge_amps'] . "</br>";
		//echo "kw: " . $result['last_state']['charge_state']['charger_power'] . "</br>";
		//echo "Time to full charge: " . $result['last_state']['charge_state']['time_to_full_charge'] . "</br>";
		//echo "<br/>";
		$i++;
	}
echo json_encode($response1);
}

function distance($lat1, $lon1, $lat2, $lon2, $unit = 'mi') {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $dist = $dist * 60 * 1.1515;
    if ($unit == 'km') {
        $dist = $dist * 1.609344;
    } else if ($unit == 'nmi') {
        $dist = $dist * 0.8684;
    }
    return $dist;
}



?>